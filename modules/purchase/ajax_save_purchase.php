<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
    exit;
}

$po_number = $data['po_number'] ?? null;
$jci_number = $data['jci_number'] ?? null;
$sell_order_number = $data['sell_order_number'] ?? null;
$bom_number = $data['bom_number'] ?? null;
$items = $data['items'] ?? [];

if (!$po_number || !$jci_number || !$sell_order_number || !$bom_number || empty($items)) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

global $conn;

try {
    // Start transaction
    $conn->beginTransaction();

    // Check if purchase already exists for this JCI
    $stmt_check = $conn->prepare("SELECT id FROM purchase_main WHERE jci_number = ?");
    $stmt_check->execute([$jci_number]);
    $existing_purchase = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_purchase) {
        // Update existing purchase
        $purchase_main_id = $existing_purchase['id'];
        $stmt_update = $conn->prepare("UPDATE purchase_main SET po_number = ?, sell_order_number = ?, bom_number = ?, updated_at = NOW() WHERE id = ?");
        $stmt_update->execute([$po_number, $sell_order_number, $bom_number, $purchase_main_id]);
        
        // Delete only non-approved items to preserve approved ones
        $stmt_delete = $conn->prepare("DELETE FROM purchase_items WHERE purchase_main_id = ? AND (invoice_number IS NULL OR invoice_number = '')");
        $stmt_delete->execute([$purchase_main_id]);
    } else {
        // Insert new purchase_main
        $stmt_main = $conn->prepare("INSERT INTO purchase_main (po_number, jci_number, sell_order_number, bom_number, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt_main->execute([$po_number, $jci_number, $sell_order_number, $bom_number]);
        $purchase_main_id = $conn->lastInsertId();
    }

    // Aggregate items by supplier_name, product_type, product_name, job_card_number
    $aggregated_items = [];
    foreach ($items as $item) {
        $key = md5(
            ($item['supplier_name'] ?? '') . '|' .
            ($item['product_type'] ?? '') . '|' .
            ($item['product_name'] ?? '') . '|' .
            ($item['job_card_number'] ?? '')
        );
        if (!isset($aggregated_items[$key])) {
            $aggregated_items[$key] = [
                'supplier_name' => $item['supplier_name'] ?? '',
                'product_type' => $item['product_type'] ?? '',
                'product_name' => $item['product_name'] ?? '',
                'job_card_number' => $item['job_card_number'] ?? '',
                'assigned_quantity' => 0,
                'price' => floatval($item['price'] ?? 0)
            ];
        }
        $aggregated_items[$key]['assigned_quantity'] += floatval($item['assigned_quantity'] ?? 0);
    }

    // Insert/Update aggregated purchase_items
    $stmt_check = $conn->prepare("SELECT id, invoice_number FROM purchase_items WHERE purchase_main_id = ? AND supplier_name = ? AND product_type = ? AND product_name = ? AND job_card_number = ?");
    $stmt_insert = $conn->prepare("INSERT INTO purchase_items (purchase_main_id, supplier_name, product_type, product_name, job_card_number, assigned_quantity, price, total, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt_update = $conn->prepare("UPDATE purchase_items SET assigned_quantity = ?, price = ?, total = ?, updated_at = NOW() WHERE id = ? AND (invoice_number IS NULL OR invoice_number = '')");

    foreach ($aggregated_items as $item) {
        // Recalculate total as assigned_quantity * price to ensure correctness
        $total = floatval($item['assigned_quantity']) * floatval($item['price']);

        // Basic validation
        if (empty($item['product_type']) || empty($item['product_name'])) {
            throw new Exception("Product type and product name are required for all items");
        }

        // Skip items with zero assigned quantity
        if ($item['assigned_quantity'] <= 0) {
            continue;
        }
        
        // Check if item already exists
        $stmt_check->execute([
            $purchase_main_id,
            $item['supplier_name'],
            $item['product_type'],
            $item['product_name'],
            $item['job_card_number']
        ]);
        $existing = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Update only if not approved
            if (empty($existing['invoice_number'])) {
                $stmt_update->execute([
                    $item['assigned_quantity'],
                    $item['price'],
                    $total,
                    $existing['id']
                ]);
            }
        } else {
            // Insert new item only if it doesn't exist
            $stmt_insert->execute([
                $purchase_main_id,
                $item['supplier_name'],
                $item['product_type'],
                $item['product_name'],
                $item['job_card_number'],
                $item['assigned_quantity'],
                $item['price'],
                $total
            ]);
        }
        
        // Clean up duplicates after insert/update
        $cleanup_stmt = $conn->prepare("DELETE p1 FROM purchase_items p1 
                                       INNER JOIN purchase_items p2 
                                       WHERE p1.id > p2.id 
                                       AND p1.purchase_main_id = p2.purchase_main_id 
                                       AND p1.supplier_name = p2.supplier_name 
                                       AND p1.product_type = p2.product_type 
                                       AND p1.product_name = p2.product_name 
                                       AND p1.job_card_number = p2.job_card_number");
        $cleanup_stmt->execute();
    }

    // Update JCI main table to mark purchase as created
    $stmt_update_jci = $conn->prepare("UPDATE jci_main SET purchase_created = 1 WHERE jci_number = ?");
    $stmt_update_jci->execute([$jci_number]);

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Purchase saved successfully']);
    exit;

} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>
