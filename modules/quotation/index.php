<?php
include_once __DIR__ . '/../../config/config.php';
if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', __DIR__ . '/../../' . DIRECTORY_SEPARATOR);
}
include_once ROOT_DIR_PATH . 'include/inc/header.php';
session_start();
$user_type = $_SESSION['user_type'] ?? 'guest';

if ($user_type === 'superadmin') {
    include_once __DIR__ . '/../../superadmin/sidebar.php';
} elseif ($user_type === 'salesadmin') {
    include_once __DIR__ . '/../../salesadmin/sidebar.php';
}

try {
    global $conn;
    $stmt = $conn->query("SELECT id, lead_id, quotation_number, customer_name, customer_email, customer_phone, delivery_term, terms_of_delivery, approve, locked FROM quotations ORDER BY id DESC");
    $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $quotations = [];
        $error = "Error fetching quotations: " . $e->getMessage();
    }
?> 

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Quotations</h1>
            </div>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <div class="table-responsive">
                <table id="quotationsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="bg-gradient-primary text-white text-center">Sr NO</th>
                            <th class="bg-gradient-primary text-white text-center">Quotation Number</th>
                            <th class="bg-gradient-primary text-white text-center">Customer Name</th>
                            <th class="bg-gradient-primary text-white text-center">Customer Email</th>
                            <th class="bg-gradient-primary text-white text-center">Status</th>
                            <th class="bg-gradient-primary text-white text-center">Export</th>
                            <th class="bg-gradient-primary text-white text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sr_no = 1; ?>
                        <?php foreach ($quotations as $quotation) : ?>
                            <tr class="text-center">
                                <td><?php echo $sr_no++; ?></td>
                                <td><?php echo htmlspecialchars($quotation['quotation_number']); ?></td>
                                <td><?php echo htmlspecialchars($quotation['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($quotation['customer_email']); ?></td>
                                <td>
                                    <button class="btn btn-primary viewStatusBtn" data-quotation-id="<?php echo $quotation['id']; ?>" title="View Status" style="color:#ffffff;">View Status</button>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm export-btn shareQuotationBtn mr-2" data-toggle="modal" data-target="#shareQuotationModal_<?php echo $quotation['id']; ?>" title="Share">
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                        <button type="button" class="btn btn-success btn-sm export-btn exportExcelBtn mr-2" data-id="<?php echo $quotation['id']; ?>" title="Export to Excel">
                                            <i class="fas fa-file-excel"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm export-btn exportPdfBtn" data-id="<?php echo $quotation['id']; ?>" title="Export to PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </div>
                                </td>
                                <td style="white-space: nowrap;">
                                    <button class="btn btn-primary editQuotationBtn" data-quotation-id="<?php echo $quotation['id']; ?>" title="<?php echo ($quotation['approve'] == 1) ? 'Editing disabled for approved quotation' : 'Edit'; ?>" style="color:#ffffff;" <?php echo ($quotation['approve'] == 1) ? 'disabled style="pointer-events:none; opacity:0.6;"' : ''; ?>><i class="fas fa-edit" style="color:#ffffff;"></i></button>
                                    <?php
                                    $approveClass = (($quotation['approve'] ?? 0) == 1) ? 'btn-success' : 'btn-warning';
                                    $approveText = (($quotation['approve'] ?? 0) == 1) ? 'Approved' : 'Approve';
                                    ?>
                                    <button class="btn <?php echo $approveClass; ?> text-capitalize activeStatusBtn" style="padding: 0.075rem;"  data-quotation-id="<?php echo $quotation['id']; ?>" style="color:#ffffff;" title="<?php echo $approveText; ?>" <?php echo ($quotation['approve'] == 1) ? 'disabled style="pointer-events:none; opacity:0.6;"' : ''; ?>><?php echo $approveText; ?></button>
                                    <button class="btn btn-secondary bg-dark" title="Lock Open" style="padding: 0.375rem; margin-left: 5px;" <?php echo ($quotation['approve'] == 1 && $quotation['locked'] == 0) ? '' : 'disabled style="pointer-events:none; opacity:0.6;"'; ?> data-toggle="modal" data-target="#lockQuotationModal_<?php echo $quotation['id']; ?>"><i class="<?php echo ($quotation['locked'] == 1) ? 'fas fa-lock' : 'fas fa-lock-open'; ?>"></i></button>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php foreach ($quotations as $quotation) : ?>
            <div class="modal fade" id="lockQuotationModal_<?php echo $quotation['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="lockQuotationModalLabel_<?php echo $quotation['id']; ?>">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="lockQuotationModalLabel_<?php echo $quotation['id']; ?>">Lock Quotation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#lockQuotationModal_<?php echo $quotation['id']; ?>').modal('hide')">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to lock this quotation? Once locked, it cannot be edited or approved.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#lockQuotationModal_<?php echo $quotation['id']; ?>').modal('hide')">Cancel</button>
                            <button type="button" class="btn btn-primary confirmLockBtn" data-quotation-id="<?php echo $quotation['id']; ?>">Lock</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include_once ROOT_DIR_PATH . 'include/inc/footer-top.php'; ?>
</div>
<?php include_once ROOT_DIR_PATH . 'include/inc/footer.php'; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="<?php echo BASE_URL; ?>modules/quotation/assets/js/lock-quotation.js"></script>

<script>
$(document).ready(function() {
    var quotationsTable = $('#quotationsTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'pdfHtml5'
        ],
        order: [[0, 'desc']],
        pageLength: 10
    });

    var currentQuotationId = null;

    $('#quotationsTable').on('click', '.viewStatusBtn', function() {
        currentQuotationId = $(this).data('quotation-id');
        $('#statusDate').val('');
        $('#statusText').val('');
        $('#statusHistoryTable tbody').empty();

        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/quotation/ajax_get_quotation_status.php',
            type: 'GET',
            data: { quotation_id: currentQuotationId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var statuses = response.data;
                    $('#statusHistoryTable tbody').empty();
                    statuses.forEach(function(item) {
                        $('#statusHistoryTable tbody').append(
                            '<tr class="text-center"><td>' + item.status_date + '</td><td>' + item.status_text + '</td></tr>'
                        );
                    });
                } else {
                    alert('Failed to fetch status history: ' + response.message);
                }
            },
            error: function() {
                alert('Error occurred while fetching status history.');
            }
        });

        $('#viewStatusModal').modal('show');
    });

    $('#addStatusBtn').click(function() {
        var date = $('#statusDate').val();
        var status = $('#statusText').val();
        if (!date || !status) {
            alert('Please enter both date and status.');
            return;
        }
        if (!currentQuotationId) {
            alert('No quotation selected.');
            return;
        }
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/quotation/ajax_update_quotation_status.php',
            type: 'POST',
            data: {
                quotation_id: currentQuotationId,
                status: status,
                status_date: date
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#statusHistoryTable tbody').append(
                        '<tr class="text-center"><td>' + date + '</td><td>' + status + '</td></tr>'
                    );
                    $('#statusDate').val('');
                    $('#statusText').val('');
                } else {
                    alert('Failed to add status: ' + response.message);
                }
            },
            error: function() {
                alert('Error occurred while adding status.');
            }
        });
    });

    $('#quotationsTable').on('click', '.editQuotationBtn', function() {
        var quotationId = $(this).data('quotation-id');
        window.location.href = 'add.php?id=' + quotationId;
    });

    $('#quotationsTable').on('click', '.activeStatusBtn', function() {
        var button = $(this);
        var quotationId = button.data('quotation-id');
        var newApprove = button.hasClass('btn-success') ? 0 : 1;

        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/quotation/ajax_update_quotation_status.php',
            type: 'POST',
            data: { quotation_id: quotationId, approve: newApprove },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (newApprove === 1) {
                        button.removeClass('btn-warning').addClass('btn-success');
                        button.text('Approved');
                    } else {
                        button.removeClass('btn-success').addClass('btn-warning');
                        button.text('Approve');
                    }
                } else {
                    alert('Failed to update approve status: ' + response.message);
                }
            },
            error: function() {
                alert('Error occurred while updating approve status.');
            }
        });
    });

    $('#quotationsTable').on('click', '.exportExcelBtn', function() {
        var quotationId = $(this).data('id');
        window.location.href = '<?php echo BASE_URL; ?>modules/quotation/export_quotation_excel.php?id=' + quotationId;
    });

    $('#quotationsTable').on('click', '.exportPdfBtn', function() {
        var quotationId = $(this).data('id');
        window.location.href = '<?php echo BASE_URL; ?>modules/quotation/export_quotation_pdf.php?id=' + quotationId;
    });
});
</script>

