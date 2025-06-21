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
}

?>
<div class="container-fluid">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <div class="row w-100">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <h6 class="m-0 font-weight-bold text-primary">Payments List</h6>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8">
                            <input type="text" id="searchPaymentInput" class="form-control form-control-sm" placeholder="Search by PO Number or SO Number">
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 text-right">
                            <a href="add.php" class="btn btn-primary btn-sm">Add New Payment</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
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
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="paymentsTable">
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
    <div class="mt-5">
        <?php include_once ROOT_DIR_PATH . 'include/inc/footer-top.php'; ?>
    </div>
</div>

<!-- Item Details Modal -->
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

<!-- Job Card Details Modal -->
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

<!-- Payment Details Modal -->
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

<!-- jQuery (First) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Popper.js (for Bootstrap 4 compatibility) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<!-- Bootstrap 4 JS (for modal) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Toastr JS (optional, if you want notifications) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
    // Item Details Modal
    $(document).on('click', '.view-items-btn', function() {
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

    // Job Card Details Modal
    $(document).on('click', '.view-jobcards-btn', function() {
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

    // Payment Details Modal
    $(document).on('click', '.view-payments-btn', function() {
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

    // Search input handler with debounce
    let searchTimeout = null;
    $('#searchPaymentInput').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '<?php echo BASE_URL; ?>modules/payments/ajax_get_payment_details.php',
                type: 'POST',
                data: { action: 'search_payments', search: query },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.payments) {
                        let rows = '';
                        if (response.payments.length > 0) {
                            response.payments.forEach(function(payment) {
                                rows += '<tr>';
                                rows += '<td>' + payment.id + '</td>';
                                rows += '<td>' + payment.pon_number + '</td>';
                                rows += '<td>' + payment.po_amt + '</td>';
                                rows += '<td>' + payment.son_number + '</td>';
                                rows += '<td>' + payment.invoice_numbers + '</td>';
                                rows += '<td>' + payment.total_invoice_amount + '</td>';
                                rows += '<td>' + payment.latest_payment_invoice_date + '</td>';
                                rows += '<td><button class="btn btn-info btn-sm view-jobcards-btn" data-payment-id="' + payment.id + '">View Job Cards</button></td>';
                                rows += '<td><button class="btn btn-info btn-sm view-payments-btn" data-payment-id="' + payment.id + '">View Payments</button></td>';
                                rows += '<td><button class="btn btn-info btn-sm view-items-btn" data-payment-id="' + payment.id + '">View Items</button></td>';
                                rows += '<td><a href="add.php?payment_id=' + encodeURIComponent(payment.id) + '" class="btn btn-primary btn-sm">Edit</a></td>';
                                rows += '</tr>';
                            });
                        } else {
                            rows = '<tr><td colspan="11" class="text-center">No payments found.</td></tr>';
                        }
                        $('#paymentsTable tbody').html(rows);
                    } else {
                        $('#paymentsTable tbody').html('<tr><td colspan="11" class="text-center text-danger">Error loading payments.</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#paymentsTable tbody').html('<tr><td colspan="11" class="text-center text-danger">Error loading payments.</td></tr>');
                }
            });
        }, 300); // debounce delay 300ms
    });
});
</script>

<?php include_once ROOT_DIR_PATH . 'include/inc/footer.php'; ?>