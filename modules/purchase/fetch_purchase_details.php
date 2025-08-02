<?php
include_once __DIR__ . '/../../config/config.php';
if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', __DIR__ . '/../../' . DIRECTORY_SEPARATOR);
}
session_start();

if (!isset($_POST['purchase_id'])) {
    echo "<div class='alert alert-danger'>Purchase ID is required.</div>";
    exit;
}

$purchase_id = $_POST['purchase_id'];

global $conn;

try {
    // Fetch purchase main data
    $stmt_main = $conn->prepare("SELECT p.id, p.po_number, p.jci_number, p.sell_order_number, p.bom_number 
                                FROM purchase_main p 
                                WHERE p.id = ?");
    $stmt_main->execute([$purchase_id]);
    $purchase_main = $stmt_main->fetch(PDO::FETCH_ASSOC);

    if (!$purchase_main) {
        echo "<div class='alert alert-danger'>Purchase record not found.</div>";
        exit;
    }

    // Fetch all purchase items including image paths
    $stmt_items = $conn->prepare("SELECT id, supplier_name, product_type, product_name, job_card_number, assigned_quantity, price, total, invoice_number, builty_number, amount, date, invoice_image, builty_image 
                                 FROM purchase_items WHERE purchase_main_id = ? 
                                 ORDER BY id");
    $stmt_items->execute([$purchase_id]);
    $purchase_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
    


    // No array manipulation needed

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<div class="row">
    <div class="col-lg-6"><p><strong>PO Number:</strong> <?php echo htmlspecialchars($purchase_main['po_number']); ?></p></div>
    <div class="col-lg-6"><p><strong>JCI Number:</strong> <?php echo htmlspecialchars($purchase_main['jci_number']); ?></p></div>
    <div class="col-lg-6"><p><strong>Sell Order Number:</strong> <?php echo htmlspecialchars($purchase_main['sell_order_number']); ?></p></div>
    <div class="col-lg-6"><p><strong>BOM Number:</strong> <?php echo htmlspecialchars($purchase_main['bom_number']); ?></p></div>
</div>

<div class="table-responsive">
<table class="table table-bordered table-sm mt-3" style="font-size: 11px; white-space: nowrap;">
    <thead>
        <tr>
            <th>Supplier Name</th>
            <th>Product Type</th>
            <th>Product Name</th>
            <th>Job Card Number</th>
            <th>Assigned Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th>Invoice Number</th>
            <th>Invoice Image</th>
            <th>Invoice Amount</th>
            <th>Builty Number</th>
            <th>Builty Image</th>
            <th>Date</th>
            <th>Approve</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($purchase_items && count($purchase_items) > 0): ?>
            <?php 
            // Debug: Show what items we have
            echo "<!-- Debug: Found " . count($purchase_items) . " items -->";
            foreach ($purchase_items as $item): 
                echo "<!-- Item ID: {$item['id']}, Type: {$item['product_type']}, Name: {$item['product_name']} -->";
            ?>
                <tr id="item-row-<?php echo $item['id']; ?>">
                    <td><?php echo htmlspecialchars($item['supplier_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['product_type']); ?></td>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['job_card_number']); ?></td>
                    <td><?php echo htmlspecialchars($item['assigned_quantity']); ?></td>
                    <td><?php echo htmlspecialchars($item['price']); ?></td>
                    <td><?php echo htmlspecialchars($item['total']); ?></td>
                    <td class="invoice-number"><?php echo htmlspecialchars($item['invoice_number'] ?? ''); ?></td>
                    <td class="invoice-image">
                        <?php if (!empty($item['invoice_image'])): ?>
                            <a href="<?php echo BASE_URL; ?>modules/purchase/uploads/invoice/<?php echo htmlspecialchars($item['invoice_image']); ?>" class="btn btn-sm btn-info" target="_blank">Download Invoice</a>
                        <?php else: ?>
                            <span class="text-muted">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td class="invoice-amount"><?php echo !empty($item['amount']) ? htmlspecialchars(floatval($item['amount'])) : htmlspecialchars(floatval($item['total'])); ?></td>
                    <td class="builty-number"><?php echo htmlspecialchars($item['builty_number'] ?? ''); ?></td>
                    <td class="builty-image">
                        <?php if (!empty($item['builty_image'])): ?>
                            <a href="<?php echo BASE_URL; ?>modules/purchase/uploads/Builty/<?php echo htmlspecialchars($item['builty_image']); ?>" class="btn btn-sm btn-success" target="_blank">Download Builty</a>
                        <?php else: ?>
                            <span class="text-muted">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td class="item-date"><?php echo htmlspecialchars($item['date'] ?? ''); ?></td>
                    <td>
                        <?php if (!empty($item['invoice_number']) && !empty($item['builty_number'])): ?>
                            <span class="badge badge-success">Approved</span>
                        <?php else: ?>
                            <button class="btn btn-success btn-sm approve-btn" data-item-id="<?php echo htmlspecialchars($item['id']); ?>">Approve</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="14" class="text-center">No purchase items found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
<style>
#approvalModal .modal-backdrop {
    background-color: rgba(0, 0, 0, 0.8) !important;
}
#approvalModal.modal {
    z-index: 1060 !important;
}
#approvalModal .modal-backdrop {
    z-index: 1055 !important;
}
</style>
  <div class="modal-dialog" role="document">
    <form id="approvalForm" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="approvalModalLabel">Approve Job Card Item</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="approval_item_id" name="item_id" value="">
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="bulk_approval" name="bulk_approval">
                <label class="form-check-label" for="bulk_approval">
                  Apply same invoice/builty to multiple items
                </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="approval_date">Date</label>
                <input type="date" class="form-control" id="approval_date" name="date" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="approval_invoice_number">Invoice Number</label>
                <input type="text" class="form-control" id="approval_invoice_number" name="invoice_number" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="approval_amount">Amount</label>
                <input type="number" step="0.01" class="form-control" id="approval_amount" name="amount" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="approval_bill_image">Invoice Image (jpg/png)</label>
                <input type="file" class="form-control-file" id="approval_bill_image" name="bill_image" accept=".jpg,.jpeg,.png" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="approval_builty_number">Builty Number</label>
                <input type="text" class="form-control" id="approval_builty_number" name="builty_number" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="approval_builty_image">Builty Image (jpg/png)</label>
                <input type="file" class="form-control-file" id="approval_builty_image" name="builty_image" accept=".jpg,.jpeg,.png" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Approval</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function() {
    // Open approval modal on approve button click
    $('.approve-btn').click(function() {
        var itemId = $(this).data('item-id');
        $('#approval_item_id').val(itemId);
        $('#approvalForm')[0].reset();
        $('#approval_item_id').val(itemId); // Set again after reset
        $('#approvalModal').modal('show');
    });

    // Handle approval form submission
    $('#approvalForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'ajax_save_approval.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        toastr.error('Invalid server response');
                        return;
                    }
                }
                if (response.success) {
                    toastr.success(response.message);
                    $('#approvalModal').modal('hide');
                    
                    // Update the row with new data
                    var itemId = $('#approval_item_id').val();
                    var row = $('#item-row-' + itemId);
                    row.find('.invoice-number').text($('#approval_invoice_number').val());
                    row.find('.invoice-amount').text($('#approval_amount').val());
                    row.find('.builty-number').text($('#approval_builty_number').val());
                    row.find('.item-date').text($('#approval_date').val());
                    
                    // Change button to approved badge
                    row.find('.approve-btn').replaceWith('<span class="badge badge-success">Approved</span>');
                } else {
                    toastr.error(response.error || 'Failed to save approval');
                }
            },
            error: function() {
                toastr.error('Error saving approval');
            }
        });
    });
});
</script>
