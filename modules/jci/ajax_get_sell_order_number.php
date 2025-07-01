<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $conn;

    $po_id = $_POST['po_id'] ?? null;

    if (!$po_id) {
        echo json_encode(['success' => false, 'message' => 'PO ID is required']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT po_number FROM po_main WHERE id = ?");
        $stmt->execute([$po_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['po_number'])) {
            echo json_encode(['success' => true, 'sell_order_number' => htmlspecialchars($result['po_number'])]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sell order number not found (PO number not found for selected PO ID).']);
        }
    } catch (PDOException $e) {
        error_log("Database error in ajax_get_sell_order_number: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
    } catch (Exception $e) {
        error_log("Error in ajax_get_sell_order_number: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}