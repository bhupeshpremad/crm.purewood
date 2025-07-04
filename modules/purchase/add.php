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

$purchase_id = $_GET['id'] ?? null;
$purchase_data = null;
$wood_data = [];
$glow_data = [];
$plynydf_data = [];
$hardware_data = [];

if ($purchase_id) {
    $stmt = $conn->prepare("SELECT * FROM purchase_main WHERE id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $purchase_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT woodtype, length_ft as length, width_ft as width, thickness_inch as thickness, quantity, price, cft, total FROM purchase_wood WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $wood_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT glowtype, quantity, price, total FROM purchase_glow WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $glow_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT quantity, width, length, price, total FROM purchase_plynydf WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $plynydf_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT itemname, quantity, price, totalprice FROM purchase_hardware WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $hardware_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid mb-5">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Add Purchase</h6>
        </div>
        <div class="card-body">
            <form id="purchaseForm" autocomplete="off">
                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="po_number" class="form-label">PO Number</label>
                        <select class="form-control" id="po_number" name="po_number" required>
                            <option value="">Select PO Number</option>
                            <?php
                            $stmt = $conn->prepare("SELECT id, po_number, sell_order_number, jci_number FROM po_main ORDER BY po_number ASC");
                            $stmt->execute();
                            $po_numbers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($po_numbers as $po) {
                                echo '<option value="' . htmlspecialchars($po['id']) . '" data-son="' . htmlspecialchars($po['sell_order_number']) . '" data-jci="' . htmlspecialchars($po['jci_number']) . '">' . htmlspecialchars($po['po_number']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label for="sell_order_number" class="form-label">Sell Order Number (SON)</label>
                        <input type="text" class="form-control" id="sell_order_number" name="sell_order_number" placeholder="Enter Sell Order Number (SON)" required readonly>
                    </div>
                    <div class="col-lg-4">
                        <label for="jci_number" class="form-label">JCI Number</label>
                        <input type="text" class="form-control" id="jci_number" name="jci_number" placeholder="Enter JCI Number" required readonly>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-3" id="purchaseTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="wood-tab" data-toggle="tab" href="#wood" role="tab" aria-controls="wood" aria-selected="true">Wood</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="glow-tab" data-toggle="tab" href="#glow" role="tab" aria-controls="glow" aria-selected="false">Glow</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="plynydf-tab" data-toggle="tab" href="#plynydf" role="tab" aria-controls="plynydf" aria-selected="false">PLY/NYDF</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="hardware-tab" data-toggle="tab" href="#hardware" role="tab" aria-controls="hardware" aria-selected="false">Hardware</a>
                    </li>
                </ul>
                <div class="tab-content" id="purchaseTabsContent">
                    <div class="tab-pane fade show active" id="wood" role="tabpanel" aria-labelledby="wood-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="woodTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Wood Type</th>
                                        <th>Length (ft)</th>
                                        <th>Width (inch)</th>
                                        <th>Thickness (inch)</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>CFT</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-end">Total Wood Amount:</th>
                                        <th>
                                            <input type="text" class="form-control" id="total_wood_amount" name="total_wood_amount" readonly>
                                        </th>
                                        <th class="text-center">
                                            <button type="button" class="btn btn-success btn-sm add-row-btn add-wood-row"><i class="fas fa-plus"></i></button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary mt-3 save-tab-btn" data-section="wood">Save Wood</button>
                    </div>
                    <div class="tab-pane fade" id="glow" role="tabpanel" aria-labelledby="glow-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="glowTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Glow Type</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Glow Amount:</th>
                                        <th><input type="text" class="form-control" id="total_glow_amount" name="total_glow_amount" readonly></th>
                                        <th class="text-center">
                                            <button type="button" class="btn btn-success btn-sm add-row-btn add-glow-row"><i class="fas fa-plus"></i></button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary mt-3 save-tab-btn" data-section="glow">Save Glow</button>
                    </div>
                    <div class="tab-pane fade" id="plynydf" role="tabpanel" aria-labelledby="plynydf-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="plynydfTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Quantity</th>
                                        <th>Width</th>
                                        <th>Length</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Total PLY/NYDF Amount:</th>
                                        <th><input type="text" class="form-control" id="total_plynydf_amount" name="total_plynydf_amount" readonly></th>
                                        <th class="text-center">
                                            <button type="button" class="btn btn-success btn-sm add-row-btn add-plynydf-row"><i class="fas fa-plus"></i></button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary mt-3 save-tab-btn" data-section="plynydf">Save PLY/NYDF</button>
                    </div>
                    <div class="tab-pane fade" id="hardware" role="tabpanel" aria-labelledby="hardware-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="hardwareTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Hardware Amount:</th>
                                        <th><input type="text" class="form-control" id="total_hardware_amount" name="total_hardware_amount" readonly></th>
                                        <th class="text-center">
                                            <button type="button" class="btn btn-success btn-sm add-row-btn add-hardware-row"><i class="fas fa-plus"></i></button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary mt-3 save-tab-btn" data-section="hardware">Save Hardware</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    .table th, .table td { vertical-align: middle; padding: 0.5rem; }
    .table input.form-control, .table select.form-control { border: 1px solid #ced4da; padding: 0.375rem 0.75rem; height: auto; }
    .add-row-btn, .remove-row { font-size: 0.9rem; padding: 0.3rem 0.6rem; line-height: 1; }
    .save-tab-btn { min-width: 120px; }
    .thead-light th { background-color: #f8f9fc; }
</style>

<script>
$(document).ready(function () {
    var sections = ['wood', 'glow', 'plynydf', 'hardware'];

    function calculateRowTotal(row, section) {
        var total = 0;
        if (section === 'wood') {
            var length = parseFloat(row.find('input[name$="[length]"]').val()) || 0;
            var width_inch = parseFloat(row.find('input[name$="[width]"]').val()) || 0;
            var thickness_inch = parseFloat(row.find('input[name$="[thickness]"]').val()) || 0;
            var quantity = parseFloat(row.find('input[name$="[quantity]"]').val()) || 0;
            var price = parseFloat(row.find('input[name$="[price]"]').val()) || 0;
            var width_ft = width_inch / 12;
            var thickness_ft = thickness_inch / 12;
            var cft = (length * width_ft * thickness_ft);
            var cftInput = row.find('input[name$="[cft]"]');
            if (cftInput.length) {
                cftInput.val((cft * quantity).toFixed(2));
            }
            total = price * quantity * cft;
        } else if (section === 'glow') {
            var quantity = parseFloat(row.find('input[name$="[quantity]"]').val()) || 0;
            var price = parseFloat(row.find('input[name$="[price]"]').val()) || 0;
            total = quantity * price;
        } else if (section === 'plynydf') {
            var quantity = parseFloat(row.find('input[name$="[quantity]"]').val()) || 0;
            var width = parseFloat(row.find('input[name$="[width]"]').val()) || 0;
            var length = parseFloat(row.find('input[name$="[length]"]').val()) || 0;
            var price = parseFloat(row.find('input[name$="[price]"]').val()) || 0;
            total = quantity * width * length * price;
        } else if (section === 'hardware') {
            var quantity = parseFloat(row.find('input[name$="[quantity]"]').val()) || 0;
            var price = parseFloat(row.find('input[name$="[price]"]').val()) || 0;
            total = quantity * price;
        }
        return total;
    }

    function updateTotals(section) {
        var tableBody = $('#' + section + 'Table tbody');
        var rows = tableBody.find('tr');
        var sectionTotal = 0;
        rows.each(function () {
            var row = $(this);
            var totalInput = row.find('input[name$="[total]"], input[name$="[totalprice]"]');
            if (totalInput.length) {
                var rowTotal = calculateRowTotal(row, section);
                totalInput.val(rowTotal.toFixed(2));
                sectionTotal += rowTotal;
            }
        });
        $('#total_' + section + '_amount').val(sectionTotal.toFixed(2));
        updateGrandTotal();
    }

    function updateGrandTotal() {
        var grandTotal = 0;
        sections.forEach(function (section) {
            var sectionTotal = parseFloat($('#total_' + section + '_amount').val()) || 0;
            grandTotal += sectionTotal;
        });
        $('#grand_total_amount').val(grandTotal.toFixed(2));
    }

    function updateRowNames(section) {
        var tbody = $('#' + section + 'Table tbody');
        var rows = tbody.find('tr');
        rows.each(function (index) {
            var inputs = $(this).find('input, select');
            inputs.each(function () {
                var name = $(this).attr('name');
                if (name) {
                    var nameParts = name.split('[');
                    if (nameParts.length > 2) {
                        $(this).attr('name', section + '[' + index + '][' + nameParts[2].replace(']', '') + ']');
                    }
                }
            });
        });
    }

    sections.forEach(function (section) {
        $('#' + section + 'Table tbody').on('input', 'input', function () {
            updateTotals(section);
        });
    });

    $('.add-row-btn').on('click', function () {
        var section = $(this).closest('table').attr('id').replace('Table', '');
        var tableBody = $('#' + section + 'Table tbody');
        var rowCount = tableBody.find('tr').length;
        var html = '<tr>';
        if (section === 'wood') {
            html += '<td><select name="wood[' + rowCount + '][woodtype]" class="form-control" required><option value="">Select Wood Type</option><option value="Mango">Mango</option><option value="Babool">Babool</option><option value="Oak">Oak</option></select></td>';
            html += '<td><input type="number" step="0.01" name="wood[' + rowCount + '][length]" class="form-control" required placeholder="Feet"></td>';
            html += '<td><input type="number" step="0.01" name="wood[' + rowCount + '][width]" class="form-control" required placeholder="Inch"></td>';
            html += '<td><input type="number" step="0.01" name="wood[' + rowCount + '][thickness]" class="form-control" required placeholder="Inch"></td>';
            html += '<td><input type="number" step="0.01" name="wood[' + rowCount + '][quantity]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="wood[' + rowCount + '][price]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="wood[' + rowCount + '][cft]" class="form-control" readonly></td>';
            html += '<td><input type="number" step="0.01" name="wood[' + rowCount + '][total]" class="form-control" readonly></td>';
            html += '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>';
        } else if (section === 'glow') {
            html += '<td><input type="text" name="glow[' + rowCount + '][glowtype]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="glow[' + rowCount + '][quantity]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="glow[' + rowCount + '][price]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="glow[' + rowCount + '][total]" class="form-control" readonly></td>';
            html += '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>';
        } else if (section === 'plynydf') {
            html += '<td><input type="number" step="0.01" name="plynydf[' + rowCount + '][quantity]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="plynydf[' + rowCount + '][width]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="plynydf[' + rowCount + '][length]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="plynydf[' + rowCount + '][price]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="plynydf[' + rowCount + '][total]" class="form-control" readonly></td>';
            html += '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>';
        } else if (section === 'hardware') {
            html += '<td><input type="text" name="hardware[' + rowCount + '][itemname]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="hardware[' + rowCount + '][quantity]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="hardware[' + rowCount + '][price]" class="form-control" required></td>';
            html += '<td><input type="number" step="0.01" name="hardware[' + rowCount + '][totalprice]" class="form-control" readonly></td>';
            html += '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>';
        }
        html += '</tr>';
        tableBody.append(html);
        updateRowNames(section);
        updateTotals(section);
    });

    sections.forEach(function (section) {
        $('#' + section + 'Table tbody').on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
            updateRowNames(section);
            updateTotals(section);
        });
    });

    $('.save-tab-btn').on('click', function () {
        var section = $(this).data('section');
        var formData = $('#purchaseForm').serializeArray();

        var relevantData = {};
        formData.forEach(function(item) {
            if (item.name === 'po_number' || item.name === 'sell_order_number' || item.name === 'jci_number') {
                relevantData[item.name] = item.value;
            }
            if (item.name.startsWith(section + '[')) {
                var match = item.name.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
                if (match) {
                    var mainKey = match[1];
                    var index = match[2];
                    var field = match[3];

                    if (!relevantData[mainKey]) {
                        relevantData[mainKey] = {};
                    }
                    if (!relevantData[mainKey][index]) {
                        relevantData[mainKey][index] = {};
                    }
                    relevantData[mainKey][index][field] = item.value;
                }
            }
        });

        for (var key in relevantData) {
            if (relevantData.hasOwnProperty(key) && typeof relevantData[key] === 'object' && !Array.isArray(relevantData[key])) {
                if (key === 'wood' || key === 'glow' || key === 'plynydf' || key === 'hardware') {
                    relevantData[key] = Object.values(relevantData[key]);
                }
            }
        }
        
        var purchaseId = <?php echo json_encode($purchase_id); ?>;
        if (purchaseId) {
            relevantData['purchase_id'] = purchaseId;
        }

        $.ajax({
            url: 'process_purchase.php?section=' + section,
            type: 'POST',
            data: JSON.stringify(relevantData),
            contentType: 'application/json',
            success: function (response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    if (response.purchase_id && !$('#purchase_id').val()) {
                        $('#purchase_id').val(response.purchase_id);
                    }
                } else {
                    toastr.error(response.message || 'An error occurred.');
                }
            },
            error: function (xhr, status, error) {
                toastr.error('AJAX error: ' + error);
                console.error('AJAX Error:', status, error, xhr.responseText);
            }
        });
    });

    sections.forEach(function (section) {
        updateTotals(section);
    });

    <?php if ($purchase_data): ?>
        $('#po_number').val('<?php echo addslashes($purchase_data['po_main_id']); ?>');
        var selectedOption = $('#po_number').find('option:selected');
        var son = selectedOption.data('son') || '';
        var jci = selectedOption.data('jci') || '';
        $('#sell_order_number').val(son);
        $('#jci_number').val(jci);
    <?php endif; ?>

    $('#po_number').on('change', function () {
        var selectedOption = $(this).find('option:selected');
        var son = selectedOption.data('son') || '';
        var jci = selectedOption.data('jci') || '';
        $('#sell_order_number').val(son);
        $('#jci_number').val(jci);
    });

    $('#woodTable').on('keydown', 'input[name$="[length]"], input[name$="[thickness]"], input[name$="[width]"]', function(e) {
        var key = e.key;
        if (key === "ArrowUp" || key === "ArrowDown") {
            e.preventDefault();
            var step = 1;
            var currentVal = parseFloat($(this).val()) || 0;
            if (key === "ArrowUp") {
                currentVal = Math.ceil(currentVal);
                $(this).val(currentVal + step);
            } else if (key === "ArrowDown") {
                currentVal = Math.floor(currentVal);
                var newVal = currentVal - step;
                if ($(this).attr('name').endsWith('[width]') && newVal < 3) {
                    newVal = 3;
                }
                $(this).val(newVal);
            }
            $(this).trigger('input');
        }
    });

    <?php if (!empty($wood_data)): ?>
        var woodRows = '';
        <?php foreach ($wood_data as $index => $row): ?>
            woodRows += '<tr>';
            woodRows += '<td><select name="wood[<?php echo $index; ?>][woodtype]" class="form-control" required>';
            woodRows += '<option value="">Select Wood Type</option>';
            woodRows += '<option value="Mango" <?php echo ($row['woodtype'] == 'Mango') ? "selected" : ""; ?>>Mango</option>';
            woodRows += '<option value="Babool" <?php echo ($row['woodtype'] == 'Babool') ? "selected" : ""; ?>>Babool</option>';
            woodRows += '<option value="Oak" <?php echo ($row['woodtype'] == 'Oak') ? "selected" : ""; ?>>Oak</option>';
            woodRows += '</select></td>';
            woodRows += '<td><input type="number" step="0.01" name="wood[<?php echo $index; ?>][length]" class="form-control" required placeholder="Feet" value="<?php echo $row['length']; ?>"></td>';
            woodRows += '<td><input type="number" step="0.01" name="wood[<?php echo $index; ?>][width]" class="form-control" required placeholder="Inch" value="<?php echo $row['width']; ?>"></td>';
            woodRows += '<td><input type="number" step="0.01" name="wood[<?php echo $index; ?>][thickness]" class="form-control" required placeholder="Inch" value="<?php echo $row['thickness']; ?>"></td>';
            woodRows += '<td><input type="number" step="0.01" name="wood[<?php echo $index; ?>][quantity]" class="form-control" required value="<?php echo $row['quantity']; ?>"></td>';
            woodRows += '<td><input type="number" step="0.01" name="wood[<?php echo $index; ?>][price]" class="form-control" required value="<?php echo $row['price']; ?>"></td>';
            woodRows += '<td><input type="number" step="0.01" name="wood[<?php echo $index; ?>][cft]" class="form-control" readonly value="<?php echo $row['cft']; ?>"></td>';
            woodRows += '<td><input type="number" step="0.01" name="wood[<?php echo $index; ?>][total]" class="form-control" readonly value="<?php echo $row['total']; ?>"></td>';
            woodRows += '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>';
            woodRows += '</tr>';
        <?php endforeach; ?>
        $('#woodTable tbody').html(woodRows);
    <?php endif; ?>

    <?php if (!empty($glow_data)): ?>
        var glowRows = '';
        <?php foreach ($glow_data as $index => $row): ?>
            glowRows += '<tr>';
            glowRows += '<td><input type="text" name="glow[<?php echo $index; ?>][glowtype]" class="form-control" required value="<?php echo $row['glowtype']; ?>"></td>';
            glowRows += '<td><input type="number" step="0.01" name="glow[<?php echo $index; ?>][quantity]" class="form-control" required value="<?php echo $row['quantity']; ?>"></td>';
            glowRows += '<td><input type="number" step="0.01" name="glow[<?php echo $index; ?>][price]" class="form-control" required value="<?php echo $row['price']; ?>"></td>';
            glowRows += '<td><input type="number" step="0.01" name="glow[<?php echo $index; ?>][total]" class="form-control" readonly value="<?php echo $row['total']; ?>"></td>';
            glowRows += '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>';
            glowRows += '</tr>';
        <?php endforeach; ?>
        $('#glowTable tbody').html(glowRows);
    <?php endif; ?>

    <?php if (!empty($plynydf_data)): ?>
        var plynydfRows = '';
        <?php foreach ($plynydf_data as $index => $row): ?>
            plynydfRows += '<tr>';
            plynydfRows += '<td><input type="number" step="0.01" name="plynydf[<?php echo $index; ?>][quantity]" class="form-control" required value="<?php echo $row['quantity']; ?>"></td>';
            plynydfRows += '<td><input type="number" step="0.01" name="plynydf[<?php echo $index; ?>][width]" class="form-control" required value="<?php echo $row['width']; ?>"></td>';
            plynydfRows += '<td><input type="number" step="0.01" name="plynydf[<?php echo $index; ?>][length]" class="form-control" required value="<?php echo $row['length']; ?>"></td>';
            plynydfRows += '<td><input type="number" step="0.01" name="plynydf[<?php echo $index; ?>][price]" class="form-control" required value="<?php echo $row['price']; ?>"></td>';
            plynydfRows += '<td><input type="number" step="0.01" name="plynydf[<?php echo $index; ?>][total]" class="form-control" readonly value="<?php echo $row['total']; ?>"></td>';
            plynydfRows += '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>';
            plynydfRows += '</tr>';
        <?php endforeach; ?>
        $('#plynydfTable tbody').html(plynydfRows);
    <?php endif; ?>

    <?php if (!empty($hardware_data)): ?>
        var hardwareRows = '';
        <?php foreach ($hardware_data as $index => $row): ?>
            hardwareRows += '<tr>';
            hardwareRows += '<td><input type="text" name="hardware[<?php echo $index; ?>][itemname]" class="form-control" required value="<?php echo $row['itemname']; ?>"></td>';
            hardwareRows += '<td><input type="number" step="0.01" name="hardware[<?php echo $index; ?>][quantity]" class="form-control" required value="<?php echo $row['quantity']; ?>"></td>';
            hardwareRows += '<td><input type="number" step="0.01" name="hardware[<?php echo $index; ?>][price]" class="form-control" required value="<?php echo $row['price']; ?>"></td>';
            hardwareRows += '<td><input type="number" step="0.01" name="hardware[<?php echo $index; ?>][totalprice]" class="form-control" readonly value="<?php echo $row['totalprice']; ?>"></td>';
            hardwareRows += '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>';
            hardwareRows += '</tr>';
        <?php endforeach; ?>
        $('#hardwareTable tbody').html(hardwareRows);
    <?php endif; ?>

});
</script>
<?php
include_once ROOT_DIR_PATH . 'include/inc/footer.php';