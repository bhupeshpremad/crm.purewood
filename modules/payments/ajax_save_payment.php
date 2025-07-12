<?php
include_once __DIR__ . '/../../config/config.php';
header('Content-Type: application/json');
global $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = $_POST;

$conn->beginTransaction();

try {
    // Save multiple payments, one per payment in payments array
    $payments = json_decode($data['payments'] ?? '[]', true);
    $payment_ids = [];
    $jci_number = $data['jci_number'] ?? null;
    
    // Check for existing payments for this JCI
    if ($jci_number) {
        $stmt_check = $conn->prepare("SELECT id FROM payments WHERE jci_number = ?");
        $stmt_check->execute([$jci_number]);
        $existing_payments = $stmt_check->fetchAll(PDO::FETCH_COLUMN);
        
        // Delete existing payments for this JCI to avoid duplicates
        if (!empty($existing_payments)) {
            $inQuery = implode(',', array_fill(0, count($existing_payments), '?'));
            $stmt_del_details = $conn->prepare("DELETE FROM payment_details WHERE payment_id IN ($inQuery)");
            $stmt_del_details->execute($existing_payments);
            
            $stmt_del_payments = $conn->prepare("DELETE FROM payments WHERE jci_number = ?");
            $stmt_del_payments->execute([$jci_number]);
        }
    }

        // Insert single payment record for this JCI
    $stmt = $conn->prepare("INSERT INTO payments (
        jci_number,
        po_number,
        sell_order_number,
        created_at,
        updated_at
    ) VALUES (
        :jci_number,
        :po_number,
        :sell_order_number,
        NOW(),
        NOW()
    )");
    $stmt->execute([
        ':jci_number' => $jci_number,
        ':po_number' => $data['pon_number'],
        ':sell_order_number' => $data['sell_order_number']
    ]);
    $payment_main_id = $conn->lastInsertId();

    // Insert payment_details records
    foreach ($payments as $p) {
        $stmt = $conn->prepare("INSERT INTO payment_details (
            payment_id,
            jc_number,
            payment_type,
            cheque_number,
            pd_acc_number,
            ptm_amount,
            payment_invoice_date,
            payment_date,
            payment_category,
            amount
        ) VALUES (
            :payment_id,
            :jc_number,
            :payment_type,
            :cheque_number,
            :pd_acc_number,
            :ptm_amount,
            :payment_invoice_date,
            :payment_date,
            :payment_category,
            :amount
        )");
        // Get proper jc_number for job cards
        $jc_number_value = '';
        if ($p['entity_type'] === 'job_card') {
            // Extract JCI number from payee text
            $payee_text = $p['payee'] ?? '';
            if (preg_match('/Job Card: ([A-Z0-9-]+)/', $payee_text, $matches)) {
                $jc_number_value = $matches[1];
            }
        }
        
        $stmt->execute([
            ':payment_id' => $payment_main_id,
            ':jc_number' => $jc_number_value,
            ':payment_type' => $p['cheque_type'] ?? null,
            ':cheque_number' => $p['cheque_number'] ?? null,
            ':pd_acc_number' => $p['pd_acc_number'] ?? null,
            ':ptm_amount' => $p['ptm_amount'] ?? 0,
            ':payment_invoice_date' => $p['invoice_date'] ?? null,
            ':payment_date' => $p['payment_date'] ?? null,
            ':payment_category' => $p['entity_type'] === 'job_card' ? 'Job Card' : 'Supplier',
            ':amount' => $p['ptm_amount'] ?? 0
        ]);
    }

    // Update JCI main table to mark payment as completed
    if ($jci_number) {
        $stmt_update_jci = $conn->prepare("UPDATE jci_main SET payment_completed = 1 WHERE jci_number = ?");
        $stmt_update_jci->execute([$jci_number]);
    }

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Payment saved successfully', 'payment_id' => $payment_main_id]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Failed to save payment: ' . $e->getMessage()]);
}
