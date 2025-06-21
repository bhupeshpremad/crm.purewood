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

// Pagination and search parameters
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search_po = $_GET['search_po'] ?? '';
$search_jci = $_GET['search_jci'] ?? '';
$search_son = $_GET['search_son'] ?? '';

$whereClauses = [];
$params = [];

if ($search_po !== '') {
    $whereClauses[] = 'po_number LIKE :po_number';
    $params[':po_number'] = '%' . $search_po . '%';
}
if ($search_jci !== '') {
    $whereClauses[] = 'jci_number LIKE :jci_number';
    $params[':jci_number'] = '%' . $search_jci . '%';
}
if ($search_son !== '') {
    $whereClauses[] = 'sell_order_number LIKE :sell_order_number';
    $params[':sell_order_number'] = '%' . $search_son . '%';
}

$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM purchase_main $whereSql";
$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);
$countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
$totalRows = $countResult['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch paginated data
$sql = "SELECT id, po_number, jci_number, sell_order_number FROM purchase_main $whereSql ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-end gap-3">
            <div class="row w-100 align-items-center">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <h6 class="m-0 font-weight-bold text-primary">Purchase List</h6>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="row justify-content-start align-items-end">
                        <div class="col-lg-1"></div>
                        <div class="col-lg-7 col-md-7">
                            <input type="text" id="purchaseSearchInput" class="form-control form-control-sm" placeholder="Search Purchase...">  
                        </div>
                        <div class="col-lg-4 col-md-4 text-end">
                            <a href="add.php" class="btn btn-primary btn-sm">Add Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="purchaseTable">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>PO Number</th>
                        <th>JCI Number</th>
                        <th>Sell Order Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $serial = $offset + 1;
                    if ($result && count($result) > 0) {
                        foreach ($result as $row) {
                            $purchase_id = $row['id'];
                            echo "<tr>";
                            echo "<td>" . $serial++ . "</td>";
                            echo "<td><a href='#' class='purchase-details-link' data-id='{$purchase_id}'>" . htmlspecialchars($row['po_number']) . "</a></td>";
                            echo "<td><a href='#' class='purchase-details-link' data-id='{$purchase_id}'>" . htmlspecialchars($row['jci_number']) . "</a></td>";
                            echo "<td><a href='#' class='purchase-details-link' data-id='{$purchase_id}'>" . htmlspecialchars($row['sell_order_number']) . "</a></td>";

                            // Edit button linking to add.php with purchase id
                            echo "<td><a href='add.php?id={$purchase_id}' class='btn btn-primary btn-sm'>Edit</a></td>";

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No purchase records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <!-- Removed pagination as per user request -->
        </div>
    </div>
</div>

<!-- Modals for details -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailsModalLabel">Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="detailsModalBody">
        <!-- Details content will be loaded here -->
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

<script>
$(document).ready(function() {
    $('.purchase-details-link').click(function(e) {
        e.preventDefault();
        var purchaseId = $(this).data('id');
        $('#detailsModalLabel').text('Purchase Details');
        $('#detailsModalBody').html('<div class="text-center"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...</div>');
        $('#detailsModal').modal('show');

        $.ajax({
            url: 'fetch_purchase_details.php',
            method: 'POST',
            data: { purchase_id: purchaseId, section: 'all' },
            dataType: 'html',
            success: function(data) {
                $('#detailsModalBody').html(data);
            },
            error: function() {
                $('#detailsModalBody').html('<div class="alert alert-danger">Failed to load details.</div>');
            }
        });
    });
});
</script>

<?php
include_once ROOT_DIR_PATH . 'include/inc/footer.php';
?>
