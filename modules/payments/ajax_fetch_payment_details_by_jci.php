<?php
include_once '../../config/config.php';
header('Content-Type: application/json');
global $conn;

$response = ['success' => false, 'payment_details' => []];

if (isset($_GET['payment_id'])) {
    $payment_id = $_GET['payment_id'];
    
    try {
        // Fetch payment details from payment_details table
        $stmt = $conn->prepare("SELECT payment_category, payment_type, cheque_number, pd_acc_number, ptm_amount, payment_date, payment_invoice_date FROM payment_details WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        $payment_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $response['success'] = true;
        $response['payment_details'] = $payment_details;
        
    } catch (Exception $e) {
        $response['message'] = 'Error fetching payment details: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Payment ID not provided.';
}

echo json_encode($response);
?>