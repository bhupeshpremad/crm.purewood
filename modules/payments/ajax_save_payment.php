<?php
include_once '../../config/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $pdo = $database->getConnection();

    $payment_id = isset($_POST['payment_id']) && $_POST['payment_id'] !== '' ? intval($_POST['payment_id']) : 0;

    $pon_number = htmlspecialchars(trim($_POST['pon_number'] ?? ''));
    $po_amt = filter_var($_POST['po_amt'] ?? 0, FILTER_VALIDATE_FLOAT);
    $son_number = htmlspecialchars(trim($_POST['son_number'] ?? ''));
    $soa_number = filter_var($_POST['soa_number'] ?? 0, FILTER_VALIDATE_FLOAT);
    $jc_number = htmlspecialchars(trim($_POST['jc_number'] ?? ''));
    $jc_amt = filter_var($_POST['jc_amt'] ?? 0, FILTER_VALIDATE_FLOAT);
    $supplier_name = htmlspecialchars(trim($_POST['supplier_name'] ?? ''));
    $invoice_number = htmlspecialchars(trim($_POST['invoice_number'] ?? ''));
    $invoice_amount = filter_var($_POST['invoice_amount'] ?? 0, FILTER_VALIDATE_FLOAT);
    $cheque_number = htmlspecialchars(trim($_POST['cheque_number'] ?? ''));
    $ptm_amount = filter_var($_POST['ptm_amount'] ?? 0, FILTER_VALIDATE_FLOAT);
    $pd_acc_number = htmlspecialchars(trim($_POST['pd_acc_number'] ?? ''));
    $payment_invoice_date = $_POST['payment_invoice_date'] ?? '';

    $items_json = $_POST['items'] ?? '[]';
    $items = json_decode($items_json, true);

    if (empty($son_number) || $soa_number === false || empty($jc_number) || $jc_amt === false || empty($invoice_number) || $invoice_amount === false || $ptm_amount === false || empty($pd_acc_number) || empty($payment_invoice_date)) {
        $response['message'] = 'Required fields are missing or invalid.';
        echo json_encode($response);
        exit;
    }

    try {
        $pdo->beginTransaction();

        if ($payment_id > 0) {
            $stmt = $pdo->prepare("UPDATE payments SET
                pon_number = ?, po_amt = ?, son_number = ?, soa_number = ?,
                jc_number = ?, jc_amt = ?, supplier_name = ?, invoice_number = ?,
                invoice_amount = ?, cheque_number = ?, ptm_amount = ?,
                pd_acc_number = ?, payment_invoice_date = ?
                WHERE id = ?");

            $stmt->execute([
                $pon_number, $po_amt, $son_number, $soa_number,
                $jc_number, $jc_amt, $supplier_name, $invoice_number,
                $invoice_amount, $cheque_number, $ptm_amount,
                $pd_acc_number, $payment_invoice_date, $payment_id
            ]);

            $stmt_delete_items = $pdo->prepare("DELETE FROM payment_items WHERE payment_id = ?");
            $stmt_delete_items->execute([$payment_id]);

        } else {
            $stmt = $pdo->prepare("INSERT INTO payments (
                pon_number, po_amt, son_number, soa_number,
                jc_number, jc_amt, supplier_name, invoice_number,
                invoice_amount, cheque_number, ptm_amount,
                pd_acc_number, payment_invoice_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $pon_number, $po_amt, $son_number, $soa_number,
                $jc_number, $jc_amt, $supplier_name, $invoice_number,
                $invoice_amount, $cheque_number, $ptm_amount,
                $pd_acc_number, $payment_invoice_date
            ]);
            $payment_id = $pdo->lastInsertId();
        }

        $stmt_items = $pdo->prepare("INSERT INTO payment_items (payment_id, item_name, item_quantity, item_price, item_amount) VALUES (?, ?, ?, ?, ?)");
        foreach ($items as $item) {
            $itemName = htmlspecialchars(trim($item['name'] ?? ''));
            $itemQuantity = filter_var($item['quantity'] ?? 0, FILTER_VALIDATE_INT);
            $itemPrice = filter_var($item['price'] ?? 0, FILTER_VALIDATE_FLOAT);
            $itemAmount = filter_var($item['amount'] ?? 0, FILTER_VALIDATE_FLOAT);

            if (empty($itemName) || $itemQuantity === false || $itemPrice === false || $itemAmount === false || $itemQuantity < 0 || $itemPrice < 0) {
                continue;
            }
            $stmt_items->execute([$payment_id, $itemName, $itemQuantity, $itemPrice, $itemAmount]);
        }

        $pdo->commit();

        $response['success'] = true;
        $response['message'] = $payment_id > 0 ? 'Payment updated successfully.' : 'Payment added successfully.';
        $response['payment_id'] = $payment_id;

    } catch (Exception $e) {
        $pdo->rollBack();
        $response['message'] = 'Error saving payment: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>