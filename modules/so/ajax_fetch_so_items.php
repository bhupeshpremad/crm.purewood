<?php

// Path to your config file
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

// Ensure database connection is established
if (!isset($conn) || !$conn instanceof PDO) {
    echo json_encode(['success' => false, 'message' => 'Database connection not initialized.']);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$po_id = (int)($_POST['po_id'] ?? 0); // Renamed to po_id as we're fetching from po_items

if ($po_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid PO ID provided.']);
    exit;
}

try {
    // Fetch items from po_items table using the po_id
    $stmt = $conn->prepare("SELECT product_code, product_name, item_code, quantity, unit, price, total_amount FROM po_items WHERE po_id = :po_id ORDER BY id ASC");
    $stmt->bindValue(':po_id', $po_id, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($items === false) {
         throw new Exception('PDO fetch operation failed.');
    }

    echo json_encode(['success' => true, 'items' => $items]);
    exit;

} catch (PDOException $e) {
    // Log the error for debugging, don't show raw error to user
    error_log("Database error fetching SO items (from PO items): " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred while fetching items. Please try again.']);
    exit;
} catch (Exception $e) {
    error_log("Error fetching SO items (from PO items): " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again later.']);
    exit;
}