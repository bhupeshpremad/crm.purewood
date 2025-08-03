<?php
include_once '../../config/config.php';
header('Content-Type: application/json');
global $conn;

$response = ['success' => false, 'data' => []];

if (!$conn instanceof PDO) {
    $response['message'] = 'Database connection not established.';
    echo json_encode($response);
    exit;
}

if (isset($_GET['payment_id'])) {
    $payment_id = intval($_GET['payment_id']);

    try {
        // Fetch payment general info
        $stmt_payment = $conn->prepare("SELECT * FROM payments WHERE id = ?");
        $stmt_payment->execute([$payment_id]);
        $payment = $stmt_payment->fetch(PDO::FETCH_ASSOC);

        if (!$payment) {
            $response['message'] = 'Payment not found.';
            echo json_encode($response);
            exit;
        }

        // Fetch payment details with enhanced information
        $stmt_payment_details = $conn->prepare("
            SELECT 
                pd.id,
                pd.payment_category, 
                pd.payment_type, 
                pd.cheque_number, 
                pd.pd_acc_number, 
                pd.ptm_amount, 
                pd.payment_invoice_date,
                pd.payment_date,
                pd.jc_number,
                p.jci_number
            FROM payment_details pd
            JOIN payments p ON pd.payment_id = p.id
            WHERE pd.payment_id = ?
        ");
        $stmt_payment_details->execute([$payment_id]);
        $payment_details = $stmt_payment_details->fetchAll(PDO::FETCH_ASSOC);

        // Get supplier names for supplier payments
        foreach ($payment_details as &$detail) {
            if ($detail['payment_category'] === 'Supplier') {
                // Try to get supplier name from purchase_items based on JCI number and cheque number
                $stmt_supplier = $conn->prepare("
                    SELECT DISTINCT pi.supplier_name 
                    FROM purchase_items pi 
                    JOIN purchase_main pm ON pi.purchase_main_id = pm.id 
                    WHERE pm.jci_number = ? AND pi.cheque_number = ?
                    LIMIT 1
                ");
                $stmt_supplier->execute([$detail['jci_number'], $detail['cheque_number']]);
                $supplier_result = $stmt_supplier->fetch(PDO::FETCH_ASSOC);
                $detail['supplier_name'] = $supplier_result['supplier_name'] ?? 'Unknown Supplier';
            } elseif ($detail['payment_category'] === 'Job Card') {
                // Get contractor name for job card payments
                $stmt_contractor = $conn->prepare("
                    SELECT DISTINCT ji.contracture_name 
                    FROM jci_items ji 
                    JOIN jci_main jm ON ji.jci_id = jm.id 
                    WHERE jm.jci_number = ?
                    LIMIT 1
                ");
                $stmt_contractor->execute([$detail['jci_number']]);
                $contractor_result = $stmt_contractor->fetch(PDO::FETCH_ASSOC);
                $detail['contracture_name'] = $contractor_result['contracture_name'] ?? 'Job Card Payment';
            }
        }
        unset($detail);

        $response['success'] = true;
        $response['data'] = [
            'payment' => $payment,
            'payment_details' => $payment_details
        ];
    } catch (Exception $e) {
        $response['message'] = 'Error fetching payment details: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Payment ID not provided.';
}

echo json_encode($response);
?>