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
} else {
    // Default or guest sidebar or no sidebar
    // include_once __DIR__ . '/../../include/inc/sidebar.php';
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Query to get customers with counts of leads, quotations, and PIs
    $stmt = $conn->query("
        SELECT 
            l.id as lead_id, 
            l.company_name, 
            l.contact_email, 
            l.contact_phone, 
            COUNT(DISTINCT l.id) as total_leads,
            COUNT(DISTINCT q.id) as total_quotations,
            COUNT(DISTINCT p.pi_id) as total_pis
        FROM leads l
        LEFT JOIN quotations q ON q.lead_id = l.id
        INNER JOIN pi p ON p.quotation_id = q.id
        GROUP BY l.id, l.company_name, l.contact_email, l.contact_phone
        HAVING total_pis > 0
        ORDER BY l.company_name ASC
    ");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $customers = [];
    $error = "Error fetching customers: " . $e->getMessage();
}
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Customers with Details</h1>

            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table id="customersTable" class="table table-bordered table-striped">
                    <thead class="bg-gradient-primary text-white">
                        <tr>
                            <th>Sl Number</th>
                            <th>Customer Name</th>
                            <th>Customer Email</th>
                            <th>Customer Phone</th>
                            <th>Total Leads</th>
                            <th>Total Quotations</th>
                            <th>Total PIs</th>
                            <th>Create Quotation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sr_no = 1; ?>
                        <?php foreach ($customers as $customer) : ?>
                            <tr>
                                <td><?php echo $sr_no++; ?></td>
                                <td><?php echo htmlspecialchars($customer['company_name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['contact_email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['contact_phone']); ?></td>
                                <td><a href="#" class="view-leads" data-lead-id="<?php echo $customer['lead_id']; ?>"><?php echo $customer['total_leads']; ?></a></td>
                                <td><a href="#" class="view-quotations" data-lead-id="<?php echo $customer['lead_id']; ?>"><?php echo $customer['total_quotations']; ?></a></td>
                                <td><a href="#" class="view-pis" data-lead-id="<?php echo $customer['lead_id']; ?>"><?php echo $customer['total_pis']; ?></a></td>
                                <td><a href="../quotation/add.php?lead_id=<?php echo $customer['lead_id']; ?>" class="btn btn-primary btn-sm">Create Quotation</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Modals for leads, quotations, and PIs -->
            <div class="modal fade" id="leadsModal" tabindex="-1" role="dialog" aria-labelledby="leadsModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="leadsModalLabel">Leads List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body" id="leadsModalBody">
                    <!-- Leads list will be loaded here -->
                  </div>
                </div>
              </div>
            </div>

            <div class="modal fade" id="quotationsModal" tabindex="-1" role="dialog" aria-labelledby="quotationsModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="quotationsModalLabel">Quotations List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body" id="quotationsModalBody">
                    <!-- Quotations list will be loaded here -->
                  </div>
                </div>
              </div>
            </div>

            <div class="modal fade" id="pisModal" tabindex="-1" role="dialog" aria-labelledby="pisModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="pisModalLabel">PIs List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body" id="pisModalBody">
                    <!-- PIs list will be loaded here -->
                  </div>
                </div>
              </div>
            </div>

        </div>
    </div>
</div>

<?php include_once ROOT_DIR_PATH . 'include/inc/footer.php'; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#customersTable').DataTable({
        order: [[1, 'asc']],
        pageLength: 10
    });

    // Load leads list in modal
    $('.view-leads').on('click', function(e) {
        e.preventDefault();
        var leadId = $(this).data('lead-id');
        $('#leadsModalBody').html('Loading...');
        $('#leadsModal').modal('show');
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/customer/ajax_get_leads.php',
            method: 'GET',
            data: { lead_id: leadId },
            success: function(data) {
                $('#leadsModalBody').html(data);
            },
            error: function() {
                $('#leadsModalBody').html('Error loading leads.');
            }
        });
    });

    // Load quotations list in modal
    $('.view-quotations').on('click', function(e) {
        e.preventDefault();
        var leadId = $(this).data('lead-id');
        $('#quotationsModalBody').html('Loading...');
        $('#quotationsModal').modal('show');
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/customer/ajax_get_quotations.php',
            method: 'GET',
            data: { lead_id: leadId },
            success: function(data) {
                $('#quotationsModalBody').html(data);
            },
            error: function() {
                $('#quotationsModalBody').html('Error loading quotations.');
            }
        });
    });

    // Load PIs list in modal
    $('.view-pis').on('click', function(e) {
        e.preventDefault();
        var leadId = $(this).data('lead-id');
        $('#pisModalBody').html('Loading...');
        $('#pisModal').modal('show');
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/customer/ajax_get_pis.php',
            method: 'GET',
            data: { lead_id: leadId },
            success: function(data) {
                $('#pisModalBody').html(data);
            },
            error: function() {
                $('#pisModalBody').html('Error loading PIs.');
            }
        });
    });
});
</script>
