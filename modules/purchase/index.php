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

global $conn;

$search_po = $_GET['search_po'] ?? '';
$search_jci = $_GET['search_jci'] ?? '';
$search_son = $_GET['search_son'] ?? '';

$whereClauses = [];
$params = [];

if ($search_po !== '') {
    $whereClauses[] = 'pm_tbl.po_number LIKE :po_number';
    $params[':po_number'] = '%' . $search_po . '%';
}
if ($search_jci !== '') {
    $whereClauses[] = 'p.jci_number LIKE :jci_number';
    $params[':jci_number'] = '%' . $search_jci . '%';
}
if ($search_son !== '') {
    $whereClauses[] = 'p.sell_order_number LIKE :sell_order_number';
    $params[':sell_order_number'] = '%' . $search_son . '%';
}

$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

$sql = "SELECT p.id, p.po_number, p.jci_number, p.sell_order_number 
        FROM purchase_main p 
        $whereSql 
        ORDER BY p.id DESC";

$stmt = $conn->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"/>
<div class="container-fluid">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Purchase List</h6>
                <form action="" method="GET" class="d-flex flex-wrap gap-2">
                  
                    <input type="text" name="search_son" class="form-control form-control-sm mb-2" style="max-width:180px;" placeholder="Search Sell Order No." value="<?php echo htmlspecialchars($search_son); ?>">
                    <button type="submit" class="btn btn-info btn-sm ms-2 mb-2 mx-2">Search</button>
                    <a href="add.php" class="btn btn-primary btn-sm ms-2 mb-2">Add Purchase</a>
                </form>
        </div>
        <div class="card-body">

            <table class="table table-bordered" id="purchaseTable">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>JCI Number</th>
                        <th>Sell Order Number</th>
                        <th>PO Number</th>
                        <th>View Details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $serial = 1;
                    if ($result && count($result) > 0) {
                        foreach ($result as $row) {
                            $purchase_id = $row['id'];
                            echo "<tr>";
                            echo "<td>" . $serial++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['jci_number']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['sell_order_number']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['po_number']) . "</td>";
                            echo "<td><button class='btn btn-info btn-sm view-details-btn' data-id='{$purchase_id}'>View Details</button></td>";
                            echo "<td><a href='add.php?id={$purchase_id}' class='btn btn-primary btn-sm'>Edit</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No purchase records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detailsModalBody">
                <div class="text-center">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#purchaseTable').DataTable({
        dom: 'rt<"row mt-3"<"col-sm-12"p>><"row"<"col-sm-12"i>>',
        paging: true,
        searching: false,
        ordering: true,
        lengthChange: false,
        pageLength: 20
    });
    $('.purchase-details-link').click(function(e) {
        e.preventDefault();
        var purchaseId = $(this).data('id');
        $('#detailsModalLabel').text('Purchase Details (ID: ' + purchaseId + ')');
        $('#detailsModalBody').html('<div class="text-center"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...</div>');
        $('#detailsModal').modal('show');
        $.ajax({
            url: 'fetch_purchase_details.php',
            method: 'POST',
            data: { purchase_id: purchaseId },
            dataType: 'html',
            success: function(data) {
                $('#detailsModalBody').html(data);
            },
            error: function(xhr, status, error) {
                $('#detailsModalBody').html('<div class="alert alert-danger">Failed to load details. Error: ' + (xhr.responseText || error) + '</div>');
            }
        });
    });

    $('.view-details-btn').click(function(e) {
        e.preventDefault();
        var purchaseId = $(this).data('id');
        $('#detailsModalLabel').text('Purchase Details (ID: ' + purchaseId + ')');
        $('#detailsModalBody').html('<div class="text-center"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...</div>');
        $('#detailsModal').modal('show');
        $.ajax({
            url: 'fetch_purchase_details.php',
            method: 'POST',
            data: { purchase_id: purchaseId },
            dataType: 'html',
            success: function(data) {
                $('#detailsModalBody').html(data);
            },
            error: function(xhr, status, error) {
                $('#detailsModalBody').html('<div class="alert alert-danger">Failed to load details. Error: ' + (xhr.responseText || error) + '</div>');
            }
        });
    });
});
</script>
<?php
include_once ROOT_DIR_PATH . 'include/inc/footer.php';
?>