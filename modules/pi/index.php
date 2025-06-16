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
} else {
    // Default or guest sidebar or no sidebar
    // include_once ROOT_DIR_PATH . 'include/inc/sidebar.php';
}



try {
    // $database = new Database();
    // $conn = $database->getConnection();

    global $conn;

    $stmt = $conn->query("SELECT pi_id, pi_number, quotation_number, status, date_of_pi_raised FROM pi ORDER BY pi_id DESC");
    $pis = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pis = [];
    $error = "Error fetching PIs: " . $e->getMessage();
}
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Proforma Invoices (PIs)</h1>

            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table id="piTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>PI Number</th>
                            <th>Quotation Number</th>
                            <th>Status</th>
                            <th>Date of PI Raised</th>
                            <th>View Quotation</th>
                            <th>Export</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sr_no = 1; ?>
                        <?php foreach ($pis as $pi) : ?>
                            <tr>
                                <td><?php echo $sr_no++; ?></td>
                                <td><?php echo htmlspecialchars($pi['pi_number']); ?></td>
                                <td><?php echo htmlspecialchars($pi['quotation_number']); ?></td>
                                <td><?php echo htmlspecialchars($pi['status']); ?></td>
                                <td><?php echo htmlspecialchars($pi['date_of_pi_raised']); ?></td>
                                <td>
                                    <button class="btn btn-primary btn-sm viewQuotationBtn" data-pi-id="<?php echo $pi['pi_id']; ?>" data-quotation-number="<?php echo htmlspecialchars($pi['quotation_number']); ?>">View Quotation</button>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info btn-sm export-btn sharePiBtn mr-2" data-toggle="modal" data-target="#sharePiModal_<?php echo $pi['pi_id']; ?>" title="Share">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm export-btn exportExcelBtn mr-2" data-id="<?php echo $pi['pi_id']; ?>" title="Export to Excel">
                                            <i class="fas fa-file-excel"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm export-btn exportPdfBtn" data-id="<?php echo $pi['pi_id']; ?>" title="Export to PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Share PI Modal -->
            <?php foreach ($pis as $pi) : ?>
            <div class="modal fade" id="sharePiModal_<?php echo $pi['pi_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="sharePiModalLabel_<?php echo $pi['pi_id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="sharePiModalLabel_<?php echo $pi['pi_id']; ?>">Send Email Confirmation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#sharePiModal_<?php echo $pi['pi_id']; ?>').modal('hide')">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Do you want to send email for PI - <?php echo htmlspecialchars($pi['pi_number']); ?>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#sharePiModal_<?php echo $pi['pi_id']; ?>').modal('hide')">Cancel</button>
                            <button type="button" class="btn btn-primary confirmSendEmailBtn" data-pi-id="<?php echo $pi['pi_id']; ?>">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </div>
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
    var piTable = $('#piTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'pdfHtml5'
        ],
        order: [[0, 'desc']],
        pageLength: 10
    });

    $('#piTable').on('click', '.viewQuotationBtn', function() {
        var quotationNumber = $(this).data('quotation-number');
        $('#quotationDetailsContent').html('<p>Loading...</p>');
        $('#quotationDetailsModal').modal('show');

        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/quotation/get_quotation_details.php',
            type: 'GET',
            data: { quotation_number: quotationNumber },
            dataType: 'html',
            success: function(response) {
                $('#quotationDetailsContent').html(response);
            },
            error: function() {
                $('#quotationDetailsContent').html('<p>Error loading quotation details.</p>');
            }
        });
    });

    // Handle per-row Excel export button click
    $('#piTable').on('click', '.exportExcelBtn', function() {
        var piId = $(this).data('id');
        window.location.href = '<?php echo BASE_URL; ?>modules/pi/export_pi_excel.php?id=' + piId;
    });

    // Handle per-row PDF export button click
    $('#piTable').on('click', '.exportPdfBtn', function() {
        var piId = $(this).data('id');
        window.location.href = '<?php echo BASE_URL; ?>modules/pi/export_pi_pdf.php?id=' + piId;
    });

    // Handle share button click
    $('#piTable').on('click', '.sharePiBtn', function() {
        var piId = $(this).data('target').split('_').pop();
        $('#sharePiModal_' + piId).modal('show');
    });

    // Handle Yes button click in share modal
    $('.confirmSendEmailBtn').on('click', function() {
        var piId = $(this).data('pi-id');

        var button = $(this);
        button.prop('disabled', true).text('Sending...');

        // Prepare data for email sending
        var subject = 'Proforma Invoice - ' + piId;
        var message = 'Dear Customer,\n\nPlease find attached the Proforma Invoice.\n\nThank you,\nPurewood Team';

        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/pi/send_pi_email.php',
            type: 'POST',
            data: {
                pi_id: piId,
                email_subject: subject,
                email_message: message,
                attach_pdf: 1,
                attach_excel: 1
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Email sent successfully');
                    $('#sharePiModal_' + piId).modal('hide');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while sending the email');
            },
            complete: function() {
                button.prop('disabled', false).text('Yes');
            }
        });
    });
});
</script>
