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

// Check if this is edit mode
$edit_mode = false;
$purchase_id = $_GET['id'] ?? null;
$purchase_data = [];
$purchase_items = [];

if ($purchase_id) {
    $edit_mode = true;
    try {
        // Fetch purchase main data
        $stmt = $conn->prepare("SELECT * FROM purchase_main WHERE id = ?");
        $stmt->execute([$purchase_id]);
        $purchase_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($purchase_data) {
            // Fetch purchase items
            $stmt_items = $conn->prepare("SELECT * FROM purchase_items WHERE purchase_main_id = ?");
            $stmt_items->execute([$purchase_id]);
            $purchase_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        echo "Error loading purchase data: " . $e->getMessage();
    }
}

try {
    // Fetch JCIs with complete sell order numbers
    if ($edit_mode && !empty($purchase_data['jci_number'])) {
        $stmt = $conn->prepare("SELECT j.jci_number, j.po_id, j.bom_id, 
                                      COALESCE(so.sell_order_number, TRIM(p.sell_order_number), 'N/A') as sell_order_number
                               FROM jci_main j 
                               LEFT JOIN po_main p ON j.po_id = p.id 
                               LEFT JOIN sell_order so ON p.id = so.po_id 
                               WHERE j.purchase_created = 0 OR j.jci_number = ? 
                               ORDER BY j.jci_number DESC");
        $stmt->execute([$purchase_data['jci_number']]);
    } else {
        $stmt = $conn->query("SELECT j.jci_number, j.po_id, j.bom_id, 
                                    COALESCE(so.sell_order_number, TRIM(p.sell_order_number), 'N/A') as sell_order_number
                             FROM jci_main j 
                             LEFT JOIN po_main p ON j.po_id = p.id 
                             LEFT JOIN sell_order so ON p.id = so.po_id 
                             WHERE j.purchase_created = 0 
                             ORDER BY j.jci_number DESC");
    }
    $jci_numbers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database query error: " . $e->getMessage();
}

?>

<div class="container-fluid mb-5">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $edit_mode ? 'Edit Purchase Details' : 'Add Purchase Details'; ?></h6>
        </div>
        <div class="card-body">
            <form id="purchaseDetailsForm">
                <input type="hidden" id="purchase_id" name="purchase_id" value="<?php echo htmlspecialchars($purchase_id ?? ''); ?>">
                <input type="hidden" id="po_number" name="po_number" value="<?php echo htmlspecialchars($purchase_data['po_number'] ?? ''); ?>">
                <input type="hidden" id="sell_order_number" name="sell_order_number" value="<?php echo htmlspecialchars($purchase_data['sell_order_number'] ?? ''); ?>">
                <input type="hidden" id="jci_number" name="jci_number" value="<?php echo htmlspecialchars($purchase_data['jci_number'] ?? ''); ?>">

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="jci_number_search">JCI Card Number:</label>
                        <select id="jci_number_search" class="form-control" required>
                            <option value="">Select Job Card Number</option>
                            <?php foreach ($jci_numbers as $jci): ?>
                                <option value="<?php echo htmlspecialchars($jci['jci_number']); ?>"
                                    data-po-id="<?php echo htmlspecialchars($jci['po_id']); ?>"
                                    data-son="<?php echo htmlspecialchars($jci['sell_order_number']); ?>"
                                    <?php echo (isset($purchase_data['jci_number']) && $purchase_data['jci_number'] == $jci['jci_number']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($jci['jci_number']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small id="jobCardCount" class="form-text text-muted mt-1"></small>
                        <ul id="jobCardList" class="list-group mt-1" style="max-height: 150px; overflow-y: auto;"></ul>
                    </div>
<div class="form-group col-md-4">
    <label for="sell_order_number_display">Sell Order Number (SON):</label>
    <input type="text" id="sell_order_number_display" class="form-control" readonly style="text-align: left; width: 100%; overflow: visible;"
           value="<?php echo htmlspecialchars(trim($purchase_data['sell_order_number'] ?? '')); ?>">
</div>
                    <div class="form-group col-md-4">
                        <label for="po_number_display">PO Number:</label>
                        <input type="text" id="po_number_display" class="form-control" readonly
                               value="<?php echo htmlspecialchars($purchase_data['po_number'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="bom_number_display">BOM Number(s):</label>
                        <input type="text" id="bom_number_display" class="form-control" readonly>
                    </div>
                </div>
            </form>
            <div class="form-group mt-3">
                <button type="submit" form="purchaseDetailsForm" class="btn btn-primary">Save Purchase</button>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">BOM Details</h6>
                </div>
                <div class="card-body" id="bomTableContainer">
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    #toast-container > .toast-success {
        background-color: #51a351 !important;
        color: white !important;
    }
    #toast-container > .toast-error {
        background-color: #bd362f !important;
        color: white !important;
    }
    .table th, .table td { vertical-align: middle; padding: 0.5rem; }
    .table input.form-control, .table select.form-control { border: 1px solid #ced4da; padding: 0.375rem 0.75rem; height: auto; }
</style>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
$('#jci_number_search').on('change', function() {
    var selectedJciNumber = $(this).val();
    var selectedOption = $(this).find('option:selected');
    var poId = selectedOption.data('po-id');
    var sellOrderNumber = selectedOption.data('son');

    var cleanSellOrderNumber = sellOrderNumber ? sellOrderNumber.toString().trim() : '';
    $('#sell_order_number_display').val(cleanSellOrderNumber);
    $('#sell_order_number').val(cleanSellOrderNumber);
    $('#purchase_id').val('');
    $('#po_number').val('');
    $('#sell_order_number').val(sellOrderNumber);
    $('#jci_number').val(selectedJciNumber);
    $('#bomTableContainer').empty();

    if (!poId) {
        $('#po_number_display').val('');
        $('#bom_number_display').val('');
        return;
    }

    $.ajax({
        url: 'ajax_fetch_po_number.php',
        method: 'POST',
        data: { po_id: poId },
        dataType: 'json',
        success: function(poData) {
            if (poData && poData.po_number) {
                $('#po_number_display').val(poData.po_number);
                $('#po_number').val(poData.po_number);
            } else {
                $('#po_number_display').val('');
                $('#po_number').val('');
            }
        },
        error: function() {
            $('#po_number_display').val('');
        }
    });

    $.ajax({
        url: 'ajax_fetch_job_cards.php',
        method: 'POST',
        data: { jci_number: selectedJciNumber },
        dataType: 'json',
        success: function(jobCardsData) {
            if (jobCardsData && jobCardsData.job_cards && jobCardsData.job_cards.length > 0) {
                $('#bomTableContainer').empty();
                var jobCards = jobCardsData.job_cards;
                var jobCardCount = jobCards.length;

                // Display job card count and list
                $('#jobCardCount').text('Job Cards Count: ' + jobCardCount);
                $('#jobCardList').empty();
                jobCards.forEach(function(card) {
                    $('#jobCardList').append('<li class="list-group-item p-1">' + card + '</li>');
                });

                // Update BOM Number display for the selected JCI
                $.ajax({
                    url: 'ajax_get_bom_number_by_jci.php',
                    method: 'POST',
                    data: { jci_number: selectedJciNumber },
                    dataType: 'json',
                    success: function(bomData) {
                        if (bomData && bomData.bom_number) {
                            $('#bom_number_display').val(bomData.bom_number);
                        } else {
                            $('#bom_number_display').val('');
                        }
                    },
                    error: function() {
                        $('#bom_number_display').val('');
                    }
                });

                // Fetch BOM items once for the JCI number
                $.ajax({
                    url: 'ajax_fetch_bom_items_by_job_card.php',
                    method: 'POST',
                    data: { jci_number: selectedJciNumber },
                    dataType: 'json',
                    success: function(bomItemsData) {
                        console.log('BOM Items Data:', bomItemsData);
                        if (bomItemsData && bomItemsData.length > 0) {
                            toastr.info('BOM items found: ' + bomItemsData.length);
                            // Clear previous BOM tables
                            $('#bomTableContainer').empty();

                            // Check for existing purchase data first
                            $.ajax({
                                url: 'ajax_fetch_saved_purchase.php',
                                method: 'POST',
                                data: { jci_number: selectedJciNumber },
                                dataType: 'json',
                                success: function(purchaseData) {
                                    console.log('Existing purchase data:', purchaseData);
                                    var existingItems = purchaseData.has_purchase ? purchaseData.purchase_items : [];
                                    
                                    // For each job card, render a table with BOM items and inputs for assign quantity and supplier name
                                    jobCards.forEach(function(jobCard) {
                                var table = $('<table class="table table-bordered table-sm mb-4"></table>');
                                var thead = $('<thead class="thead-light"></thead>');
                                var tbody = $('<tbody></tbody>');

                                var headerRow = $('<tr><th colspan="8">Job Card: ' + jobCard + '</th></tr>');
                                thead.append(headerRow);

var colHeaderRow = $('<tr></tr>');
colHeaderRow.append('<th><input type="checkbox" id="selectAllRows"></th>');
colHeaderRow.append('<th>Supplier Name</th>');
colHeaderRow.append('<th>Product Type</th>');
colHeaderRow.append('<th>Product Name</th>');
colHeaderRow.append('<th>BOM Quantity</th>');
colHeaderRow.append('<th>BOM Price</th>');
colHeaderRow.append('<th>Assign Quantity</th>');
colHeaderRow.append('<th>Status</th>');
thead.append(colHeaderRow);

    bomItemsData.forEach(function(item) {
        var tr = $('<tr></tr>');
        
        // Find existing purchase item data for this BOM item
        var existingItem = null;
        if (existingItems && existingItems.length > 0) {
            existingItem = existingItems.find(function(pItem) {
                return pItem.product_type === item.product_type && 
                       (pItem.product_name === item.product_name || (item.product_name === '' && pItem.product_name === item.product_type)) &&
                       pItem.job_card_number === jobCard;
            });
        }
        
        var supplierName = existingItem ? (existingItem.supplier_name || '').toString().trim() : '';
        var assignedQty = existingItem ? existingItem.assigned_quantity : '0';
        var isChecked = existingItem ? true : false;
        var isApproved = existingItem && existingItem.invoice_number ? true : false;
        
        console.log('Matching for item:', item, 'Found existing:', existingItem, 'Supplier:', supplierName, 'Approved:', isApproved);

tr.append('<td><input type="checkbox" class="rowCheckbox" ' + (isChecked ? 'checked' : '') + ' ' + (isApproved ? 'disabled' : '') + '></td>');
tr.append('<td><input type="text" class="form-control form-control-sm supplierNameInput" value="' + (supplierName || '').toString().replace(/"/g, '&quot;') + '" ' + (isApproved ? 'readonly' : '') + '></td>');
tr.append('<td><input type="text" class="form-control form-control-sm productTypeInput" value="' + item.product_type + '" readonly></td>');
tr.append('<td><input type="text" class="form-control form-control-sm productNameInput" value="' + item.product_name + '" readonly></td>');
tr.append('<td><input type="number" class="form-control form-control-sm bomQuantityInput" value="' + item.quantity + '" readonly></td>');
tr.append('<td><input type="number" class="form-control form-control-sm bomPriceInput" value="' + item.price + '" readonly></td>');
tr.append('<td><input type="number" min="0" max="' + item.quantity + '" step="0.001" class="form-control form-control-sm assignQuantityInput" value="' + assignedQty + '" ' + (isApproved ? 'readonly' : '') + '></td>');
if (isApproved) {
    tr.addClass('table-success');
    tr.append('<td><span class="badge badge-success">Approved</span></td>');
} else {
    tr.append('<td><span class="badge badge-warning">Pending</span></td>');
}

        tbody.append(tr);
    });

                                        table.append(thead);
                                        table.append(tbody);
                                        $('#bomTableContainer').append(table);
                                    });
                                },
                                error: function() {
                                    console.log('Error fetching existing purchase data');
                                    var existingItems = [];
                                    
                                    // Fallback: render tables without existing data
                                    jobCards.forEach(function(jobCard) {
                                        // Same table rendering logic as above but with empty existingItems
                                        var table = $('<table class="table table-bordered table-sm mb-4"></table>');
                                        var thead = $('<thead class="thead-light"></thead>');
                                        var tbody = $('<tbody></tbody>');

                                        var headerRow = $('<tr><th colspan="8">Job Card: ' + jobCard + '</th></tr>');
                                        thead.append(headerRow);

                                        var colHeaderRow = $('<tr></tr>');
                                        colHeaderRow.append('<th><input type="checkbox" id="selectAllRows"></th>');
                                        colHeaderRow.append('<th>Supplier Name</th>');
                                        colHeaderRow.append('<th>Product Type</th>');
                                        colHeaderRow.append('<th>Product Name</th>');
                                        colHeaderRow.append('<th>Quantity</th>');
                                        colHeaderRow.append('<th>Assign Quantity</th>');
                                        colHeaderRow.append('<th>Status</th>');
                                        thead.append(colHeaderRow);

                                        bomItemsData.forEach(function(item) {
                                            var tr = $('<tr></tr>');
                                            tr.append('<td><input type="checkbox" class="rowCheckbox"></td>');
                                            tr.append('<td><input type="text" class="form-control form-control-sm supplierNameInput" value=""></td>');
                                            tr.append('<td><input type="text" class="form-control form-control-sm productTypeInput" value="' + item.product_type + '" readonly></td>');
                                            tr.append('<td><input type="text" class="form-control form-control-sm productNameInput" value="' + item.product_name + '" readonly></td>');
                                            tr.append('<td><input type="number" class="form-control form-control-sm" value="' + item.quantity + '" readonly></td>');
                                            tr.append('<td><input type="number" min="0" max="' + item.quantity + '" step="0.001" class="form-control form-control-sm assignQuantityInput" value="0"></td>');
                                            tr.append('<td><span class="badge badge-warning">Pending</span></td>');
                                            tbody.append(tr);
                                        });

                                        table.append(thead);
                                        table.append(tbody);
                                        $('#bomTableContainer').append(table);
                                    });
                                }
                            });
                        } else {
                            console.log('No BOM items found for JCI:', selectedJciNumber);
                            toastr.warning('No BOM items found for the selected JCI.');
                        }
                    },
                    error: function() {
                        console.log('Error fetching BOM items for JCI:', selectedJciNumber);
                        toastr.error('Error fetching BOM items for the selected JCI.');
                    }
                });
            } else if (jobCardsData && jobCardsData.error) {
                toastr.error('Error fetching job cards: ' + jobCardsData.error);
                $('#bomTableContainer').empty();
                $('#jobCardCount').text('');
                $('#jobCardList').empty();
            } else {
                $('#bomTableContainer').empty();
                $('#jobCardCount').text('');
                $('#jobCardList').empty();
            }
        },
        error: function() {
            toastr.error('AJAX error fetching job cards');
            $('#bomTableContainer').empty();
            $('#jobCardCount').text('');
            $('#jobCardList').empty();
        }
    });
});

function getTableColumnIndex(categoryName, keyName) {
    var baseOffset = 1; // Sr. No. column only, removed checkbox column
    var headerKeys = [];

    var columnOrder = {
        'BOM Glow': ['supplier_name', 'glowtype', 'quantity', 'price', 'total'],
        'BOM Hardware': ['supplier_name', 'itemname', 'quantity', 'price', 'totalprice'],
        'BOM Plynydf': ['supplier_name', 'quantity', 'width', 'length', 'price', 'total'],
        'BOM Wood': ['supplier_name', 'woodtype', 'length_ft', 'width_ft', 'thickness_inch', 'quantity', 'price', 'cft', 'total']
    };

    if (categoryName === 'Unknown') {
        // Skip processing for unknown category
        return -1;
    }

    if (columnOrder[categoryName]) {
        headerKeys = columnOrder[categoryName];
    } else {
        console.warn("Unknown category for getTableColumnIndex:", categoryName);
        return -1;
    }

    var dataColIndex = headerKeys.indexOf(keyName);
    if (dataColIndex !== -1) {
        return baseOffset + dataColIndex;
    }

    var quantityIndex = headerKeys.indexOf('quantity');
    if (quantityIndex === -1 && categoryName === 'BOM Plynydf') { // For Plynydf, 'total' might act as quantity
        quantityIndex = headerKeys.indexOf('total');
    }

    // Adjust for job card and assigned quantity columns, which appear after the main data columns
    if (quantityIndex !== -1) {
        if (keyName === 'job_card_select') {
            return baseOffset + quantityIndex + 1; // +1 because it's after the quantity/total column
        }
        if (keyName === 'assigned_quantity_input') {
            return baseOffset + quantityIndex + 2; // +2 because it's after quantity/total and job_card_select
        }
    }

    console.warn("Could not find column index for:", keyName, "in category:", categoryName);
    return -1;
}

// Updated createBomTable function to accept jobCardCount
function createBomTable(categoryName, data, savedItems, jobCardCount) {
    if (!data || data.length === 0) {
        return;
    }
    var table = $('<table class="table table-bordered table-sm mb-4"></table>');
    var thead = $('<thead class="thead-light"></thead>');
    var tbody = $('<tbody></tbody>');

    var totalColumns = Object.keys(data[0]).length + 2; // Account for checkbox and Sr. No.
    totalColumns -= (Object.keys(data[0]).includes('bom_main_id') ? 1 : 0); // Exclude bom_main_id from column count
    totalColumns -= (Object.keys(data[0]).includes('id') ? 1 : 0); // Exclude id from column count

    var headerMappings = {
        'BOM Glow': {
            'supplier_name': 'Supplier Name', 'glowtype': 'Glow Type', 'quantity': 'Quantity', 'price': 'Price', 'total': 'Total'
        },
        'BOM Hardware': {
            'supplier_name': 'Supplier Name', 'itemname': 'Item Name', 'quantity': 'Quantity', 'price': 'Price', 'totalprice': 'Total Price'
        },
        'BOM Plynydf': {
            'supplier_name': 'Supplier Name', 'quantity': 'Quantity', 'width': 'Width', 'length': 'Length', 'price': 'Price', 'total': 'Total'
        },
        'BOM Wood': {
            'supplier_name': 'Supplier Name', 'woodtype': 'Wood Type', 'length_ft': 'Length Ft', 'width_ft': 'Width Ft', 'thickness_inch': 'Thickness Inch', 'quantity': 'Quantity', 'price': 'Price', 'cft': 'CFT', 'total': 'Total'
        }
    };

    var currentCategoryHeaders = headerMappings[categoryName] || {};
    var headerKeys = Object.keys(currentCategoryHeaders);

    var firstDataRowKeys = Object.keys(data[0]);
    var hasQuantityOrTotal = firstDataRowKeys.includes('quantity') || (categoryName.includes('BOM Plynydf') && firstDataRowKeys.includes('total'));
    if (hasQuantityOrTotal) {
        totalColumns += 2; // Add columns for "Select Job Card" and "Assigned Quantity"
    }

    var headerRow = $('<tr><th colspan="' + totalColumns + '">' + categoryName + '</th></tr>');
    thead.append(headerRow);

    var colHeaderRow = $('<tr></tr>');
    colHeaderRow.append('<th><input type="checkbox" class="selectAllRows"></th>');
    colHeaderRow.append('<th>Sr. No.</th>');

    headerKeys.forEach(function(key) {
        if (key === 'bom_main_id' || key === 'id') {
            return;
        }
        colHeaderRow.append('<th>' + currentCategoryHeaders[key] + '</th>');
    });

    if (hasQuantityOrTotal) {
        colHeaderRow.append('<th>Select Job Card</th>');
        colHeaderRow.append('<th>Assigned Quantity</th>');
    }
    thead.append(colHeaderRow);

    // Use jobCardCount passed as argument
    // Remove the loop repeating rows by jobCardCount to avoid duplication
    // for (var repeat = 0; repeat < (jobCardCount || 1); repeat++) {
        data.forEach(function(row_data) {
            var tr = $('<tr></tr>');
            var checkboxTd = $('<td><input type="checkbox" class="rowCheckbox"></td>');
            tr.append(checkboxTd);

            var serialTd = $('<td></td>').text('');
            tr.append(serialTd);

            headerKeys.forEach(function(key) {
                if (key === 'bom_main_id' || key === 'id') {
                    return;
                }
                var td = $('<td></td>');
                var inputVal = row_data[key] !== undefined ? row_data[key] : '';

                if (key === 'supplier_name') {
                    td.text(inputVal);
                } else {
                    var input = $('<input type="text" class="form-control form-control-sm" readonly>').val(inputVal);
                    td.append(input);
                }
                tr.append(td);
            });

            if (hasQuantityOrTotal) {
                var jobCardTd = $('<td></td>');
                var jobCardSelect = $('<select class="form-control form-control-sm jobCardSelect"><option value="">Select Job Card</option></select>');
                jobCardTd.append(jobCardSelect);
                tr.append(jobCardTd);

                var assignedQtyTd = $('<td></td>');
                var assignedQtyInput = $('<input type="number" min="0" class="form-control form-control-sm assignedQtyInput" value="0">');
                assignedQtyTd.append(assignedQtyInput);
                tr.append(assignedQtyTd);
            }

            var matchedSavedItem = null;
            if (savedItems && savedItems.length > 0) {
                savedItems.some(function(savedItem) {
                    var match = false;
                    console.log("Matching savedItem:", savedItem, "with row_data:", row_data, "category:", categoryName);
                    if (categoryName.includes('Glow') && savedItem.product_type === 'Glow Type' && savedItem.product_name === row_data.glowtype && savedItem.supplier_name === row_data.supplier_name) {
                        match = true;
                    } else if (categoryName.includes('Hardware') && savedItem.product_type === 'Item Name' && savedItem.product_name === row_data.itemname && savedItem.supplier_name === row_data.supplier_name) {
                        match = true;
                    } else if (categoryName.includes('Plynydf') && savedItem.product_type === 'Plynydf' && savedItem.supplier_name === row_data.supplier_name && parseFloat(savedItem.assigned_quantity) === parseFloat(row_data.quantity) && parseFloat(savedItem.price) === parseFloat(row_data.price)) {
                        match = true;
                    } else if (categoryName.includes('Wood') && savedItem.product_type === 'Wood Type' && savedItem.product_name === row_data.woodtype && savedItem.supplier_name === row_data.supplier_name) {
                        match = true;
                    }

                    if (match) {
                        matchedSavedItem = savedItem;
                        return true;
                    }
                    return false;
                });
            }

            if (matchedSavedItem) {
                checkboxTd.find('.rowCheckbox').prop('checked', true);
                // Do not disable checkbox to allow user to uncheck if needed
                // checkboxTd.find('.rowCheckbox').prop('disabled', true);
                tr.find('.jobCardSelect').val(matchedSavedItem.job_card_number).prop('disabled', true);
                tr.find('.assignedQtyInput').val(matchedSavedItem.assigned_quantity).prop('readonly', true);

                var totalColIndex = getTableColumnIndex(categoryName, categoryName === 'BOM Hardware' ? 'totalprice' : 'total');
                if (totalColIndex !== -1) {
                    tr.find('td').eq(totalColIndex).find('input').val(matchedSavedItem.total);
                }
            }
            tbody.append(tr);
        });
    // }

    function updateSerialNumbers() {
        tbody.find('tr').each(function(index) {
            $(this).find('td').eq(1).text(index + 1);
        });
    }
    updateSerialNumbers();

    table.append(thead);
    table.append(tbody);
    $('#bomTableContainer').append(table);

    table.find('.selectAllRows').on('change', function() {
        var checked = $(this).is(':checked');
        $(this).closest('table').find('tbody .rowCheckbox:not(:disabled)').prop('checked', checked);
    });

    tbody.on('input', '.assignedQtyInput', function() {
        var row_dom = $(this).closest('tr');
        var tableElement = row_dom.closest('table');
        var category = tableElement.find('thead tr').first().text().trim();

        var originalQuantity = 0;
        var price = 0;

        var quantityColIdx = getTableColumnIndex(category, 'quantity');
        var priceColIdx = getTableColumnIndex(category, 'price');

        if (quantityColIdx !== -1) {
            originalQuantity = parseFloat(row_dom.find('td').eq(quantityColIdx).find('input').val());
        }
        if (priceColIdx !== -1) {
            price = parseFloat(row_dom.find('td').eq(priceColIdx).find('input').val());
        }

        var assignedQty = parseFloat($(this).val());

        if (isNaN(assignedQty) || assignedQty < 0) {
            $(this).val(0);
            assignedQty = 0;
        }

        if (assignedQty > originalQuantity + 0.001) { // Allow small floating point tolerance
            toastr.warning('Assigned quantity (' + assignedQty + ') exceeds BOM quantity (' + originalQuantity + '). Auto-correcting.');
            $(this).val(originalQuantity);
            assignedQty = originalQuantity;
        }

        var totalInputColIndex;
        if (category === 'BOM Hardware') {
            totalInputColIndex = getTableColumnIndex(category, 'totalprice');
        } else {
            totalInputColIndex = getTableColumnIndex(category, 'total');
        }

        if (totalInputColIndex !== -1) {
            var totalInput = row_dom.find('td').eq(totalInputColIndex).find('input');
            if (!isNaN(price) && !isNaN(assignedQty)) {
                totalInput.val((price * assignedQty).toFixed(2));
            } else {
                totalInput.val('');
            }
        }

        var totalAssignedInTable = 0;
        tableElement.find('tbody .assignedQtyInput').each(function() {
            var val = parseFloat($(this).val());
            if (!isNaN(val)) {
                totalAssignedInTable += val;
            }
        });

        var totalRow = tableElement.find('.totalAssignedRow');
        if (totalRow.length === 0) {
            totalRow = $('<tr class="totalAssignedRow table-info"><td colspan="' + (row_dom.find('td').length) + '" style="text-align:right; font-weight:bold;">Total Assigned Quantity: <span class="totalAssignedQty">0</span></td></tr>');
            tbody.append(totalRow);
        }
        totalRow.find('.totalAssignedQty').text(totalAssignedInTable);
    });
}

$('#purchaseDetailsForm').on('submit', function(e) {
    e.preventDefault();

    var po_number = $('#po_number').val();
    var jci_number = $('#jci_number').val();
    var sell_order_number = $('#sell_order_number').val();
    var bom_number = $('#bom_number_display').val();

    var items = [];
    var validationFailed = false;

    var bomItemTotals = {};

$('#bomTableContainer table').each(function() {
    // Extract BOM category name from table structure
    var categoryName = 'Unknown';
    var headerRows = $(this).find('thead tr');
    
    // First try to get from first header row (main category header)
    if (headerRows.length > 0) {
        var firstHeaderText = headerRows.eq(0).text().trim();
        if (firstHeaderText.startsWith('Job Card: ')) {
            // This is the new table structure with job cards
            // Check column headers to determine category
            var colHeaders = $(this).find('thead tr').eq(1).find('th');
            if (colHeaders.length > 2) {
                var productTypeHeader = colHeaders.eq(2).text().trim();
                if (productTypeHeader === 'Product Type') {
                    categoryName = 'Job Card Items';
                }
            }
        } else if (firstHeaderText.includes('BOM')) {
            // Old BOM table structure
            categoryName = firstHeaderText;
        } else {
            // Try to determine from column headers
            if (headerRows.length > 1) {
                var colHeaders = headerRows.eq(1).find('th');
                if (colHeaders.length > 2) {
                    var productTypeHeader = colHeaders.eq(2).text().trim();
                    if (productTypeHeader === 'Glow Type') {
                        categoryName = 'BOM Glow';
                    } else if (productTypeHeader === 'Item Name') {
                        categoryName = 'BOM Hardware';
                    } else if (productTypeHeader === 'Quantity') {
                        categoryName = 'BOM Plynydf';
                    } else if (productTypeHeader === 'Wood Type') {
                        categoryName = 'BOM Wood';
                    } else if (productTypeHeader === 'Product Type') {
                        categoryName = 'Job Card Items';
                    }
                }
            }
        }
    }
        // Store job card number in data attribute from first thead tr
        var headerText = headerRows.eq(0).text().trim();
        if (headerText.startsWith('Job Card: ')) {
            $(this).data('jobCardNumber', headerText.replace('Job Card: ', '').trim());
        }

        $(this).find('tbody tr').each(function() {
            var row = $(this);

            // Only process rows with checked checkbox
            if (!row.find('.rowCheckbox').is(':checked')) {
                return true; // continue to next row
            }

            // Get supplier name from the correct input field
            var supplier_name = '';
            if (categoryName === 'Job Card Items') {
                supplier_name = row.find('.supplierNameInput').val().trim();
            } else {
                supplier_name = row.find('td').eq(getTableColumnIndex(categoryName, 'supplier_name')).find('input').val().trim();
            }
            var product_type = '';
            var product_name = '';

            if (categoryName === 'Job Card Items') {
                // New table structure with job cards
                product_type = row.find('.productTypeInput').val() || '';
                product_name = row.find('.productNameInput').val() || '';
                
                // If product_name is empty, use product_type as product_name
                if (!product_name || product_name.trim() === '') {
                    product_name = product_type;
                }
            } else if (categoryName.includes('Glow')) {
                product_type = 'Glow Type';
                product_name = row.find('td').eq(getTableColumnIndex(categoryName, 'glowtype')).find('input').val();
            } else if (categoryName.includes('Hardware')) {
                product_type = 'Item Name';
                product_name = row.find('td').eq(getTableColumnIndex(categoryName, 'itemname')).find('input').val();
            } else if (categoryName.includes('Plynydf')) {
                product_type = 'Plynydf';
                product_name = 'Plynydf';
            } else if (categoryName.includes('Wood')) {
                product_type = 'Wood Type';
                product_name = row.find('td').eq(getTableColumnIndex(categoryName, 'woodtype')).find('input').val();
            } else {
                // Fallback for unknown categories
                product_type = 'Unknown';
                product_name = 'Unknown Item';
            }

            // Get job card number from table data attribute instead of select element
            var job_card_number = $(this).closest('table').data('jobCardNumber') || '';
            
            // Get assigned quantity from the correct input field
            var assigned_quantity_input_element = null;
            var assigned_quantity = '';
            if (categoryName === 'Job Card Items') {
                assigned_quantity_input_element = row.find('.assignQuantityInput');
                assigned_quantity = assigned_quantity_input_element.val();
            } else {
                assigned_quantity_input_element = row.find('td').eq(getTableColumnIndex(categoryName, 'assigned_quantity_input')).find('input');
                assigned_quantity = assigned_quantity_input_element.val();
            }
            
var price = '0';
if (categoryName === 'Job Card Items') {
    price = row.find('.bomPriceInput').val() || '0';
} else {
    var price_element = row.find('td').eq(getTableColumnIndex(categoryName, 'price')).find('input');
    price = price_element ? price_element.val() : '0';
}

            var total;
            if (categoryName === 'BOM Hardware') {
                total = row.find('td').eq(getTableColumnIndex(categoryName, 'totalprice')).find('input').val();
            } else {
                total = row.find('td').eq(getTableColumnIndex(categoryName, 'total')).find('input').val();
            }

            if (!job_card_number) {
                // If only one job card exists, auto select it
                var jobCards = $('#jci_number_search').data('jobCards') || [];
                if (jobCards.length === 1) {
                    job_card_number = jobCards[0];
                } else {
                    toastr.error('Please select a Job Card Number for all items.');
                    job_card_select_element.focus();
                    validationFailed = true;
                    return false;
                }
            }

            if ((isNaN(parseFloat(assigned_quantity)) || parseFloat(assigned_quantity) < 0) && supplier_name !== '') {
                toastr.error('Assigned quantity must be zero or a positive number for all items with supplier name.');
                assigned_quantity_input_element.focus();
                validationFailed = true;
                return false;
            }
            if (supplier_name === '' && parseFloat(assigned_quantity) > 0) {
                toastr.error('Supplier name is required if assigned quantity is greater than zero.');
                assigned_quantity_input_element.focus();
                validationFailed = true;
                return false;
            }
            
            // Validate product_type and product_name are not empty
            if (!product_type || !product_name) {
                console.log('Missing product data:', { product_type, product_name, categoryName });
                toastr.error('Product type and product name are required for category: ' + categoryName);
                validationFailed = true;
                return false;
            }

            // Simplified quantity validation - only check individual row quantity
            var bomQuantity = 0;
            var assignedQtyFloat = parseFloat(assigned_quantity) || 0;
            
            if (categoryName === 'Job Card Items') {
                bomQuantity = parseFloat(row.find('td').eq(4).find('input').val()) || 0; // 5th column is quantity
            } else {
                var quantityColIdx = getTableColumnIndex(categoryName, 'quantity');
                if (quantityColIdx !== -1) {
                    bomQuantity = parseFloat(row.find('td').eq(quantityColIdx).find('input').val()) || 0;
                }
            }
            
            // Only validate that assigned quantity doesn't exceed BOM quantity for this specific row
            if (assignedQtyFloat > bomQuantity + 0.001) {
                toastr.error('Assigned quantity (' + assignedQtyFloat + ') cannot exceed BOM quantity (' + bomQuantity + ') for ' + product_name);
                assigned_quantity_input_element.focus();
                validationFailed = true;
                return false;
            }

            // Debug logging
            console.log('Item data being saved:', {
                supplier_name: supplier_name,
                product_type: product_type,
                product_name: product_name,
                job_card_number: job_card_number,
                assigned_quantity: assigned_quantity,
                price: price,
                total: total,
                categoryName: categoryName
            });
            
            items.push({
                supplier_name: supplier_name,
                product_type: product_type,
                product_name: product_name,
                job_card_number: job_card_number,
                assigned_quantity: assigned_quantity,
                price: price,
                total: total
            });
        });

        if (validationFailed) {
            return false;
        }
    });

    if (validationFailed) {
        return;
    }

    console.log('Items to save:', items);

    if (items.length === 0) {
        toastr.warning('Please enter assigned quantity for at least one item.');
        return;
    }

    $.ajax({
        url: 'ajax_save_purchase.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            po_number: po_number,
            jci_number: jci_number,
            sell_order_number: sell_order_number,
            bom_number: bom_number,
            items: items
        }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#jci_number_search').trigger('change');
            } else {
                toastr.error(response.error || 'Unknown error occurred');
            }
        },
        error: function(xhr, status, error) {
            toastr.error('AJAX error: ' + error);
        }
    });
});

// Load existing data in edit mode
<?php if ($edit_mode && $purchase_data): ?>
    console.log('Edit mode detected, loading existing data');
    var existingData = <?php echo json_encode($purchase_data); ?>;
    var existingItems = <?php echo json_encode($purchase_items); ?>;
    
    // Set JCI dropdown value
    if (existingData.jci_number) {
        $('#jci_number_search').val(existingData.jci_number);
        $('#jci_number_search').trigger('change');
    }
<?php else: ?>
    if ($('#jci_number_search').val()) {
        $('#jci_number_search').trigger('change');
    }
<?php endif; ?>
});
</script>

<?php
include_once ROOT_DIR_PATH . 'include/inc/footer.php';
?>
