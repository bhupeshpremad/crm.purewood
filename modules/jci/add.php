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
$jci_data = [];
$item_data = [];

if ($id) {
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM jci_main WHERE id = ?");
    $stmt->execute([$id]);
    $jci_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $conn->prepare("SELECT * FROM jci_items WHERE jci_id = ?");
    $stmt2->execute([$id]);
    $item_data = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

function generateJCINumber($conn) {
    $year = date('Y');
    $prefix = "JCI-$year-";
    $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING_INDEX(jci_number, '-', -1) AS UNSIGNED)) AS last_seq FROM jci_main WHERE jci_number LIKE ?");
    $stmt->execute(["JCI-$year-%"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_seq = (int)$result['last_seq'];
    $next_seq = $last_seq + 1;
    $seqFormatted = str_pad($next_seq, 4, '0', STR_PAD_LEFT);
    return $prefix . $seqFormatted;
}

$auto_jci_number = $edit_mode ? $jci_data['jci_number'] : generateJCINumber($conn);
$jci_type = $edit_mode ? ($jci_data['jci_type'] ?? 'Contracture') : 'Contracture';
$created_by = $edit_mode ? $jci_data['created_by'] : ($_SESSION['user_name'] ?? '');
$jci_date = $edit_mode ? substr($jci_data['jci_date'], 0, 10) : date('Y-m-d');
$po_id_selected = $edit_mode ? ($jci_data['po_id'] ?? '') : '';

$stmt_po = $conn->prepare("SELECT id, po_number FROM po_main ORDER BY po_number ASC");
$stmt_po->execute();
$po_numbers = $stmt_po->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .select2.select2-container{
        display: block;
        width: 100%;
        height: calc(1.5em + .75rem + 2px);
        padding: .375rem .75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #6e707e;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #d1d3e2;
        border-radius: .35rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    .select2-container .select2-selection--single .select2-selection__rendered{
        display: inline-block !important;
    }
</style>

<div class="container-fluid mb-5">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Job Card</h6>
        </div>
        <div class="card-body">
            <form id="jciForm" autocomplete="off">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                <?php endif; ?>
<div class="row mb-3">
    <div class="col-lg-4">
        <label for="po_id" class="form-label">Select PO Number</label>
        <select class="form-control select2-enabled" id="po_id" name="po_id" required style="width:100%;">
            <option value="">Select PO Number</option>
            <?php foreach ($po_numbers as $po): ?>
                <option value="<?php echo htmlspecialchars($po['id']); ?>" <?php echo ($po_id_selected == $po['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($po['po_number']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-lg-4">
        <label for="sell_order_number" class="form-label">Sell Order Number</label>
        <input type="text" class="form-control" id="sell_order_number" name="sell_order_number" readonly>
    </div>
    <div class="col-lg-4">
        <label for="jci_number" class="form-label">JCI Number</label>
        <input type="text" class="form-control" id="jci_number" name="jci_number" readonly value="<?php echo htmlspecialchars($auto_jci_number); ?>">
    </div>
    <!-- Removed duplicate Job Card Number input -->
    <div class="col-lg-4">
        <label for="created_by" class="form-label">Created By</label>
        <input type="text" class="form-control" id="created_by" name="created_by" value="<?php echo htmlspecialchars($created_by); ?>" required>
    </div>
</div>

                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="jci_date" class="form-label">Job Card Date</label>
                        <input type="date" class="form-control" id="jci_date" name="jci_date" value="<?php echo htmlspecialchars($jci_date); ?>" required>
                    </div>
                    <div class="col-lg-4">
                        <label for="jci_type" class="form-label">Job Card Type</label>
                        <select class="form-control" id="jci_type" name="jci_type" required>
                            <option value="Contracture" <?php echo ($jci_type === 'Contracture') ? 'selected' : ''; ?>>Contracture</option>
                            <option value="In-House" <?php echo ($jci_type === 'In-House') ? 'selected' : ''; ?>>In-House</option>
                        </select>
                    </div>
                </div>

                <div id="contractureDetails" style="display: <?php echo ($jci_type === 'Contracture') ? 'block' : 'none'; ?>;">
                    <h5>Add Contracture Details</h5>
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th>Serial Number</th>
                                <th>Contracture Name</th>
                                <th>Labour Cost</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Delivery Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Grand Total Labour Cost</th>
                                <th>
                                    <input type="text" id="grandTotal" class="form-control" readonly value="0">
                                </th>
                                <th></th>
                                <th>
                                    <button type="button" class="btn btn-secondary btn-sm ms-2 mb-3" id="addRowBtn" title="Add Row">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update' : 'Save'; ?> Job Card</button>
            </form>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2-enabled').select2({
        placeholder: "Select a PO Number",
        allowClear: true,
        theme: "bootstrap4"
    });

    // Update sell order number on PO change
    $('#po_id').on('change', function() {
        var poId = $(this).val();
        if (poId) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>modules/jci/ajax_get_sell_order_number.php',
                type: 'POST',
                data: { po_id: poId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#sell_order_number').val(response.sell_order_number);
                    } else {
                        $('#sell_order_number').val('');
                    }
                },
                error: function() {
                    $('#sell_order_number').val('');
                }
            });
        } else {
            $('#sell_order_number').val('');
        }
    });

    // Trigger change event on page load to set initial sell order number
    $('#po_id').trigger('change');

    let rowCount = 0;

    function calculateRowTotal(row) {
        const costInput = row.querySelector('.labour-cost');
        const qtyInput = row.querySelector('.item-qty');
        const totalInput = row.querySelector('.item-total');

        const cost = parseFloat(costInput.value) || 0;
        const qty = parseFloat(qtyInput.value) || 0;
        const total = cost * qty;

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

    function addRow(contractureName = '', labourCost = '', qty = '', total = '', deliveryDate = '') {
        rowCount++;
        const tbody = document.querySelector('#itemsTable tbody');
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" name="contracture_name[]" class="form-control" value="${contractureName}" required></td>
            <td><input type="number" name="labour_cost[]" class="form-control labour-cost" step="0.01" min="0" value="${labourCost}" required></td>
            <td><input type="number" name="quantity[]" class="form-control item-qty" step="1" min="0" value="${qty}" required></td>
            <td><input type="text" name="total[]" class="form-control item-total" readonly value="${total || 0}"></td>
            <td><input type="date" name="delivery_date[]" class="form-control" value="${deliveryDate}" required></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRowBtn" title="Delete Row"><i class="fas fa-trash"></i></button></td>
        `;

        tbody.appendChild(tr);

        tr.querySelector('.labour-cost').addEventListener('input', () => calculateRowTotal(tr));
        tr.querySelector('.item-qty').addEventListener('input', () => calculateRowTotal(tr));
        tr.querySelector('.removeRowBtn').addEventListener('click', () => {
            tr.remove();
            calculateGrandTotal();
            updateSerialNumbers();
        });

        calculateRowTotal(tr);
        updateSerialNumbers();
    }

    function updateSerialNumbers() {
        const rows = document.querySelectorAll('#itemsTable tbody tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
    }

    const jciTypeSelect = document.getElementById('jci_type');
    const contractureDetailsDiv = document.getElementById('contractureDetails');

    jciTypeSelect.addEventListener('change', function() {
        if (this.value === 'Contracture') {
            contractureDetailsDiv.style.display = 'block';
            if (document.querySelectorAll('#itemsTable tbody tr').length === 0) {
                addRow();
            }
        } else {
            contractureDetailsDiv.style.display = 'none';
        }
    });

    <?php if ($edit_mode && !empty($item_data) && $jci_type === 'Contracture'): ?>
        <?php foreach ($item_data as $item): ?>
            addRow(
                "<?php echo htmlspecialchars($item['contracture_name']); ?>",
                "<?php echo htmlspecialchars($item['labour_cost']); ?>",
                "<?php echo htmlspecialchars($item['quantity']); ?>",
                "<?php echo htmlspecialchars($item['total_amount']); ?>",
                "<?php echo htmlspecialchars($item['delivery_date']); ?>"
            );
        <?php endforeach; ?>
    <?php else: ?>
        if (jciTypeSelect.value === 'Contracture') {
            addRow();
        }
    <?php endif; ?>
    calculateGrandTotal();

    document.getElementById('addRowBtn').addEventListener('click', function() {
        addRow();
    });

    document.getElementById('jciForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?php echo BASE_URL; ?>modules/jci/ajax_save_jci.php', {
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
                $('.select2-enabled').val('').trigger('change');
                document.querySelector('#itemsTable tbody').innerHTML = '';
                document.getElementById('jci_type').value = 'Contracture';
                contractureDetailsDiv.style.display = 'block';
                addRow();
                document.getElementById('grandTotal').value = '0.00';
                document.getElementById('jci_number').value = data.new_jci_number;
                document.getElementById('created_by').value = '<?php echo $_SESSION['user_name'] ?? ''; ?>';
                document.getElementById('jci_date').value = '<?php echo date('Y-m-d'); ?>';
            } else if (data.success && <?php echo $edit_mode ? 'true' : 'false'; ?>) {
                 setTimeout(() => {
                    window.location.href = '<?php echo BASE_URL; ?>modules/jci/index.php';
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
