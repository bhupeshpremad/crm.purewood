<?php
include_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');
global $conn;

try {
    // Fetch payments with basic info
    $stmt = $conn->prepare("SELECT 
        p.id,
        p.jci_number,
        p.po_number,
        p.sell_order_number,
        p.created_at,
        COUNT(pd.id) as payment_count,
        SUM(pd.amount) as total_amount
        FROM payments p
        LEFT JOIN payment_details pd ON p.id = pd.payment_id
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'payments' => $payments
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching payments: ' . $e->getMessage()
    ]);
}
?>