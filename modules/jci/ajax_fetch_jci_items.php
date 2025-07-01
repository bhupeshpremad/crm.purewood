<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$jci_id = (int)($_POST['jci_id'] ?? 0);

if ($jci_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Job Card ID provided.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT contracture_name, labour_cost, quantity, total_amount, delivery_date FROM jci_items WHERE jci_id = ?");
    $stmt->execute([$jci_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'items' => $items]);
    exit;
} catch (Exception $e) {
    error_log("Error fetching JCI items: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred while fetching items. Please try again later.']);
    exit;
}