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

$id = $_GET['id'] ?? null;
$edit_mode = false;
$bom_data = [];
$item_data = [];

if ($id) {
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM bom_main WHERE id = ?");
    $stmt->execute([$id]);
    $bom_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $conn->prepare("SELECT * FROM bom_items WHERE bom_id = ?");
    $stmt2->execute([$id]);
    $item_data = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

function generateBOMNumber($conn) {
    $year = date('Y');
    $prefix = "BOM-$year-";
    $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING_INDEX(bom_number, '-', -1) AS UNSIGNED)) AS last_seq FROM bom_main WHERE bom_number LIKE ?");
    $stmt->execute(["BOM-$year-%"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_seq = (int)$result['last_seq'];
    $next_seq = $last_seq + 1;
    $seqFormatted = str_pad($next_seq, 4, '0', STR_PAD_LEFT);
    return $prefix . $seqFormatted;
}

$auto_bom_number = $edit_mode ? $bom_data['bom_number'] : generateBOMNumber($conn);
$costing_sheet_number = $edit_mode ? ($bom_data['costing_sheet_number'] ?? '') : '';
$client_name = $edit_mode ? $bom_data['client_name'] : '';
// FIX: Format created_at to YYYY-MM-DD for input type="date"
$created_date = $edit_mode ? substr($bom_data['created_at'], 0, 10) : date('Y-m-d');
$prepared_by = $edit_mode ? $bom_data['prepared_by'] : '';
?>

<div class="container-fluid mb-5">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Bill Of Material</h6>
        </div>
        <div class="card-body">
            <form id="bomForm" autocomplete="off">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                <?php endif; ?>
                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="bom_number" class="form-label">Bill Of Material Number</label>
                        <input type="text" class="form-control" id="bom_number" name="bom_number" value="<?php echo htmlspecialchars($auto_bom_number); ?>" readonly>
                    </div>
                    <div class="col-lg-4">
                        <label for="costing_sheet_number" class="form-label">Costing Sheet Number</label>
                        <input type="text" class="form-control" id="costing_sheet_number" name="costing_sheet_number" value="<?php echo htmlspecialchars($costing_sheet_number); ?>" required>
                    </div>
                    <div class="col-lg-4">
                        <label for="client_name" class="form-label">Client Name</label>
                        <input type="text" class="form-control" id="client_name" name="client_name" value="<?php echo htmlspecialchars($client_name); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="created_date" class="form-label">Created Date</label>
                        <input type="date" class="form-control" id="created_date" name="created_date" value="<?php echo htmlspecialchars($created_date); ?>" required>
                    </div>
                    <div class="col-lg-4">
                        <label for="prepared_by" class="form-label">Prepared By</label>
                        <input type="text" class="form-control" id="prepared_by" name="prepared_by" value="<?php echo htmlspecialchars($prepared_by); ?>" required>
                    </div>
                </div>

                <h5>Add Item Details</h5>

                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Item Name</th>
                            <th>Item Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total</th>
                            <th>
                                <input type="text" id="grandTotal" class="form-control" readonly value="0">
                            </th>
                            <th>
                                <button type="button" class="btn btn-secondary btn-sm ms-2 mb-3" id="addRowBtn" title="Add Row">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update' : 'Save'; ?> BOM</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowCount = 0;

    function calculateRowTotal(row) {
        const priceInput = row.querySelector('.item-price');
        const qtyInput = row.querySelector('.item-qty');
        const totalInput = row.querySelector('.item-total');

        const price = parseFloat(priceInput.value) || 0;
        const qty = parseFloat(qtyInput.value) || 0;
        const total = price * qty;

        totalInput.value = total.toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-total').forEach(function(input) {
            grandTotal += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotal').value = grandTotal.toFixed(2);
    }

    function addRow(serial = '', name = '', price = '', qty = '', total = '') {
        rowCount++;
        const tbody = document.querySelector('#itemsTable tbody');
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td><input type="text" name="serial_number[]" class="form-control" value="${serial}" required></td>
            <td><input type="text" name="item_name[]" class="form-control" value="${name}" required></td>
            <td><input type="number" name="item_price[]" class="form-control item-price" step="0.01" min="0" value="${price}" required></td>
            <td><input type="number" name="quantity[]" class="form-control item-qty" step="1" min="0" value="${qty}" required></td>
            <td><input type="text" name="total[]" class="form-control item-total" readonly value="${total || 0}"></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRowBtn" title="Delete Row"><i class="fas fa-trash"></i></button></td>
        `;

        tbody.appendChild(tr);

        tr.querySelector('.item-price').addEventListener('input', () => calculateRowTotal(tr));
        tr.querySelector('.item-qty').addEventListener('input', () => calculateRowTotal(tr));
        tr.querySelector('.removeRowBtn').addEventListener('click', () => {
            tr.remove();
            calculateGrandTotal();
        });

        calculateRowTotal(tr);
    }

    <?php if ($edit_mode && !empty($item_data)): ?>
        <?php foreach ($item_data as $item): ?>
            addRow(
                "<?php echo htmlspecialchars($item['product_code']); ?>",
                "<?php echo htmlspecialchars($item['product_name']); ?>",
                "<?php echo htmlspecialchars($item['price']); ?>",
                "<?php echo htmlspecialchars($item['quantity']); ?>",
                "<?php echo htmlspecialchars($item['total_amount']); ?>"
            );
        <?php endforeach; ?>
    <?php else: ?>
        addRow();
    <?php endif; ?>

    document.getElementById('addRowBtn').addEventListener('click', function() {
        addRow();
    });

    document.getElementById('bomForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?php echo BASE_URL; ?>modules/bom/ajax_save_bom.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const toastContainer = document.body;
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white ${data.success ? 'bg-success' : 'bg-danger'} border-0`;
            toast.role = 'alert';
            toast.ariaLive = 'assertive';
            toast.ariaAtomic = 'true';
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '1055';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${data.message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            toastContainer.appendChild(toast);
            var bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            if (data.success && !<?php echo $edit_mode ? 'true' : 'false'; ?>) {
                e.target.reset();
                document.querySelector('#itemsTable tbody').innerHTML = '';
                addRow();
                document.getElementById('grandTotal').value = '0.00';
                document.getElementById('bom_number').value = data.new_bom_number; // Update with new BOM number
            } else if (data.success && <?php echo $edit_mode ? 'true' : 'false'; ?>) {
                 setTimeout(() => {
                    window.location.href = '<?php echo BASE_URL; ?>modules/bom/index.php';
                }, 1500);
            }
        })
        .catch(error => {
            const toastContainer = document.body;
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-danger border-0';
            toast.role = 'alert';
            toast.ariaLive = 'assertive';
            toast.ariaAtomic = 'true';
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '1055';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        Error submitting form: ${error.message || error}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            toastContainer.appendChild(toast);
            var bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            console.error('Fetch error:', error);
        });
    });
});
</script>

<?php include_once ROOT_DIR_PATH . 'include/inc/footer.php'; ?>