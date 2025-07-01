<?php
include_once __DIR__ . '/../../config/config.php';
if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', __DIR__ . '/../../' . DIRECTORY_SEPARATOR);
}
session_start();
include_once ROOT_DIR_PATH . 'include/inc/header.php';

$user_type = $_SESSION['user_type'] ?? 'guest';

if ($user_type === 'superadmin') {
    include_once ROOT_DIR_PATH . 'superadmin/sidebar.php';
} elseif ($user_type === 'salesadmin') {
    include_once ROOT_DIR_PATH . 'salesadmin/sidebar.php';
} elseif ($user_type === 'accounts') {
    include_once ROOT_DIR_PATH . 'accountsadmin/sidebar.php';
}

global $conn;

$sql = "SELECT j.id, j.jci_number, j.jci_type, j.created_by, j.jci_date, p.po_number
        FROM jci_main j
        LEFT JOIN po_main p ON j.po_id = p.id
        ORDER BY j.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$jci_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mb-5">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Job Card List</h6>
            <a href="add.php" class="btn btn-primary btn-sm">Add New Job Card</a>
        </div>
        <div class="card-body">
            <div class="table-responsive dataTables_wrapper_custom">
                <table class="table table-bordered table-striped" id="jciTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Serial No</th>
                            <th>PO Number</th>
                            <th>Job Card Number</th>
                            <th>Job Card Type</th>
                            <th>Created By</th>
                            <th>Job Card Date</th>
                            <th>Details</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn = 1; foreach ($jci_list as $jci): ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td><?php echo htmlspecialchars($jci['po_number'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($jci['jci_number']); ?></td>
                            <td><?php echo htmlspecialchars($jci['jci_type']); ?></td>
                            <td><?php echo htmlspecialchars($jci['created_by']); ?></td>
                            <td><?php echo htmlspecialchars($jci['jci_date']); ?></td>
                            <td>
                                <?php if ($jci['jci_type'] === 'Contracture'): ?>
                                    <button class="btn btn-info btn-sm view-items-btn" data-jci-id="<?php echo $jci['id']; ?>">View Contracture Details</button>
                                <?php else: ?>
                                    <span>N/A (In-House)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="add.php?id=<?php echo $jci['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="itemDetailsModal" tabindex="-1" role="dialog" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemDetailsModalLabel">Job Card Contracture Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemDetailsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sl No</th>
                                    <th>Contracture Name</th>
                                    <th>Labour Cost</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                    <th>Delivery Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#jciTable').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        paging: true,
        searching: true,
        ordering: true,
        lengthChange: true,
        pageLength: 10
    });

    document.querySelectorAll('.view-items-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const jciId = this.getAttribute('data-jci-id');
            fetch('<?php echo BASE_URL; ?>modules/jci/ajax_fetch_jci_items.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'jci_id=' + encodeURIComponent(jciId)
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                const tbody = document.querySelector('#itemDetailsTable tbody');
                tbody.innerHTML = '';
                if (data.success && data.items.length > 0) {
                    data.items.forEach((item, idx) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${idx + 1}</td>
                            <td>${item.contracture_name}</td>
                            <td>${item.labour_cost}</td>
                            <td>${item.quantity}</td>
                            <td>${item.total_amount}</td>
                            <td>${item.delivery_date}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else if (data.success && data.items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No contracture details found for this Job Card.</td></tr>';
                } else {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error: ${data.message || 'Failed to load items.'}</td></tr>`;
                }
                var modal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
                modal.show();
            })
            .catch(error => {
                const tbody = document.querySelector('#itemDetailsTable tbody');
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Network error or failed to parse response: ${error.message}</td></tr>`;
                var modal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
                modal.show();
                console.error('Fetch error:', error);
            });
        });
    });
});
</script>