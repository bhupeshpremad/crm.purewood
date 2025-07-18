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

    $limit = 20;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM quotations";
    $countStmt = $conn->prepare($countSql);
    $countStmt->execute();
    $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalRows = $countResult['total'];
    $totalPages = ceil($totalRows / $limit);

    // Fetch paginated data
    $sql = "SELECT id, lead_id, quotation_number, customer_name, customer_email, customer_phone, delivery_term, terms_of_delivery, approve, locked FROM quotations ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $quotations = [];
        $error = "Error fetching quotations: " . $e->getMessage();
    }
?> 

    <div class="container-fluid">
    
        <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>
        
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Quotations</h6>
                    <div class="d-flex align-items-center gap-3">
                        <input type="text" id="searchQuotationInput" class="form-control form-control-sm" placeholder="Search Quotations" style="width: 250px;">
                        <a href="add.php" class="btn btn-primary btn-sm">Add New Quotation</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="quotationsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sr NO</th>
                            <th>Quotation Number</th>
                            <th>Customer Name</th>
                            <th>Customer Email</th>
                            <th>Status</th>
                            <th>Export</th>
                            <th>Actions</th>
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
                                    <button class="btn <?php echo $approveClass; ?> text-capitalize approveBtn" style="padding: 0.375rem;" data-quotation-id="<?php echo $quotation['id']; ?>" title="<?php echo $approveText; ?>" <?php echo ($quotation['approve'] == 1) ? 'disabled' : ''; ?>><?php echo $approveText; ?></button>
                                    <button class="btn btn-secondary bg-dark lockBtn" title="<?php echo ($quotation['locked'] == 1) ? 'Locked' : 'Lock'; ?>" style="padding: 0.375rem; margin-left: 5px;" data-quotation-id="<?php echo $quotation['id']; ?>" <?php echo ($quotation['approve'] != 1 || $quotation['locked'] == 1) ? 'disabled' : ''; ?>><i class="<?php echo ($quotation['locked'] == 1) ? 'fas fa-lock' : 'fas fa-lock-open'; ?>"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
    </tbody>
</table>
</div>

<nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <?php if ($page > 1): ?>
      <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
    <?php else: ?>
      <li class="page-item disabled"><span class="page-link">Previous</span></li>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
      </li>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
      <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
    <?php else: ?>
      <li class="page-item disabled"><span class="page-link">Next</span></li>
    <?php endif; ?>
  </ul>
</nav>

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
        <?php include_once ROOT_DIR_PATH . 'include/inc/footer-top.php'; ?>


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
    $('#quotationsTable').DataTable({
        dom: '<"row"<"col-sm-6"i><"col-sm-6"p>>rt<"row"<"col-sm-12">>',
        paging: true,
        searching: true,
        ordering: true,
        lengthChange: false,
        pageLength: 10
    });
    
    // Approve quotation
    $('.approveBtn').on('click', function() {
        var quotationId = $(this).data('quotation-id');
        var button = $(this);
        
        if (confirm('Are you sure you want to approve this quotation?')) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>modules/quotation/ajax_update_quotation_status.php',
                type: 'POST',
                data: {
                    quotation_id: quotationId,
                    action: 'approve'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('Quotation approved successfully');
                        button.removeClass('btn-warning').addClass('btn-success').text('Approved').prop('disabled', true);
                        // Enable lock button
                        button.siblings('.lockBtn').prop('disabled', false);
                    } else {
                        showToast('Error: ' + response.message, false);
                    }
                },
                error: function() {
                    showToast('An error occurred while approving quotation', false);
                }
            });
        }
    });
    
    // Lock quotation
    $('.lockBtn').on('click', function() {
        var quotationId = $(this).data('quotation-id');
        var button = $(this);
        
        if (confirm('Are you sure you want to lock this quotation? Once locked, it cannot be edited.')) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>modules/quotation/ajax_lock_quotation.php',
                type: 'POST',
                data: {
                    quotation_id: quotationId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast('Quotation locked successfully');
                        button.find('i').removeClass('fa-lock-open').addClass('fa-lock');
                        button.prop('disabled', true).attr('title', 'Locked');
                    } else {
                        showToast('Error: ' + response.message, false);
                    }
                },
                error: function() {
                    showToast('An error occurred while locking quotation', false);
                }
            });
        }
    });
    
    // Edit quotation
    $('.editQuotationBtn').on('click', function() {
        var quotationId = $(this).data('quotation-id');
        window.location.href = 'add.php?id=' + quotationId;
    });
    
    // Export Excel
    $('.exportExcelBtn').on('click', function() {
        var quotationId = $(this).data('id');
        window.open('<?php echo BASE_URL; ?>modules/quotation/export_quotation_excel.php?id=' + quotationId, '_blank');
    });
    
    // Export PDF
    $('.exportPdfBtn').on('click', function() {
        var quotationId = $(this).data('id');
        window.open('<?php echo BASE_URL; ?>modules/quotation/export_quotation_pdf.php?id=' + quotationId, '_blank');
    });
    
    // View Status
    var currentQuotationId = null;
    $('.viewStatusBtn').on('click', function() {
        currentQuotationId = $(this).data('quotation-id');
        $('#viewStatusModal').modal('show');
        loadStatusHistory(currentQuotationId);
    });
    
    // Add Status
    $('#addStatusBtn').on('click', function() {
        var statusDate = $('#statusDate').val();
        var statusText = $('#statusText').val();
        
        if (!statusDate || !statusText) {
            showToast('Please fill both date and status fields', false);
            return;
        }
        
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/quotation/ajax_update_quotation_status.php',
            type: 'POST',
            data: {
                quotation_id: currentQuotationId,
                action: 'add_status',
                status_date: statusDate,
                status_text: statusText
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast('Status added successfully');
                    $('#statusDate').val('');
                    $('#statusText').val('');
                    loadStatusHistory(currentQuotationId);
                } else {
                    showToast('Error: ' + response.message, false);
                }
            },
            error: function() {
                showToast('An error occurred while adding status', false);
            }
        });
    });
});

function loadStatusHistory(quotationId) {
    $.ajax({
        url: '<?php echo BASE_URL; ?>modules/quotation/ajax_get_quotation_status.php',
        type: 'POST',
        data: { quotation_id: quotationId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var tbody = $('#statusHistoryTable tbody');
                tbody.empty();
                if (response.status_history && response.status_history.length > 0) {
                    response.status_history.forEach(function(status) {
                        tbody.append('<tr><td>' + status.date + '</td><td>' + status.status + '</td></tr>');
                    });
                } else {
                    tbody.append('<tr><td colspan="2" class="text-center">No status history found</td></tr>');
                }
            }
        },
        error: function() {
            showToast('Error loading status history', false);
        }
    });
}
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