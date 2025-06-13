<?php
    include_once '../../../config/config.php';
    include_once '../../../include/inc/header.php';
    include_once '../../sidebar.php';
?>

<div class="container-fluid">
    <?php include_once '../../../include/inc/topbar.php'; ?>

    <h1 class="h3 mb-4 text-gray-800">Payments List</h1>

    <?php
    $database = new Database();
    $pdo = $database->getConnection();

    try {
        $stmt = $pdo->query("SELECT id, pon_number, po_amt, son_number, invoice_number, invoice_amount, payment_invoice_date FROM payments ORDER BY id DESC");
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
                            <th>Invoice Number</th>
                            <th>Invoice Amount</th>
                            <th>Invoice Date</th>
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
                                    <td><?= htmlspecialchars($payment['invoice_number']) ?></td>
                                    <td><?= htmlspecialchars($payment['invoice_amount']) ?></td>
                                    <td><?= htmlspecialchars($payment['payment_invoice_date']) ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm view-items-btn" data-payment-id="<?= htmlspecialchars($payment['id']) ?>">View Items</button>
                                    </td>
                                    <td>
                                        <a href="add.php?payment_id=<?= urlencode($payment['id']) ?>" class="btn btn-primary btn-sm">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9" class="text-center">No payments found.</td></tr>
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
            url: '/php_erp/purewood/modules/payments/ajax_get_payment_items.php',
            type: 'GET',
            data: { payment_id: paymentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var items = response.items;
                    var totalAmount = 0;
                    if (items.length > 0) {
                        items.forEach(function(item) {
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
                        $('#itemDetailsTable tbody').append('<tr><td colspan="4" class="text-center">No items found for this payment.</td></tr>');
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
});
</script>