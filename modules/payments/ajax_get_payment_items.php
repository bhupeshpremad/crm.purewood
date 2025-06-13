<?php
include_once '../../config/config.php';

header('Content-Type: application/json');

$database = new Database();
$pdo = $database->getConnection();

$response = ['success' => false, 'items' => []];

if (isset($_GET['payment_id'])) {
    $payment_id = intval($_GET['payment_id']);

    try {
        $stmt = $pdo->prepare("SELECT item_name, item_quantity, item_price, item_amount FROM payment_items WHERE payment_id = ?");
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