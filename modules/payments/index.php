<?php
include_once __DIR__ . '/../../config/config.php';
if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', __DIR__ . '/../../' . DIRECTORY_SEPARATOR);
}
include_once ROOT_DIR_PATH . 'include/inc/header.php';
session_start();


$user_type = $_SESSION['user_type'] ?? 'guest';

if ($user_type === 'superadmin') {
    include_once ROOT_DIR_PATH . 'superadmin/sidebar.php';
} elseif ($user_type === 'salesadmin') {
    include_once ROOT_DIR_PATH . 'salesadmin/sidebar.php';
} elseif ($user_type === 'accounts') {
    include_once ROOT_DIR_PATH . 'accountsadmin/sidebar.php';
} else {
    
}

?>
<div class="container-fluid">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>
    <h1 class="h3 mb-4 text-gray-800">Payments List</h1>
    <?php
    global $conn;
    try {
        $stmt = $conn->query("
            SELECT 
                p.id, p.pon_number, p.po_amt, p.son_number, 
                GROUP_CONCAT(DISTINCT s.invoice_number SEPARATOR ', ') AS invoice_numbers,
                IFNULL(SUM(s.invoice_amount), 0) AS total_invoice_amount,
                MAX(pd.payment_invoice_date) AS latest_payment_invoice_date
            FROM payments p
            LEFT JOIN suppliers s ON s.payment_id = p.id
            LEFT JOIN payment_details pd ON pd.payment_id = p.id
            GROUP BY p.id
            ORDER BY p.id DESC
        ");
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $payments = [];
    }
    ?>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="paymentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>PO Number</th>
                            <th>PO Amount</th>
                            <th>SO Number</th>
                            <th>Invoice Numbers</th>
                            <th>Invoice Amount</th>
                            <th>Latest Payment Invoice Date</th>
                            <th>Job Card Details</th>
                            <th>Payment Details</th>
                            <th>Item Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($payments)): ?>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($payment['id']) ?></td>
                                    <td><?= htmlspecialchars($payment['pon_number']) ?></td>
                                    <td><?= htmlspecialchars($payment['po_amt']) ?></td>
                                    <td><?= htmlspecialchars($payment['son_number']) ?></td>
                                    <td><?= htmlspecialchars($payment['invoice_numbers']) ?></td>
                                    <td><?= htmlspecialchars($payment['total_invoice_amount']) ?></td>
                                    <td><?= htmlspecialchars($payment['latest_payment_invoice_date']) ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-jobcards-btn" data-payment-id="<?= htmlspecialchars($payment['id']) ?>">View Job Cards</button>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-payments-btn" data-payment-id="<?= htmlspecialchars($payment['id']) ?>">View Payments</button>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-items-btn" data-payment-id="<?= htmlspecialchars($payment['id']) ?>">View Items</button>
                                    </td>
                                    <td>
                                        <a href="add.php?payment_id=<?= urlencode($payment['id']) ?>" class="btn btn-primary btn-sm">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="11" class="text-center">No payments found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="itemDetailsModal" tabindex="-1" role="dialog" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="itemDetailsModalLabel">Payment Item Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="itemDetailsTable">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-right">Total Amount:</th>
              <th id="totalItemAmount"></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
    $('.view-items-btn').on('click', function() {
        var paymentId = $(this).data('payment-id');
        $('#itemDetailsTable tbody').empty();
        $('#totalItemAmount').text('');
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/payments/ajax_get_payment_details.php',
            type: 'GET',
            data: { payment_id: paymentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    var totalAmount = 0;
                    $('#itemDetailsTable tbody').empty();
                    if (data.suppliers && data.suppliers.length > 0) {
                        data.suppliers.forEach(function(supplier) {
                            var supplierHeader = '<tr><th colspan="4" class="text-center">Supplier: ' + supplier.supplier_name + '</th></tr>';
                            $('#itemDetailsTable tbody').append(supplierHeader);
                            if (supplier.items && supplier.items.length > 0) {
                                supplier.items.forEach(function(item) {
                                    var row = '<tr>' +
                                        '<td>' + item.item_name + '</td>' +
                                        '<td>' + item.item_quantity + '</td>' +
                                        '<td>' + item.item_price + '</td>' +
                                        '<td>' + item.item_amount + '</td>' +
                                        '</tr>';
                                    $('#itemDetailsTable tbody').append(row);
                                    totalAmount += parseFloat(item.item_amount);
                                });
                            } else {
                                $('#itemDetailsTable tbody').append('<tr><td colspan="4" class="text-center">No items found for this supplier.</td></tr>');
                            }
                        });
                    }
                    $('#totalItemAmount').text(totalAmount.toFixed(2));
                    $('#itemDetailsModal').modal('show');
                } else {
                    alert('Failed to load item details: ' + (response.message || 'Unknown error.'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error loading item details.');
                console.error("AJAX Error: ", status, error, xhr.responseText);
            }
        });
    });

    $('.view-jobcards-btn').on('click', function() {
        var paymentId = $(this).data('payment-id');
        $('#jobCardDetailsTable tbody').empty();
        $('#totalJobCardAmount').text('');
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/payments/ajax_get_payment_details.php',
            type: 'GET',
            data: { payment_id: paymentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    var totalAmount = 0;
                    $('#jobCardDetailsTable tbody').empty();
                    if (data.job_cards && data.job_cards.length > 0) {
                        data.job_cards.forEach(function(jobCard) {
                            var jcNumber = jobCard.jc_number.replace(/\+/g, '');
                            var jcAmt = jobCard.jc_amt.toString().replace(/\+/g, '');
                            var row = '<tr>' +
                                '<td>' + jcNumber + '</td>' +
                                '<td>' + jcAmt + '</td>' +
                                '</tr>';
                            $('#jobCardDetailsTable tbody').append(row);
                            totalAmount += parseFloat(jcAmt);
                        });
                    } else {
                        $('#jobCardDetailsTable tbody').append('<tr><td colspan="2" class="text-center">No job cards found.</td></tr>');
                    }
                    $('#totalJobCardAmount').text(totalAmount.toFixed(2));
                    $('#jobCardDetailsModal').modal('show');
                } else {
                    alert('Failed to load job card details: ' + (response.message || 'Unknown error.'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error loading job card details.');
                console.error("AJAX Error: ", status, error, xhr.responseText);
            }
        });
    });

    $('.view-payments-btn').on('click', function() {
        var paymentId = $(this).data('payment-id');
        $('#paymentDetailsTable tbody').empty();
        $('#totalPaymentAmount').text('');
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/payments/ajax_get_payment_details.php',
            type: 'GET',
            data: { payment_id: paymentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    var totalAmount = 0;
                    $('#paymentDetailsTable tbody').empty();
                    if (data.payments && data.payments.length > 0) {
                        data.payments.forEach(function(payment) {
                            var paymentCategory = payment.payment_category.replace(/\+/g, '');
                            var paymentType = payment.payment_type.replace(/\+/g, '');
                            var chequeNumber = payment.cheque_number.replace(/\+/g, '');
                            var pdAccNumber = payment.pd_acc_number.replace(/\+/g, '');
                            var paymentFullPartial = payment.payment_full_partial.replace(/\+/g, '');
                            var ptmAmount = payment.ptm_amount.toString().replace(/\+/g, '');
                            var paymentInvoiceDate = payment.payment_invoice_date.replace(/\+/g, '');

                            var row = '<tr>' +
                                '<td>' + paymentCategory + '</td>' +
                                '<td>' + paymentType + '</td>' +
                                '<td>' + chequeNumber + '</td>' +
                                '<td>' + pdAccNumber + '</td>' +
                                '<td>' + paymentFullPartial + '</td>' +
                                '<td>' + ptmAmount + '</td>' +
                                '<td>' + paymentInvoiceDate + '</td>' +
                                '</tr>';
                            $('#paymentDetailsTable tbody').append(row);
                            totalAmount += parseFloat(ptmAmount);
                        });
                    } else {
                        $('#paymentDetailsTable tbody').append('<tr><td colspan="7" class="text-center">No payment details found.</td></tr>');
                    }
                    $('#totalPaymentAmount').text(totalAmount.toFixed(2));
                    $('#paymentDetailsModal').modal('show');
                } else {
                    alert('Failed to load payment details: ' + (response.message || 'Unknown error.'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error loading payment details.');
                console.error("AJAX Error: ", status, error, xhr.responseText);
            }
        });
    });
});
</script>

<div class="modal fade" id="jobCardDetailsModal" tabindex="-1" role="dialog" aria-labelledby="jobCardDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="jobCardDetailsModalLabel">Job Card Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="jobCardDetailsTable">
          <thead>
            <tr>
              <th>Job Card No.</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th>Total Amount:</th>
              <th id="totalJobCardAmount"></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentDetailsModalLabel">Payment Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="paymentDetailsTable">
          <thead>
            <tr>
              <th>Payment Category</th>
              <th>Payment Type</th>
              <th>Cheque/RTGS Number</th>
              <th>PD ACC Number</th>
              <th>Full/Partial</th>
              <th>Amount</th>
              <th>Invoice Date</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="6" class="text-right">Total Amount:</th>
              <th id="totalPaymentAmount"></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>