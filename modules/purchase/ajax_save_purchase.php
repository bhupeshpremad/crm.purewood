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
        
        // Delete existing items to replace with new ones
        $stmt_delete = $conn->prepare("DELETE FROM purchase_items WHERE purchase_main_id = ?");
        $stmt_delete->execute([$purchase_main_id]);
    } else {
        // Insert new purchase_main
        $stmt_main = $conn->prepare("INSERT INTO purchase_main (po_number, jci_number, sell_order_number, bom_number, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt_main->execute([$po_number, $jci_number, $sell_order_number, $bom_number]);
        $purchase_main_id = $conn->lastInsertId();
    }

    // Insert purchase_items
    $stmt_item = $conn->prepare("INSERT INTO purchase_items (purchase_main_id, supplier_name, product_type, product_name, job_card_number, assigned_quantity, price, total, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    foreach ($items as $item) {
        $supplier_name = $item['supplier_name'] ?? '';
        $product_type = $item['product_type'] ?? '';
        $product_name = $item['product_name'] ?? '';
        $job_card_number = $item['job_card_number'] ?? '';
        $assigned_quantity = $item['assigned_quantity'] ?? 0;
        $price = $item['price'] ?? 0;
        $total = $item['total'] ?? 0;

        // Basic validation
        if (empty($product_type) || empty($product_name)) {
            throw new Exception("Product type and product name are required for all items");
        }

        // Convert to proper decimal format and validate
        $assigned_quantity = floatval($assigned_quantity);
        $price = floatval($price);
        $total = floatval($total);
        
        // Skip items with zero assigned quantity
        if ($assigned_quantity <= 0) {
            continue;
        }
        
        $stmt_item->execute([
            $purchase_main_id,
            $supplier_name,
            $product_type,
            $product_name,
            $job_card_number,
            $assigned_quantity,
            $price,
            $total
        ]);
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
