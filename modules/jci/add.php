<?php
// Ensure configuration is loaded and ROOT_DIR_PATH is defined
include_once __DIR__ . '/../../config/config.php';
if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', __DIR__ . '/../../' . DIRECTORY_SEPARATOR);
}

session_start();

// Include header for common head elements and initial HTML structure
include_once ROOT_DIR_PATH . 'include/inc/header.php';

// Determine user type for sidebar inclusion
$user_type = $_SESSION['user_type'] ?? 'guest';

// Include appropriate sidebar based on user type
if ($user_type === 'superadmin') {
    include_once ROOT_DIR_PATH . 'superadmin/sidebar.php';
} elseif ($user_type === 'salesadmin') {
    include_once ROOT_DIR_PATH . 'salesadmin/sidebar.php';
} elseif ($user_type === 'accounts') {
    include_once ROOT_DIR_PATH . 'accountsadmin/sidebar.php';
}

// Access global database connection
global $conn;

// Initialize variables for edit mode and data
$id = $_GET['id'] ?? null;
$edit_mode = false;
$jci_data = []; // Stores main JCI record
$item_data = []; // Stores associated JCI items

// If an ID is provided, activate edit mode and fetch data
if ($id) {
    $edit_mode = true;
    // Fetch the specific jci_main record
    $stmt = $conn->prepare("SELECT * FROM jci_main WHERE id = ?");
    $stmt->execute([$id]);
    $jci_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch items related to this specific jci_main ID
    $stmt2 = $conn->prepare("SELECT * FROM jci_items WHERE jci_id = ?");
    $stmt2->execute([$id]);
    $item_data = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Function to generate the base JCI number (like JCI-2025-0001).
 * The 'X' suffix for JOB-YEAR-JCN-X will be handled in ajax_save_jci.php.
 * @param PDO $conn The database connection object.
 * @return string The generated base JCI number.
 */
function generateBaseJCINumber($conn) {
    $year = date('Y');
    $prefix = "JCI-$year-"; // This will be the base for JOB-YEAR-JCN
    $stmt = $conn->prepare("SELECT MAX(
        CASE
            WHEN jci_number LIKE 'JOB-{$year}-%-%' THEN CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(jci_number, '-', 3), '-', -1) AS UNSIGNED)
            WHEN jci_number LIKE 'JCI-{$year}-%' THEN CAST(SUBSTRING_INDEX(jci_number, '-', -1) AS UNSIGNED)
            ELSE 0
        END
    ) AS last_seq FROM jci_main
    WHERE jci_number LIKE 'JOB-{$year}-%' OR jci_number LIKE 'JCI-{$year}-%';");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_seq = (int)$result['last_seq'];
    $next_seq = $last_seq + 1;
    $seqFormatted = str_pad($next_seq, 4, '0', STR_PAD_LEFT);
    return $prefix . $seqFormatted;
}

// Determine the JCI number for the input field
$auto_jci_number = $edit_mode ? ($jci_data['jci_number'] ?? generateBaseJCINumber($conn)) : generateBaseJCINumber($conn);

// In edit mode, if jci_number already has a suffix (e.g., JOB-2024-0001-A),
// we need to extract the base part for the input field display.
if ($edit_mode && preg_match('/JOB-\d{4}-(\d{4})-[A-Z]/', $jci_data['jci_number'] ?? '', $matches)) {
    $auto_jci_number = "JCI-" . date('Y') . "-" . $matches[1];
}

// Set created_by and main JCI date/type
$created_by = $edit_mode ? ($jci_data['created_by'] ?? '') : ($_SESSION['user_name'] ?? '');
$po_id_selected = $edit_mode ? ($jci_data['po_id'] ?? '') : '';

$jci_main_date = $edit_mode ? ($jci_data['jci_date'] ?? date('Y-m-d')) : date('Y-m-d');
$jci_main_type = $edit_mode ? ($jci_data['jci_type'] ?? 'Internal') : 'Internal'; // Default to Internal

