<?php
include_once __DIR__ . '/../../config/config.php';

$database = new Database();
$conn = $database->getConnection();

$uploadDir = ROOT_DIR_PATH . 'uploads/quotation/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$requiredFields = ['lead_id', 'quotation_date', 'quotation_number', 'customer_name', 'customer_email', 'customer_phone', 'delivery_term', 'terms_of_delivery'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Field $field is required."]);
        exit;
    }
}

// Function to handle product insertion with image upload and validation
function insertProducts($conn, $quotationId, $productsJson, $uploadDir) {
    $products = json_decode($productsJson, true);
    if (!is_array($products)) {
        return;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
    $maxFileSize = 5 * 1024 * 1024;

    $productStmt = $conn->prepare("INSERT INTO quotation_products (quotation_id, item_name, item_code, description, assembly, item_h, item_w, item_d, box_h, box_w, box_d, cbm, wood_type, no_of_packet, iron_gauge, mdf_finish, quantity, price_usd, comments, product_image_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
