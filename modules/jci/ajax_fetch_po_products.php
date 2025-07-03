<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$po_id = $_POST['po_id'] ?? null;

if (!$po_id) {
    echo json_encode(['success' => false, 'message' => 'PO ID is required']);
    exit;
}

try {
    // Corrected: Fetch product details directly from po_items table
    $stmt = $conn->prepare("
        SELECT id, product_name, item_code, quantity
        FROM po_items
        WHERE po_id = ?
    ");
    $stmt->execute([$po_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'products' => $products]);
    exit;
} catch (Exception $e) {
    error_log("Error fetching PO products: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred while fetching PO products.']);
    exit;
}