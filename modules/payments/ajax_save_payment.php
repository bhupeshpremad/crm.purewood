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
    
    // Debug: Log received data
    error_log("Received payments data: " . $data['payments']);
    error_log("Decoded payments count: " . count($payments));
    error_log("JCI Number: " . $jci_number);
    
    // Check if this is an update (payment_id exists)
    $payment_main_id = null;
    if (!empty($data['payment_id'])) {
        $payment_main_id = $data['payment_id'];
        // Update existing payment record
        $stmt_update = $conn->prepare("UPDATE payments SET updated_at = NOW() WHERE id = ?");
        $stmt_update->execute([$payment_main_id]);
    }

    // Insert single payment record for this JCI only if new
    if (!$payment_main_id) {
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
    }

    // Insert only new payment_details records (skip existing ones)
    foreach ($payments as $p) {
        // Skip if this payment already exists (has payment_date and cheque_number)
        if (!empty($p['payment_date']) && !empty($p['cheque_number'])) {
            continue; // Skip already processed payments
        }
        
        // Only process checked payments with required fields
        if (empty($p['cheque_type']) || empty($p['payment_date']) || empty($p['cheque_number'])) {
            error_log("Skipping incomplete payment: " . json_encode($p));
            continue; // Skip incomplete payments
        }
        
        error_log("Processing payment: " . json_encode($p));
        error_log("About to insert into payment_details with payment_id: $payment_main_id");
        
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
            $jc_number_value = $jci_number; // Use the main JCI number
        }
        
        $result = $stmt->execute([
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
        
        $inserted_id = $conn->lastInsertId();
        error_log("Payment detail inserted with ID: $inserted_id, Result: " . ($result ? 'SUCCESS' : 'FAILED'));
    }

    // Update JCI main table to mark payment as completed
    if ($jci_number) {
        $stmt_update_jci = $conn->prepare("UPDATE jci_main SET payment_completed = 1 WHERE jci_number = ?");
        $stmt_update_jci->execute([$jci_number]);
    }

    $conn->commit();
    
    // Log successful save
    error_log("Payment saved successfully. Payment ID: $payment_main_id, JCI: $jci_number");

    echo json_encode(['success' => true, 'message' => 'Payment saved successfully', 'payment_id' => $payment_main_id]);
} catch (Exception $e) {
    $conn->rollBack();
    error_log("Payment save failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to save payment: ' . $e->getMessage()]);
}
