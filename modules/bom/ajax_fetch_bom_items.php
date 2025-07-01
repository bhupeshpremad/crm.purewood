<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$bom_id = (int)($_POST['bom_id'] ?? 0);

if ($bom_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid BOM ID provided.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT product_code, product_name, quantity, price, total_amount FROM bom_items WHERE bom_id = ?");
    $stmt->execute([$bom_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'items' => $items]);
    exit;
} catch (Exception $e) {
    error_log("Error fetching BOM items: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred while fetching items. Please try again later.']);
    exit;
}