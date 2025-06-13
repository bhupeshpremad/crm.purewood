<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (empty($_GET['quotation_id'])) {
    echo json_encode(['success' => false, 'message' => 'Quotation ID is required']);
    exit;
}

$quotationId = intval($_GET['quotation_id']);

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("SELECT item_name, item_code, description, assembly, item_h, item_w, item_d, box_h, box_w, box_d, cbm, wood_type, no_of_packet, iron_gauge, mdf_finish, quantity, price_usd, comments, product_image_name FROM quotation_products WHERE quotation_id = ?");
    $stmt->execute([$quotationId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'products' => $products]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching products: ' . $e->getMessage()]);
}
?>
