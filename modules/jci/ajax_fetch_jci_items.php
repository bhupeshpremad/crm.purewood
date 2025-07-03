<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$jci_id = (int)($_POST['jci_id'] ?? 0); // This jci_id will now correspond to a single JOB-YEAR-JCN-X entry in jci_main

if ($jci_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Job Card ID provided.']);
    exit;
}

try {
    // Select all the new columns from jci_items, including contracture_name in its new logical position
    $stmt = $conn->prepare("SELECT
                                po_product_id,
                                product_name,
                                item_code,
                                original_po_quantity,
                                quantity, -- This is now the assigned quantity
                                labour_cost,
                                total_amount,
                                delivery_date,
                                job_card_date,
                                job_card_type,
                                contracture_name -- New field (moved)
                            FROM jci_items WHERE jci_id = ?");
    $stmt->execute([$jci_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'items' => $items]);
    exit;
} catch (Exception $e) {
    error_log("Error fetching JCI items: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred while fetching items. Please try again later.']);
    exit;
}