// Fetch all PO numbers for the dropdown
$stmt_po = $conn->prepare("SELECT id, po_number FROM po_main ORDER BY po_number ASC");
$stmt_po->execute();
$po_numbers = $stmt_po->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* Custom CSS for Select2 to better integrate with Bootstrap form-control */
    .select2-container .select2-selection--single {
        display: block;
        width: 100%;
        height: calc(1.5em + .75rem + 2px); /* Matches Bootstrap's form-control height */
        padding: .375rem .75rem; /* Matches Bootstrap's form-control padding */
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #6e707e;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #d1d3e2;
        border-radius: .35rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        width: 100% !important;
        height: calc(1.5em + .75rem + 2px) !important;
        padding: .375rem !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        color: #6e707e;
        line-height: 1.5; /* Ensure text aligns in the middle */
        padding-left: 0; /* Remove default padding from select2 */
        padding-right: 2rem; /* Make space for the clear button */
        width: 100% !important;
        height: calc(1.5em + .75rem + 2px) !important;
        padding: 0 !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem + 2px); /* Match height */
        width: 2rem; /* Give enough space for the arrow */
        position: absolute;
        top: 0;
        right: 0;
    }
    .select2-container--bootstrap4.select2-container--focus .select2-selection--single,
    .select2-container--bootstrap4.select2-container--open .select2-selection--single {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .select2-container--bootstrap4 .select2-selection__clear {
        float: right;
        font-size: 1em; /* Adjust size of the 'x' */
        position: absolute;
        right: 1.7rem; /* Position it to the left of the arrow */
        top: 50%;
        transform: translateY(-50%);
        padding: 0;
        height: 1.5em; /* Match line height */
        line-height: 1.5em;
        width: 1.5em; /* Make it a square */
        text-align: center;
    }
    .select2-container--bootstrap4 .select2-dropdown {
        border-color: #d1d3e2;
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .select2-container--bootstrap4 .select2-results__option--highlighted,
    .select2-container--bootstrap4 .select2-results__option--selected {
        background-color: #4e73df !important; /* Primary color from your theme */
        color: #fff !important;
    }

    /* Table container for horizontal scroll */
    .table-responsive-custom {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Table column widths - Adjust as needed */
    #itemsTable {
        min-width: 1500px; /* Minimum width to ensure horizontal scrolling */
    }
    #itemsTable th, #itemsTable td {
        vertical-align: middle;
        white-space: nowrap; /* Prevent text wrapping */
    }
    #itemsTable thead th:nth-child(1) { width: 50px; }  /* Serial Number */
    #itemsTable thead th:nth-child(2) { width: 180px; } /* PO Product */
    #itemsTable thead th:nth-child(3) { width: 150px; } /* Product Name */
    #itemsTable thead th:nth-child(4) { width: 120px; } /* Item Code */
    #itemsTable thead th:nth-child(5) { width: 100px; } /* Original Qty */
    #itemsTable thead th:nth-child(6) { width: 100px; } /* Assign Qty */
    #itemsTable thead th:nth-child(7) { width: 100px; } /* Remaining Qty */ /* Corrected index */
    #itemsTable thead th:nth-child(8) { width: 100px; } /* Labour Cost */ /* Corrected index */
    #itemsTable thead th:nth-child(9) { width: 120px; } /* Total */ /* Corrected index */
    #itemsTable thead th:nth-child(10) { width: 140px; } /* Delivery Date */ /* Corrected index */
    #itemsTable thead th:nth-child(11) { width: 140px; } /* Job Card Date */ /* Corrected index */
    #itemsTable thead th:nth-child(12) { width: 140px; } /* Job Card Type */ /* Corrected index */
    #itemsTable thead th:nth-child(13) { width: 180px; } /* Contracture Name */ /* Corrected index */
    #itemsTable thead th:nth-child(14) { width: 80px; }  /* Action */ /* Corrected index */

    /* Ensure specific input types are narrow enough within table cells */
    #itemsTable input[type="number"],
    #itemsTable input[type="date"],
    #itemsTable select {
        width: 100%;
    }
</style>

