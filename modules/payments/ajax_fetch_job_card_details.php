<?php
include_once '../../config/config.php';
header('Content-Type: application/json');
global $conn;

$response = ['success' => false, 'job_cards' => [], 'suppliers' => []];

if (isset($_GET['jci_number'])) {
    $jci_number = $_GET['jci_number'];

    try {
        // Fetch job card details from jci_main first
        $stmt_jci_main = $conn->prepare("SELECT id, jci_number, jci_type FROM jci_main WHERE jci_number = ?");
        $stmt_jci_main->execute([$jci_number]);
        $jci_main = $stmt_jci_main->fetch(PDO::FETCH_ASSOC);
        
        $job_cards = [];
        if ($jci_main) {
            // Try to get from jci_items first
            $stmt_jci = $conn->prepare("SELECT contracture_name, labour_cost, quantity, total_amount FROM jci_items WHERE jci_id = ?");
            $stmt_jci->execute([$jci_main['id']]);
            $job_card_items = $stmt_jci->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($job_card_items)) {
                foreach ($job_card_items as $item) {
                    $job_cards[] = [
                        'id' => $jci_main['id'],
                        'jci_number' => $jci_main['jci_number'],
                        'jci_type' => $jci_main['jci_type'],
                        'contracture_name' => $item['contracture_name'],
                        'labour_cost' => $item['labour_cost'],
                        'quantity' => $item['quantity'],
                        'total_amount' => $item['total_amount']
                    ];
                }
            } else {
                // Fallback: create default job card entry
                $job_cards[] = [
                    'id' => $jci_main['id'],
                    'jci_number' => $jci_main['jci_number'],
                    'jci_type' => $jci_main['jci_type'],
                    'contracture_name' => 'Default Contractor',
                    'labour_cost' => 0,
                    'quantity' => 1,
                    'total_amount' => 0
                ];
            }
        }

        // Fetch payment_id linked to this Job Card Number from payments table
        //$stmt_payment_id = $conn->prepare("SELECT id FROM payments WHERE jci_number = ? LIMIT 1");
        //$stmt_payment_id->execute([$jci_number]);
        //$payment_id = $stmt_payment_id->fetchColumn();

        $suppliers = [];

        // Fetch purchase_main id linked to po_id from jci_main
        $stmt_purchase_main = $conn->prepare("SELECT pm.id FROM purchase_main pm JOIN jci_main jm ON pm.jci_number = jm.jci_number WHERE jm.jci_number = ? LIMIT 1");
        $stmt_purchase_main->execute([$jci_number]);
        $purchase_main_id = $stmt_purchase_main->fetchColumn();

        if ($purchase_main_id) {
            $all_items = [];

            // Fetch ALL purchase_items for the purchase_main_id (including those without invoice details)
$stmt_purchase_items = $conn->prepare("SELECT id, supplier_name, product_type, product_name as item_name, assigned_quantity as quantity, price, total as item_amount, invoice_number, date as invoice_date, amount as invoice_amount FROM purchase_items WHERE purchase_main_id = ?");
            $stmt_purchase_items->execute([$purchase_main_id]);
            $purchase_items = $stmt_purchase_items->fetchAll(PDO::FETCH_ASSOC);
            $all_items = array_merge($all_items, $purchase_items);

            // Debug info
            $response['debug'] = [
                'purchase_main_id' => $purchase_main_id,
                'purchase_items_count' => count($purchase_items),
                'total_items_count' => count($all_items),
                'all_items' => $all_items
            ];

            // Group items by supplier_name
            $grouped_suppliers = [];
            foreach ($all_items as $item) {
                $supplier_name = $item['supplier_name'] ?? 'Unknown Supplier';
if (!isset($grouped_suppliers[$supplier_name])) {
    $grouped_suppliers[$supplier_name] = [
        'id' => md5($supplier_name . $item['invoice_number']), // unique supplier id based on supplier name + invoice
        'supplier_name' => $supplier_name,
        'invoice_number' => $item['invoice_number'] ?? null,
        'invoice_date' => $item['invoice_date'] ?? null,
        'invoice_amount' => floatval($item['invoice_amount'] ?? 0),
        'items' => []
    ];
}
$grouped_suppliers[$supplier_name]['items'][] = [
    'id' => $item['id'],
    'item_name' => $item['item_name'],
    'product_type' => $item['product_type'] ?? '',
    'item_quantity' => floatval($item['quantity']),
    'item_price' => floatval($item['price']),
    'item_amount' => floatval($item['quantity']) * floatval($item['price'])
];
                // Use the actual invoice_amount from purchase_items instead of calculating
                if (!$grouped_suppliers[$supplier_name]['invoice_amount']) {
                    $grouped_suppliers[$supplier_name]['invoice_amount'] = floatval($item['invoice_amount'] ?? 0);
                }
                
                // Update invoice details if not set
                if (!$grouped_suppliers[$supplier_name]['invoice_number'] && !empty($item['invoice_number'])) {
                    $grouped_suppliers[$supplier_name]['invoice_number'] = $item['invoice_number'];
                }
                if (!$grouped_suppliers[$supplier_name]['invoice_date'] && !empty($item['invoice_date'])) {
                    $grouped_suppliers[$supplier_name]['invoice_date'] = $item['invoice_date'];
                }
            }

            $suppliers = array_values($grouped_suppliers);
        }

        // Fetch PO Number and Sell Order Number for the Job Card Number
        $po_number = '';
        $sell_order_number = '';
        $stmt_po = $conn->prepare("SELECT po_number, sell_order_number FROM po_main WHERE id = (SELECT po_id FROM jci_main WHERE jci_number = ? LIMIT 1) LIMIT 1");
        $stmt_po->execute([$jci_number]);
        $po_data = $stmt_po->fetch(PDO::FETCH_ASSOC);
        if ($po_data) {
            $po_number = $po_data['po_number'];
            $sell_order_number = $po_data['sell_order_number'];
        }

        // Calculate po_amount as sum of total_amount from po_items for the PO linked to this job card
        $po_amount = 0;
        $stmt_po_amt = $conn->prepare("SELECT SUM(total_amount) as po_amount FROM po_items WHERE po_id = (SELECT po_id FROM jci_main WHERE jci_number = ? LIMIT 1)");
        $stmt_po_amt->execute([$jci_number]);
        $po_amt_result = $stmt_po_amt->fetch(PDO::FETCH_ASSOC);
        if ($po_amt_result && isset($po_amt_result['po_amount'])) {
            $po_amount = floatval($po_amt_result['po_amount']);
        }

        // For soa_number, set equal to po_amount as fallback
        $soa_number = $po_amount;

        // Fetch existing payments for this job card
$stmt_existing_payments = $conn->prepare("SELECT p.id as payment_id, p.jci_number, pd.jc_number, pd.payment_type, pd.cheque_number, pd.pd_acc_number, pd.ptm_amount, pd.payment_invoice_date, pd.payment_date, pd.payment_category, pd.amount
            FROM payments p
            LEFT JOIN payment_details pd ON p.id = pd.payment_id
            WHERE p.jci_number = ?");
$stmt_existing_payments->execute([$jci_number]);
$existing_payments = $stmt_existing_payments->fetchAll(PDO::FETCH_ASSOC);

        // Fetch invoice_number and invoice_date from purchase_items table for the job card
        $stmt_invoice = $conn->prepare("SELECT pi.invoice_number, pi.date as invoice_date FROM purchase_items pi JOIN purchase_main pm ON pi.purchase_main_id = pm.id WHERE pm.jci_number = ? LIMIT 1");
        $stmt_invoice->execute([$jci_number]);
        $invoice_data = $stmt_invoice->fetch(PDO::FETCH_ASSOC);
        $invoice_number = $invoice_data['invoice_number'] ?? null;
        $invoice_date = $invoice_data['invoice_date'] ?? null;

        $response['success'] = true;
        $response['job_cards'] = $job_cards;
        $response['suppliers'] = $suppliers;
        $response['po_number'] = $po_number;
        $response['sell_order_number'] = $sell_order_number;
        $response['po_amount'] = $po_amount;
        $response['soa_number'] = $soa_number;
        $response['existing_payments'] = $existing_payments;
        $response['invoice_number'] = $invoice_number;
        $response['invoice_date'] = $invoice_date;
    } catch (Exception $e) {
        $response['message'] = 'Error fetching job card details: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Job Card Number (jci_number) not provided.';
}

echo json_encode($response);
?>
