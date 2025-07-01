<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? null;
$edit_mode = ($id !== null && $id !== '');

$bom_number = $_POST['bom_number'] ?? '';
$costing_sheet_number = $_POST['costing_sheet_number'] ?? '';
$client_name = $_POST['client_name'] ?? '';
$created_date = $_POST['created_date'] ?? '';
$prepared_by = $_POST['prepared_by'] ?? '';

$serial_numbers = $_POST['serial_number'] ?? [];
$item_names = $_POST['item_name'] ?? [];
$item_prices = $_POST['item_price'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$totals = $_POST['total'] ?? [];

if (!$bom_number || !$client_name || !$created_date || !$prepared_by || !$costing_sheet_number) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
    exit;
}

if (empty($item_names)) {
    echo json_encode(['success' => false, 'message' => 'At least one item detail is required.']);
    exit;
}

try {
    $conn->beginTransaction();

    if ($edit_mode) {
        $stmt = $conn->prepare("UPDATE bom_main SET bom_number = ?, costing_sheet_number = ?, client_name = ?, prepared_by = ?, order_date = ?, delivery_date = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$bom_number, $costing_sheet_number, $client_name, $prepared_by, $created_date, $created_date, $id]);
        $bom_id = $id;

        $stmtDeleteItems = $conn->prepare("DELETE FROM bom_items WHERE bom_id = ?");
        $stmtDeleteItems->execute([$bom_id]);
    } else {
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM bom_main WHERE bom_number = ?");
        $stmtCheck->execute([$bom_number]);
        if ($stmtCheck->fetchColumn() > 0) {
            throw new Exception('BOM number already exists. Please try again or use a new BOM number.');
        }

        $stmt = $conn->prepare("INSERT INTO bom_main (bom_number, costing_sheet_number, client_name, prepared_by, order_date, delivery_date, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$bom_number, $costing_sheet_number, $client_name, $prepared_by, $created_date, $created_date]);
        $bom_id = $conn->lastInsertId();
    }

    $stmtItem = $conn->prepare("INSERT INTO bom_items (bom_id, product_name, product_code, quantity, unit, price, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($item_names); $i++) {
        $product_name = trim($item_names[$i]);
        $product_code = trim($serial_numbers[$i]);
        $quantity = filter_var($quantities[$i], FILTER_VALIDATE_FLOAT);
        $price = filter_var($item_prices[$i], FILTER_VALIDATE_FLOAT);
        $total_amount = filter_var($totals[$i], FILTER_VALIDATE_FLOAT);
        $unit = '';

        if (empty($product_name) || empty($product_code) || $quantity === false || $quantity < 0 || $price === false || $price < 0 || $total_amount === false || $total_amount < 0) {
            throw new Exception("Invalid item data for row " . ($i + 1) . ". Please check all item fields.");
        }

        $stmtItem->execute([$bom_id, $product_name, $product_code, $quantity, $unit, $price, $total_amount]);
    }

    $conn->commit();

    // Re-generate new BOM number for the 'add' case after successful save
    if (!$edit_mode) {
        $new_bom_number_for_next_add = generateBOMNumber($conn);
        echo json_encode(['success' => true, 'message' => 'BOM saved successfully.', 'new_bom_number' => $new_bom_number_for_next_add]);
    } else {
        echo json_encode(['success' => true, 'message' => 'BOM updated successfully.']);
    }
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Error saving BOM: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error saving BOM: ' . $e->getMessage()]);
    exit;
}

function generateBOMNumber($conn) {
    $year = date('Y');
    $prefix = "BOM-$year-";
    $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING_INDEX(bom_number, '-', -1) AS UNSIGNED)) AS last_seq FROM bom_main WHERE bom_number LIKE ?");
    $stmt->execute(["BOM-$year-%"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_seq = (int)$result['last_seq'];
    $next_seq = $last_seq + 1;
    $seqFormatted = str_pad($next_seq, 4, '0', STR_PAD_LEFT);
    return $prefix . $seqFormatted;
}