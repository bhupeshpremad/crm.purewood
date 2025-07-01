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

$sql = "SELECT * FROM bom_main ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$bom_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mb-5">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Bill Of Material List</h6>
            <a href="add.php" class="btn btn-primary btn-sm">Add New BOM</a>
        </div>
        <div class="card-body">
            <div class="table-responsive dataTables_wrapper_custom">
                <table class="table table-bordered table-striped" id="bomTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Serial No</th>
                            <th>Bill Of Material Number</th>
                            <th>Costing Sheet Number</th>
                            <th>Client Name</th>
                            <th>Prepared By</th>
                            <th>Created Date</th>
                            <th>Item Details</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn = 1; foreach ($bom_list as $bom): ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td><?php echo htmlspecialchars($bom['bom_number']); ?></td>
                            <td><?php echo htmlspecialchars($bom['costing_sheet_number'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($bom['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($bom['prepared_by']); ?></td>
                            <td><?php echo htmlspecialchars($bom['order_date']); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm view-items-btn" data-bom-id="<?php echo $bom['id']; ?>">View Items</button>
                            </td>
                            <td>
                                <a href="add.php?id=<?php echo $bom['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
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
                    <h5 class="modal-title" id="itemDetailsModalLabel">BOM Item Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemDetailsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sl No</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total Amount</th>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#bomTable').DataTable({
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
            const bomId = this.getAttribute('data-bom-id');
            fetch('<?php echo BASE_URL; ?>modules/bom/ajax_fetch_bom_items.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'bom_id=' + encodeURIComponent(bomId)
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
                            <td>${item.product_code}</td>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.price}</td>
                            <td>${item.total_amount}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else if (data.success && data.items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No items found for this BOM.</td></tr>';
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