<div class="container-fluid mb-5" style="width: 85%;">
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
                    <div class="col-lg-4 mb-2">
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
                    <div class="col-lg-4 mb-2">
                        <label for="sell_order_number" class="form-label">Sell Order Number</label>
                        <input type="text" class="form-control" id="sell_order_number" name="sell_order_number" readonly value="<?php echo htmlspecialchars($jci_data['sell_order_number'] ?? ''); ?>">
                    </div>
                    <div class="col-lg-4 mb-2">
                        <label for="base_jci_number" class="form-label">Base JCI Number</label>
                        <input type="text" class="form-control" id="base_jci_number" name="base_jci_number" readonly value="<?php echo htmlspecialchars($auto_jci_number); ?>">
                    </div>
                    <div class="col-lg-4 mb-2">
                        <label for="created_by" class="form-label">Created By</label>
                        <input type="text" class="form-control" id="created_by" name="created_by" value="<?php echo htmlspecialchars($created_by); ?>" required>
                    </div>
                    <div class="col-lg-4 mb-2">
                        <label for="jci_date" class="form-label">Job Card Date</label>
                        <input type="date" class="form-control" id="jci_date" name="jci_date" value="<?php echo htmlspecialchars($jci_main_date); ?>" required>
                    </div>
                </div>

                <div id="contractureDetails">
                    <h5>Add Contracture Details</h5>
                    <div class="table-responsive-custom">
                        <table class="table table-bordered" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>Serial Number</th>
                                    <th>PO Product</th>
                                    <th>Product Name</th>
                                    <th>Item Code</th>
                                    <th>Original Qty</th>
                                    <th>Assign Qty</th>
                                    <th>Remaining Qty</th>
                                    <th>Labour Cost</th>
                                    <th>Total</th>
                                    <th>Delivery Date</th>
                                    <th>Job Card Date</th>
                                    <th>Job Card Type</th>
                                    <th>Contracture Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-right">Grand Total Labour Cost</th>
                                    <th>
                                        <input type="text" id="grandTotal" class="form-control" readonly value="0">
                                    </th>
                                    <th colspan="5"></th>
                                    <th>
                                        <button type="button" class="btn btn-secondary btn-sm ms-2 mb-3" id="addRowBtn" title="Add Row">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update' : 'Save'; ?> Job Card(s)</button>
            </form>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for the main PO dropdown
    $('.select2-enabled').select2({
        placeholder: "Select a PO Number",
        allowClear: true,
        theme: "bootstrap4"
    });

    let poProducts = []; // Stores products fetched for the selected PO
    let rowCount = 0; // Tracks the number of rows in the items table

    /**
     * Calculates the total for a single row based on labour cost and assigned quantity.
     * @param {HTMLElement} row The table row element.
     */
    function calculateRowTotal(row) {
        const costInput = row.querySelector('.labour-cost');
        const assignedQtyInput = row.querySelector('.assign-qty');
        const totalInput = row.querySelector('.item-total');

        const cost = parseFloat(costInput.value) || 0;
        const assignedQty = parseFloat(assignedQtyInput.value) || 0;
        const total = cost * assignedQty;

        totalInput.value = total.toFixed(2);
        calculateGrandTotal(); // Update grand total after each row total change
    }

    /**
     * Calculates and updates the grand total of all item totals.
     */
    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-total').forEach(function(input) {
            grandTotal += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotal').value = grandTotal.toFixed(2);
    }

    /**
     * Updates the serial numbers in the first column of the table after row additions/removals.
     */
    function updateSerialNumbers() {
        const rows = document.querySelectorAll('#itemsTable tbody tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
            // For new unsaved rows, re-index data-row-index if needed (not strictly used by backend)
            const jciItemIdInput = row.querySelector('input[name="jci_item_id[]"]');
            if (jciItemIdInput && jciItemIdInput.value === '') {
                row.dataset.rowIndex = index + 1;
            }
        });
    }

    /**
     * Toggles the visibility and required status of the "Contracture Name" input
     * based on the selected "Job Card Type" for a given row.
     * @param {HTMLElement} rowElement The table row containing the job card type select and contracture name input.
     */
    function toggleContractureName(rowElement) {
        const jobCardTypeSelect = rowElement.querySelector('.job-card-type');
        const contractureNameCell = rowElement.querySelector('.contracture-name-cell');
        const contractureNameInput = rowElement.querySelector('.contracture-name');

        if (jobCardTypeSelect.value === 'Contracture') {
            contractureNameCell.style.display = ''; // Show the cell
            contractureNameInput.required = true;
            contractureNameInput.disabled = false;
        } else {
            contractureNameCell.style.display = 'none'; // Hide the cell
            contractureNameInput.required = false;
            contractureNameInput.disabled = true;
            contractureNameInput.value = ''; // Clear value if hidden
        }
    }

    /**
     * Adds a new row to the contracture details table.
     * Can pre-populate fields if itemData is provided (for edit mode).
     * @param {object} itemData Optional: Object containing data for pre-populating the row.
     */
    function addRow(itemData = {}) {
        rowCount++;
        const tbody = document.querySelector('#itemsTable tbody');
        const tr = document.createElement('tr');
        tr.dataset.rowIndex = rowCount; // Assign a data attribute for easier lookup

        // Extract item data or set default values
        const jciItemId = itemData.id || '';
        const poProductId = itemData.po_product_id || '';
        const productName = itemData.product_name || '';
        const itemCode = itemData.item_code || '';
        const originalPoQuantity = itemData.original_po_quantity || '';
        const assignedQuantity = itemData.quantity || ''; // 'quantity' in jci_items is now assigned_quantity
        const labourCost = itemData.labour_cost || '';
        const total = itemData.total_amount || '';
        const deliveryDate = itemData.delivery_date || '';
        const jobCardDate = itemData.job_card_date || '<?php echo date('Y-m-d'); ?>';
        const jobCardType = itemData.job_card_type || 'Contracture';
        const contractureName = itemData.contracture_name || '';

        tr.innerHTML = `
            <td>${rowCount}</td>
            <td>
                <input type="hidden" name="jci_item_id[]" value="${jciItemId}">
                <select name="po_product_id[]" class="form-control po-product-select" required style="width:100%;">
                    <option value="">Select Product</option>
                    ${poProducts.map(p => `<option value="${p.id}" data-product-name="${p.product_name}" data-item-code="${p.item_code}" data-quantity="${p.quantity}" ${poProductId == p.id ? 'selected' : ''}>${p.product_name} (${p.item_code})</option>`).join('')}
                </select>
            </td>
            <td><input type="text" name="product_name[]" class="form-control product-name" value="${productName}" readonly></td>
            <td><input type="text" name="item_code[]" class="form-control item-code" value="${itemCode}" readonly></td>
            <td><input type="number" name="original_po_quantity[]" class="form-control original-po-qty" step="0.01" min="0" value="${originalPoQuantity}" readonly></td>
            <td><input type="number" name="assign_quantity[]" class="form-control assign-qty" step="0.01" min="0" value="${assignedQuantity}" required></td>
            <td><input type="number" name="remaining_quantity[]" class="form-control remaining-qty" step="0.01" min="0" value="0" readonly></td>
            <td><input type="number" name="labour_cost[]" class="form-control labour-cost" step="0.01" min="0" value="${labourCost}" required></td>
            <td><input type="text" name="total[]" class="form-control item-total" readonly value="${total || 0}"></td>
            <td><input type="date" name="delivery_date[]" class="form-control" value="${deliveryDate}" required></td>
            <td><input type="date" name="job_card_date[]" class="form-control" value="${jobCardDate}" required></td>
            <td><select name="job_card_type[]" class="form-control job-card-type" required>
                <option value="Contracture" ${jobCardType === 'Contracture' ? 'selected' : ''}>Contracture</option>
                <option value="In-House" ${jobCardType === 'In-House' ? 'selected' : ''}>In-House</option>
            </select></td>
            <td class="contracture-name-cell"><input type="text" name="contracture_name[]" class="form-control contracture-name" value="${contractureName}" required></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRowBtn" title="Delete Row"><i class="fas fa-trash"></i></button></td>
        `;

        tbody.appendChild(tr);

        // Initialize Select2 for the newly added PO Product dropdown
        $(tr.querySelector('.po-product-select')).select2({
            placeholder: "Select Product",
            allowClear: true,
            theme: "bootstrap4",
            dropdownAutoWidth: true,
            width: 'style'
        });

        // Add event listeners for the new row inputs
        tr.querySelector('.labour-cost').addEventListener('input', () => calculateRowTotal(tr));
        tr.querySelector('.assign-qty').addEventListener('input', function() {
            // Note: The original quantity cap here is removed as updateRemainingQuantities() will handle the max value
            updateRemainingQuantities(); // Update remaining quantities across all rows
            calculateRowTotal(tr); // Recalculate total for this row
        });

        // Trigger initial updates for remaining quantities and total
        // These will be called again after product selection or full data load
        updateRemainingQuantities();
        calculateGrandTotal();

        // Add event listener for row removal button
        tr.querySelector('.removeRowBtn').addEventListener('click', () => {
            tr.remove();
            calculateGrandTotal();
            updateSerialNumbers();
            updateRemainingQuantities(); // Update remaining quantities after a row is removed
        });

        // Add event listener for Job Card Type change
        tr.querySelector('.job-card-type').addEventListener('change', () => toggleContractureName(tr));

        // Event listener for PO Product selection within the row
        $(tr.querySelector('.po-product-select')).on('change', function() {
            const selectedOption = $(this).find(':selected');
            const product = poProducts.find(p => p.id == selectedOption.val());

            if (product) {
                tr.querySelector('.product-name').value = product.product_name;
                tr.querySelector('.item-code').value = product.item_code;
                tr.querySelector('.original-po-qty').value = product.quantity;
                // Only set assigned quantity to original if it's a new row (not pre-populated from existing data)
                // OR if it's explicitly an empty string, implying no previous assignment.
                if (assignedQuantity === '' || assignedQuantity === null) {
                     tr.querySelector('.assign-qty').value = product.quantity; // Default to original quantity for new selections
                }
            } else {
                // Clear fields if no product is selected
                tr.querySelector('.product-name').value = '';
                tr.querySelector('.item-code').value = '';
                tr.querySelector('.original-po-qty').value = '';
                tr.querySelector('.assign-qty').value = '';
            }
            updateRemainingQuantities(); // Crucial: update remaining quantities after product selection
            calculateRowTotal(tr); // Recalculate total after product change
        });

        // If in edit mode and itemData is provided, trigger the change event
        // to correctly populate product-related fields and set remaining quantity.
        if (Object.keys(itemData).length > 0) {
            setTimeout(() => {
                $(tr.querySelector('.po-product-select')).val(poProductId).trigger('change');
                toggleContractureName(tr); // Apply visibility based on loaded type
                // updateRemainingQuantities() and calculateRowTotal() are implicitly called by the change trigger.
                // We re-call them here explicitly to ensure they run after all values are set from itemData.
                updateRemainingQuantities();
                calculateRowTotal(tr);
            }, 100); // Small delay to ensure Select2 is fully initialized
        } else {
            // For new rows, immediately apply the visibility based on default type
            toggleContractureName(tr);
        }

        updateSerialNumbers();
    }

    /**
     * Updates the "Remaining Qty" for each product across all rows
     * and sets the maximum allowed "Assign Qty" to prevent over-assignment.
     */
    function updateRemainingQuantities() {
        // For each row, calculate remaining quantity as original quantity minus assigned quantity in that row
        document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
            const originalQtyInput = row.querySelector('input[name="original_po_quantity[]"]');
            const assignQtyInput = row.querySelector('input[name="assign_quantity[]"]');
            const remainingQtyInput = row.querySelector('input[name="remaining_quantity[]"]');

            const originalQty = parseFloat(originalQtyInput.value) || 0;
            const assignQty = parseFloat(assignQtyInput.value) || 0;

            const remainingQty = originalQty - assignQty;
            remainingQtyInput.value = remainingQty >= 0 ? remainingQty.toFixed(2) : '0.00';

            // Set max attribute for assign quantity input to original quantity
            assignQtyInput.max = originalQty;

            // Enforce max assign quantity immediately if current value exceeds the new limit
            if (assignQty > originalQty) {
                assignQtyInput.value = originalQty;
                alert('Assigned Quantity has been adjusted to not exceed the Original Quantity.');
            }

            calculateRowTotal(row); // Recalculate row total after quantity adjustments
        });
    }


    // --- Main PO Selection Logic ---
    $('#po_id').on('change', function() {
        var poId = $(this).val();
        poProducts = []; // Clear previous products
        $('#itemsTable tbody').empty(); // Clear existing rows
        rowCount = 0; // Reset row count
        calculateGrandTotal(); // Reset grand total

        if (poId) {
            // Fetch Sell Order Number
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
                    console.error('Error fetching sell order number.');
                    $('#sell_order_number').val('');
                }
            });

            // Fetch PO Products for the selected PO
            $.ajax({
                url: '<?php echo BASE_URL; ?>modules/jci/ajax_fetch_po_products.php',
                type: 'POST',
                data: { po_id: poId },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.products.length > 0) {
                        poProducts = response.products;
                        // For new JCI, add one empty row after PO products are loaded.
                        // For edit mode, existing items are loaded by the PHP block below.
                        if (!<?php echo $edit_mode ? 'true' : 'false'; ?>) {
                            addRow();
                        }
                    } else {
                        alert('No products found for the selected PO. Please add them in the PO module.');
                        addRow(); // Still add an empty row to allow adding details even if no PO products initially
                    }
                },
                error: function() {
                    alert('Error fetching PO products.');
                    addRow(); // Add an empty row on error
                }
            });
        } else {
            // Clear sell order number and table if no PO is selected
            $('#sell_order_number').val('');
            // Ensure an empty row exists if PO is cleared and it's not edit mode or no existing items.
            // This prevents an empty table if the user clears the PO on a new JCI form.
            if (!<?php echo $edit_mode ? 'true' : 'false'; ?> || <?php echo empty($item_data) ? 'true' : 'false'; ?>) {
                 addRow();
            }
        }
    });

    // --- Initial Load Logic (for Edit Mode and New Add) ---
    <?php if ($edit_mode): ?>
        // If in edit mode, fetch PO products first, then populate existing items
        var initialPoId = '<?php echo htmlspecialchars($po_id_selected); ?>';
        if (initialPoId) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>modules/jci/ajax_fetch_po_products.php',
                type: 'POST',
                data: { po_id: initialPoId },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.products.length > 0) {
                        poProducts = response.products;
                        // Now load existing JCI items using item_data from PHP
                        <?php if (!empty($item_data)): ?>
                            <?php foreach ($item_data as $item): ?>
                                addRow(<?php echo json_encode($item); ?>);
                            <?php endforeach; ?>
                        <?php else: ?>
                            addRow(); // If no items for this JCI in edit mode, add one empty row
                        <?php endif; ?>
                        calculateGrandTotal();
                        updateRemainingQuantities(); // Update remaining quantities on load
                    } else {
                        alert('No products found for the selected PO. Please add them in the PO module.');
                        addRow(); // Add an empty row even if no products
                    }
                },
                error: function() {
                    alert('Error fetching PO products for existing JCI.');
                    addRow(); // Add an empty row on error
                }
            });
            // In edit mode, also populate the sell order number from existing jci_data
            $('#sell_order_number').val('<?php echo htmlspecialchars($jci_data['sell_order_number'] ?? ''); ?>');
        } else {
            addRow(); // If no PO selected initially (shouldn't happen in edit mode), add one empty row
        }
    <?php else: ?>
        // For new entry, if a PO is already selected (e.g., from query param), trigger change to load products
        if ($('#po_id').val()) {
            $('#po_id').trigger('change');
        } else {
            // If no PO selected initially, still add an empty row (user will select PO later)
            addRow();
        }
    <?php endif; ?>

    // Add Row Button click handler
    document.getElementById('addRowBtn').addEventListener('click', function() {
        if ($('#po_id').val()) { // Only allow adding rows if a PO is selected
            addRow();
        } else {
            alert('Please select a PO Number first to add contracture details.');
        }
    });

    // Form Submission Handler
    document.getElementById('jciForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Basic validation: ensure at least one detail row if a PO is selected
        if ($('#po_id').val() && document.querySelectorAll('#itemsTable tbody tr').length === 0) {
            alert('Please add at least one contracture detail.');
            return;
        }

        // Validate "Contracture Name" if "Job Card Type" is "Contracture"
        let isValid = true;
        document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
            const jobCardTypeSelect = row.querySelector('.job-card-type');
            const contractureNameInput = row.querySelector('.contracture-name');
            if (jobCardTypeSelect.value === 'Contracture' && contractureNameInput.value.trim() === '') {
                isValid = false;
                alert('Contracture Name is required for Job Card Type "Contracture".');
                contractureNameInput.focus();
                return; // Exit forEach early if validation fails
            }
        });

        if (!isValid) {
            return; // Stop form submission if validation fails
        }

        const formData = new FormData(this); // Create FormData object from the form

        // Send data using Fetch API (modern AJAX)
        fetch('<?php echo BASE_URL; ?>modules/jci/ajax_save_jci.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Parse JSON response
        .then(data => {
            // Display Bootstrap toast notification
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
                // If successful in "add new" mode, reset form for next entry
                e.target.reset(); // Reset form fields
                $('.select2-enabled').val('').trigger('change'); // Clear Select2 dropdowns
                document.querySelector('#itemsTable tbody').innerHTML = ''; // Clear table rows
                document.getElementById('grandTotal').value = '0.00'; // Reset grand total
                // Update generated JCI number and created by field for the next entry
                document.getElementById('base_jci_number').value = data.new_base_jci_number;
                document.getElementById('created_by').value = '<?php echo $_SESSION['user_name'] ?? ''; ?>';
                document.getElementById('jci_date').value = '<?php echo date('Y-m-d'); ?>';
                addRow(); // Add a fresh empty row for the next entry
            } else if (data.success && <?php echo $edit_mode ? 'true' : 'false'; ?>) {
                // If successful in "edit" mode, redirect after a short delay
                setTimeout(() => {
                    window.location.href = '<?php echo BASE_URL; ?>modules/jci/index.php';
                }, 1500);
            }
        })
        .catch(error => {
            // Handle fetch errors (network issues, etc.)
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