<?php
include_once '../../config/config.php';

header('Content-Type: application/json');

$database = new Database();
$pdo = $database->getConnection();

$response = ['success' => false, 'data' => null];

if (isset($_GET['payment_id'])) {
    $payment_id = intval($_GET['payment_id']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ?");
        $stmt->execute([$payment_id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($payment) {
            $stmt = $pdo->prepare("SELECT item_name, item_quantity, item_price, item_amount FROM payment_items WHERE payment_id = ?");
            $stmt->execute([$payment_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $payment['items'] = $items;

            $response['success'] = true;
            $response['data'] = $payment;
        } else {
            $response['message'] = 'Payment not found.';
        }
    } catch (Exception $e) {
        $response['message'] = 'Error fetching payment data: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Payment ID not provided.';
}

echo json_encode($response);
?>