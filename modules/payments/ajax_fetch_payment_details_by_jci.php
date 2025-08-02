<?php
include_once '../../config/config.php';
header('Content-Type: application/json');
global $conn;

$response = ['success' => false, 'payment_details' => []];

if (isset($_GET['payment_id'])) {
    $payment_id = $_GET['payment_id'];
    
    try {
        // Fetch payment details from payment_details table, including jc_number for Job Card payments
        $stmt = $conn->prepare(
            "SELECT 
                id, 
                payment_category, 
                payment_type, 
                cheque_number, 
                pd_acc_number, 
                ptm_amount, 
                payment_date, 
                payment_invoice_date, 
                invoice_number, 
                payment_id,
                jc_number -- include jc_number for Job Card
            FROM payment_details 
            WHERE payment_id = ?"
        );
        $stmt->execute([$payment_id]);
        $payment_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add payee and entity_id fields for frontend matching and readonly logic
        foreach ($payment_details as &$detail) {
            $detail['payee'] = '';
            // Use payment_details.id as entity_id for uniqueness in frontend
            $detail['entity_id'] = $detail['id'];

            if ($detail['payment_category'] === 'Job Card') {
                $detail['payee'] = 'Job Card: ' . ($detail['jc_number'] ?? '');
            } elseif ($detail['payment_category'] === 'Supplier') {
                $detail['payee'] = 'Supplier Payment';
            }
        }
        unset($detail); // break reference

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