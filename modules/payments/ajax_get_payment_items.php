<?php

include_once '../../config/config.php';

header('Content-Type: application/json');

global $conn;

$response = ['success' => false, 'items' => []];

if (!$conn instanceof PDO) {
    $response['message'] = 'Database connection not established.';
    echo json_encode($response);
    exit;
}

if (isset($_GET['payment_id'])) {
    $payment_id = intval($_GET['payment_id']);

    try {
        $stmt = $conn->prepare("SELECT item_name, item_quantity, item_price, item_amount FROM payment_items WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response['success'] = true;
        $response['items'] = $items;
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Error fetching items: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Payment ID not provided.';
}

echo json_encode($response);
?>