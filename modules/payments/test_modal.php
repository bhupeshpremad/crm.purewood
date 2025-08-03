<?php
include_once '../../config/config.php';
header('Content-Type: application/json');
global $conn;

$payment_id = 4; // Test with payment ID 4

$response = ['success' => false, 'payment_details' => [], 'debug' => []];

try {
    // Get payment info
    $stmt_payment = $conn->prepare("SELECT * FROM payments WHERE id = ?");
    $stmt_payment->execute([$payment_id]);
    $payment_info = $stmt_payment->fetch(PDO::FETCH_ASSOC);
    
    $response['debug']['payment_info'] = $payment_info;
    
    if (!$payment_info) {
        throw new Exception('Payment not found');
    }
    
    $jci_number = $payment_info['jci_number'];
    
    // Get payment details
    $stmt = $conn->prepare("SELECT * FROM payment_details WHERE payment_id = ?");
    $stmt->execute([$payment_id]);
    $payment_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response['debug']['raw_payment_details'] = $payment_details;
    $response['debug']['payment_details_count'] = count($payment_details);
    
    if (empty($payment_details)) {
        $response['message'] = 'No payment details found for this payment ID';
        $response['success'] = true;
        echo json_encode($response);
        exit;
    }
    
    // Process each payment detail
    foreach ($payment_details as &$detail) {
        $detail['supplier_name'] = '';
        $detail['contracture_name'] = '';
        
        if ($detail['payment_category'] === 'Job Card') {
            $detail['contracture_name'] = 'Job Card Payment';
        } elseif ($detail['payment_category'] === 'Supplier') {
            $detail['supplier_name'] = 'Test Supplier';
        }
    }
    
    $response['success'] = true;
    $response['payment_details'] = $payment_details;
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>