<div class="modal fade" id="viewStatusModal" tabindex="-1" role="dialog" aria-labelledby="viewStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewStatusModalLabel">View Status History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#viewStatusModal').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="statusForm" class="form-inline mb-3">
          <div class="form-group mr-3" style="flex: 1;">
            <label for="statusDate" class="mr-2">Date</label>
            <input type="date" class="form-control" id="statusDate" name="statusDate" style="width: 100%;" />
          </div>
          <div class="form-group" style="flex: 2;">
            <label for="statusText" class="mr-2">Status</label>
            <input type="text" class="form-control" id="statusText" name="statusText" style="width: 100%;" />
          </div>
        </form>
        <table class="table table-bordered table-striped" id="statusHistoryTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="$('#viewStatusModal').modal('hide')">Close</button>
        <button type="button" class="btn btn-primary" id="addStatusBtn">Add Status</button>
      </div>
    </div>
  </div>
</div>
<?php foreach ($quotations as $quotation) : ?>
<div class="modal fade" id="confirmSendEmailModal_<?php echo $quotation['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="confirmSendEmailModalLabel_<?php echo $quotation['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmSendEmailModalLabel_<?php echo $quotation['id']; ?>">Send Email Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Do you want to send email for Quotation - <?php echo htmlspecialchars($quotation['quotation_number']); ?>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmSendEmailBtn" data-quotation-id="<?php echo $quotation['id']; ?>">Yes</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<div aria-live="polite" aria-atomic="true" style="position: fixed; top: 1rem; right: 1rem; min-height: 200px; z-index: 1080;">
    <div id="toastContainer" style="position: absolute; top: 0; right: 0;"></div>
</div>

<script>
function showToast(message, isSuccess = true) {
    var toastId = 'toast_' + Date.now();
    var toastHtml = `
    <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000" style="min-width: 250px;">
        <div class="toast-header ${isSuccess ? 'bg-success' : 'bg-danger'} text-white">
            <strong class="mr-auto">${isSuccess ? 'Success' : 'Error'}</strong>
            <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    </div>`;
    $('#toastContainer').append(toastHtml);
    $('#' + toastId).toast('show').on('hidden.bs.toast', function () {
        $(this).remove();
    });
}

$(document).ready(function() {
    $('.shareQuotationBtn').on('click', function() {
        var quotationId = $(this).data('target').split('_').pop();
        $('#confirmSendEmailModal_' + quotationId).modal('show');
    });

    $('.confirmSendEmailBtn').on('click', function() {
        var quotationId = $(this).data('quotation-id');
        var button = $(this);
        button.prop('disabled', true).text('Sending...');
        var recipientEmail = $('#quotationsTable').find('tr').filter(function() {
            return $(this).find('.shareQuotationBtn').data('target') === '#shareQuotationModal_' + quotationId;
        }).find('td:nth-child(5)').text().trim();
        var subject = 'Quotation - ' + quotationId;
        var message = 'Dear Customer,\n\nPlease find attached the quotation.\n\nThank you,\nPurewood Team';

        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/quotation/send_quotation_email.php',
            type: 'POST',
            data: {
                quotation_id: quotationId,
                recipient_email: recipientEmail,
                email_subject: subject,
                email_message: message,
                attach_pdf: 1,
                attach_excel: 1
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast('Email sent successfully');
                    $('#confirmSendEmailModal_' + quotationId).modal('hide');
                } else {
                    showToast('Error: ' + response.message, false);
                }
            },
            error: function() {
                showToast('An error occurred while sending the email', false);
            },
            complete: function() {
                button.prop('disabled', false).text('Yes');
            }
        });
    });
});
</script>