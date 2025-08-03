<?php
include_once '../../config/config.php';
header('Content-Type: application/json');
global $conn;

$response = ['success' => false, 'payments' => []];

try {
    // Fetch all payments with their details
    $stmt = $conn->prepare("
        SELECT 
            p.id,
            p.jci_number,
            p.po_number,
            p.sell_order_number,
            p.created_at,
            COUNT(pd.id) as payment_count,
            SUM(pd.ptm_amount) as total_amount
        FROM payments p
        LEFT JOIN payment_details pd ON p.id = pd.payment_id
        GROUP BY p.id, p.jci_number, p.po_number, p.sell_order_number, p.created_at
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['payments'] = $payments;
    
} catch (Exception $e) {
    $response['message'] = 'Error fetching payments: ' . $e->getMessage();
}

echo json_encode($response);
?>