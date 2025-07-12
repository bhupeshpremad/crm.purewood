<?php
error_reporting(0);
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

// Check if edit mode
$payment_id = $_GET['id'] ?? null;
$edit_mode = !empty($payment_id);
$existing_payment_data = null;

if ($edit_mode) {
    // Fetch existing payment data
    $stmt = $conn->prepare("SELECT * FROM payments WHERE id = ?");
    $stmt->execute([$payment_id]);
    $existing_payment_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch Job Card Numbers with related PO and SON for dropdown
// In edit mode, include the current JCI even if payment is completed
if ($edit_mode && $existing_payment_data) {
    $stmt = $conn->prepare("SELECT j.jci_number, j.po_id, p.po_number, p.sell_order_number FROM jci_main j LEFT JOIN po_main p ON j.po_id = p.id WHERE j.purchase_created = 1 AND (j.payment_completed = 0 OR j.jci_number = ?) ORDER BY j.jci_number ASC");
    $stmt->execute([$existing_payment_data['jci_number']]);
} else {
    $stmt = $conn->prepare("SELECT j.jci_number, j.po_id, p.po_number, p.sell_order_number FROM jci_main j LEFT JOIN po_main p ON j.po_id = p.id WHERE j.purchase_created = 1 AND j.payment_completed = 0 ORDER BY j.jci_number ASC");
    $stmt->execute();
}
$jci_numbers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid" style="width: 85%;">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary" id="formTitle"><?php echo $edit_mode ? 'Edit Payment Details' : 'Add Payment Details'; ?></h6>
        </div>
        <div class="card-body">
            <form id="vendorPayment_form" autocomplete="off">
                <input type="hidden" name="payment_id" id="payment_id" value="<?php echo htmlspecialchars($payment_id ?? ''); ?>">
                <input type="hidden" name="lead_id" id="lead_id" value="">

                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="jci_number" class="form-label">JCI Number</label>
                        <select class="form-control" id="jci_number" name="jci_number" required <?php echo $edit_mode ? 'disabled' : ''; ?>>
                            <option value="">Select JCI Number</option>
                            <?php
                            foreach ($jci_numbers as $jci) {
                                $selected = ($edit_mode && $existing_payment_data && $existing_payment_data['jci_number'] == $jci['jci_number']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($jci['jci_number']) . '" data-po-id="' . htmlspecialchars($jci['po_id']) . '" data-po-number="' . htmlspecialchars($jci['po_number']) . '" data-son="' . htmlspecialchars($jci['sell_order_number']) . '" ' . $selected . '>' . htmlspecialchars($jci['jci_number']) . '</option>';
                            }
                            ?>
                        </select>
                        <?php if ($edit_mode): ?>
                        <input type="hidden" name="jci_number" value="<?php echo htmlspecialchars($existing_payment_data['jci_number'] ?? ''); ?>">
                        <?php endif; ?>
                        <input type="hidden" id="po_id" name="po_id" value="">
                    </div>
                    <div class="col-lg-4">
                        <label for="po_number_display" class="form-label">Purchase Order Number (PO Number)</label>
                        <input type="text" class="form-control" id="po_number_display" name="po_number_display" readonly>
                        <input type="hidden" id="po_number" name="po_number">
                    </div>
                    <div class="col-lg-4">
                        <label for="son_number" class="form-label">Sale Order Number (SON)</label>
                        <input type="text" class="form-control" id="son_number" name="son_number" readonly>
                    </div>
                    <input type="hidden" id="po_amt" name="po_amt">
                    <input type="hidden" id="soa_number" name="soa_number">
                </div>

                <div class="mb-3">
                    <label class="form-label font-weight-bold">Job Card Details</label>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="job_card_details_table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 15%;">Job Card Number</th>
                                    <th style="width: 15%;">Job Card Type</th>
                                    <th style="width: 20%;">Contracture Name</th>
                                    <th style="width: 15%;">Labour Cost</th>
                                    <th style="width: 15%;">Quantity</th>
                                    <th style="width: 15%;">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-end" colspan="5">Total Job Card Amount:</th>
                                    <th><input type="text" class="form-control" id="total_jc_amount" name="total_jc_amount" readonly></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-weight-bold">Supplier Information</label>
                    <div id="suppliers_container"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-weight-bold">Payment Mode Information</label>
                    <div style="overflow-x: auto; max-width: 100%; white-space: nowrap;">
                        <table class="table table-bordered table-sm" id="payment_details_table" style="min-width: 1300px;">
                            <thead class="thead-light">
                                <tr>
                            <th style="width: 5%;"><input type="checkbox" id="select_all_payments" title="Select All"></th>
                            <th style="width: 12%;">Payee</th>
                            <th style="width: 18%;min-width: 135px;">Type</th>
                            <th style="width: 13%;">Cheque/RTGS Number</th>
                            <th style="width: 12%;">PD ACC Number</th>
                            <th style="width: 12%;min-width: 150px;">Amount</th>
                            <th style="width: 8%;">Invoice Number</th>
                            <th style="width: 7%;">Invoice Date</th>
                            <th style="width: 7%;">Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-end">Total Payment Amount:</th>
                                    <td colspan="2"> <input type="text" class="form-control d-inline-block w-auto" id="total_ptm_amount" name="total_ptm_amount" readonly>
                                    </td>
                                </tr>
                                <tr style="display:none;">
                                    <th colspan="7" class="text-end">Margin:</th> <td><span id="margin_percentage" class="ms-2">N/A</span></td> </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3" id="submitBtn">Submit</button>
            </form>
        </div>
    </div>
    <?php include_once ROOT_DIR_PATH . 'include/inc/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<script>
    $(document).ready(function() {
    // Auto-load data in edit mode
    <?php if ($edit_mode && $existing_payment_data): ?>
        console.log('Edit mode detected, loading existing payment data');
        var existingJci = '<?php echo htmlspecialchars($existing_payment_data['jci_number'] ?? ''); ?>';
        console.log('Existing JCI:', existingJci);
        if (existingJci) {
            // Set the dropdown value and trigger change
            $('#jci_number').val(existingJci);
            console.log('JCI dropdown set to:', $('#jci_number').val());
            // Trigger change after a small delay to ensure DOM is ready
            setTimeout(function() {
                $('#jci_number').trigger('change');
            }, 100);
        }
    <?php endif; ?>
    
    $('#jci_number').on('change', function() {
        const jciNumber = $(this).val();

        if (!jciNumber) {
            $('#po_number_display, #po_number, #son_number, #po_amt, #soa_number, #total_jc_amount, #total_supplier_amount, #total_ptm_amount').val('');
            $('#job_card_details_table tbody, #suppliers_container, #payment_details_table tbody').empty();
            $('#po_amt_validation_msg').hide().text('');
            return;
        }

        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/payments/ajax_fetch_job_card_details.php',
            type: 'GET',
            data: { jci_number: jciNumber },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    console.log("Supplier data received:", response.suppliers); // Added console log for supplier data
                    $('#po_number_display').val(response.po_number || '');
                    $('#po_number').val(response.po_number || '');
                    $('#son_number').val(response.sell_order_number || '');

                    const jobCardTbody = $('#job_card_details_table tbody');
                    jobCardTbody.empty();
                    if (response.job_cards.length > 0) {
                        response.job_cards.forEach(function(jc) {
                            const row = `
                                <tr class="jobcard-row" data-jc-id="${jc.id || ''}">
                                    <td><input type="text" class="form-control jc_number" name="jc_number[]" value="${jc.jci_number || ''}" readonly></td>
                                    <td><input type="text" class="form-control jc_type" name="jc_type[]" value="${jc.jci_type || ''}" readonly></td>
                                    <td><input type="text" class="form-control contracture_name" name="contracture_name[]" value="${jc.contracture_name || ''}" readonly></td>
                                    <td><input type="number" class="form-control labour_cost" name="labour_cost[]" value="${jc.labour_cost || ''}" readonly></td>
                                    <td><input type="number" class="form-control quantity" name="quantity[]" value="${jc.quantity || ''}" readonly></td>
                                    <td><input type="number" class="form-control total_amount" name="total_amount[]" value="${parseFloat(jc.total_amount).toFixed(2) || '0.00'}" readonly></td>
                                </tr>`;
                            jobCardTbody.append(row);
                        });
                    } else {
                        jobCardTbody.append('<tr><td colspan="6" class="text-center">No Job Card Details found.</td></tr>');
                    }

                    const suppliersContainer = $('#suppliers_container');
                    suppliersContainer.empty();
                    if (response.suppliers.length > 0) {
                        const woodItems = [];
                        const glowItems = [];
                        const plyItems = [];
                        const hardwareItems = [];

                        response.suppliers.forEach(function(supplier) {
                            supplier.items.forEach(function(item) {
                                item.supplier_id = supplier.id;
                                item.supplier_name = supplier.supplier_name;
                                const itemName = item.item_name.toLowerCase();
                                const productType = (item.product_type || '').toLowerCase();

                                // Use product_type for better categorization
                                if (productType.includes('wood') || itemName.includes('wood') || itemName.includes('mango') || itemName.includes('teak') || itemName.includes('sheesham')) {
                                    woodItems.push(item);
                                } else if (productType.includes('glow') || itemName.includes('glow') || itemName.includes('fevicol') || itemName.includes('favicole')) {
                                    glowItems.push(item);
                                } else if (productType.includes('plynydf') || productType.includes('ply') || itemName.includes('ply')) {
                                    plyItems.push(item);
                                } else if (productType.includes('hardware') || itemName.includes('hardware') || itemName.includes('screw')) {
                                    hardwareItems.push(item);
                                } else {
                                    // Default to appropriate category based on product_type
                                    if (productType) {
                                        if (productType.includes('wood')) woodItems.push(item);
                                        else if (productType.includes('glow')) glowItems.push(item);
                                        else if (productType.includes('ply')) plyItems.push(item);
                                        else if (productType.includes('hardware')) hardwareItems.push(item);
                                        else plyItems.push(item); // fallback
                                    } else {
                                        plyItems.push(item); // fallback for unknown items
                                    }
                                }
                            });
                        });

                        const generateSupplierTable = (items, label) => {
                            let tableHtml = `
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label"><strong>${label}</strong></label>
                                        <table class="table table-bordered supplier-item-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%;">Item Name</th>
                                                    <th style="width: 20%;">Quantity</th>
                                                    <th style="width: 20%;">Price</th>
                                                    <th style="width: 30%;">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;
                            if (items.length > 0) {
                                items.forEach(function(item) {
                                    tableHtml += `
                                        <tr class="supplier-item-row" data-supplier-id="${item.supplier_id || ''}" data-item-id="${item.id || ''}">
                                            <td><input type="text" class="form-control item_name" value="${item.item_name || ''}" readonly></td>
                                            <td><input type="number" class="form-control item_quantity" value="${item.item_quantity || ''}" readonly></td>
                                            <td><input type="number" class="form-control item_price" value="${item.item_price || ''}" readonly></td>
                                            <td><input type="number" class="form-control item_amount" value="${parseFloat(item.item_amount).toFixed(2) || '0.00'}" readonly></td>
                                            <input type="hidden" class="item_product_type" value="${item.product_type || ''}">
                                        </tr>`;
                                });
                            } else {
                                tableHtml += `<tr><td colspan="4" class="text-center">No ${label} found.</td></tr>`;
                            }
                            tableHtml += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>`;
                            return tableHtml;
                        };

                        let fullSupplierHtml = `
                            <div class="card card-body mb-3 supplier-group">
                                ${generateSupplierTable(woodItems, 'Wood Items')}
                                ${generateSupplierTable(glowItems, 'Glow Items')}
                                ${generateSupplierTable(plyItems, 'Ply Items')}
                                ${generateSupplierTable(hardwareItems, 'Hardware Items')}
                            </div>`;
                        suppliersContainer.append(fullSupplierHtml);
                    } else {
                        suppliersContainer.append('<p>No Supplier Information found.</p>');
                    }

                    $('#po_amt').val(response.po_amount || '');
                    $('#soa_number').val(response.soa_number || '');
                    $('#payment_details_table tbody').empty();

                    window.latestFetchedResponse = response;
                    generatePaymentRows(response.job_cards, response.suppliers, response.existing_payments || []);

                    calculateJobCardAmounts();
                    calculateSupplierItemAmounts();
                    calculatePaymentAmounts();
                    checkTotalAmountsAgainstPO();

                } else {
                    toastr.error('Failed to fetch Job Card details: ' + (response.message || 'Unknown error'));
                    $('#po_number_display, #po_number, #son_number, #po_amt, #soa_number, #total_jc_amount, #total_supplier_amount, #total_ptm_amount').val('');
                    $('#job_card_details_table tbody, #suppliers_container, #payment_details_table tbody').empty();
                    $('#po_amt_validation_msg').hide().text('');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error fetching Job Card details: ", status, error, xhr.responseText);
                toastr.error('AJAX error fetching Job Card details: ' + error + '. Check console for more details.');
            }
        });
    });

    // Removed second AJAX call to ajax_fetch_payments_by_jci.php to prevent disabling inputs and checking checkboxes for existing payments
    // Existing payments are handled in the first AJAX call and generatePaymentRows function

    // Added event handler to enable/disable inputs based on checkbox state
    $('#payment_details_table').on('change', '.select_payment', function() {
        const $row = $(this).closest('.payment-row');
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            $row.find('input, select').not('.invoice_number, .invoice_date').prop('disabled', false);
            $row.find('.ptm_amount').prop('readonly', false);
            $row.find('.payment_date').focus(); // Focus on payment date after checking
        } else {
            $row.find('input, select').not('.select_payment').prop('disabled', true);
            $row.find('.ptm_amount').prop('readonly', true);
        }
    });

    function generatePaymentRows(jobCards, suppliers, existingPayments = []) {
        $('#payment_details_table tbody').empty();
        
        // In edit mode, fetch existing payment details from database
        <?php if ($edit_mode): ?>
        // Load existing payment details for edit mode
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/payments/ajax_fetch_payment_details_by_jci.php',
            type: 'GET',
            data: { payment_id: <?php echo $payment_id; ?> },
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.success && response.payment_details) {
                    existingPayments = response.payment_details;
                }
            }
        });
        <?php endif; ?>

        jobCards.forEach(function(jc) {
            // Find existing payment for this job card
            const existingPayment = existingPayments.find(p => p.payment_category === 'Job Card');
            const isReadOnly = !!existingPayment;

            const chequeType = isReadOnly ? existingPayment.payment_type : '';
            const chequeNumber = isReadOnly ? existingPayment.cheque_number : '';
            const pdAccNumber = isReadOnly ? existingPayment.pd_acc_number : '';
            const ptmAmount = isReadOnly ? parseFloat(existingPayment.ptm_amount).toFixed(2) : parseFloat(jc.total_amount).toFixed(2);
            const paymentDate = isReadOnly ? existingPayment.payment_date : '';

            const disabledSelectAttr = isReadOnly ? 'disabled' : '';
            const disabledInputAttr = isReadOnly ? 'readonly disabled' : '';
            const ptmAmountReadonlyAttr = isReadOnly ? 'readonly' : '';

            const checkedAttr = isReadOnly ? 'checked' : '';

            const newRow = `
                <tr class="payment-row" data-entity-type="job_card" data-entity-id="${jc.id || ''}" data-original-amount="${parseFloat(jc.total_amount).toFixed(2)}">
                    <td><input type="checkbox" class="select_payment" title="Select Payment" ${checkedAttr}></td>
                    <td>Job Card: ${jc.jci_number}</td>
                    <td style="width: 18%;">
                        <select class="form-control cheque_type" name="cheque_type[]" ${disabledSelectAttr}>
                            <option value="">Select Type</option>
                            <option value="Cheque" ${chequeType === 'Cheque' ? 'selected' : ''}>Cheque</option>
                            <option value="RTGS" ${chequeType === 'RTGS' ? 'selected' : ''}>RTGS</option>
                        </select>
                    </td>
                    <td style="width: 13%;"><input type="text" class="form-control cheque_number" name="cheque_number[]" placeholder="Enter Cheque/RTGS Number" value="${chequeNumber}" ${disabledInputAttr}></td>
                    <td style="width: 12%;"><input type="number" class="form-control pd_acc_number" name="pd_acc_number[]" value="${pdAccNumber}" ${disabledInputAttr}></td>
                    <td style="width: 12%;"><input type="number" class="form-control ptm_amount" name="ptm_amount[]" min="0" step="0.01" value="${ptmAmount}" ${ptmAmountReadonlyAttr}></td>
                    <td style="width: 8%;"><input type="text" class="form-control invoice_number" name="invoice_number[]" value="" readonly></td>
                    <td style="width: 7%;"><input type="date" class="form-control invoice_date" name="invoice_date[]" value="" readonly></td>
                    <td style="width: 7%;"><input type="date" class="form-control payment_date" name="payment_date[]" value="${paymentDate}" required></td>
                </tr>`;
            $('#payment_details_table tbody').append(newRow);
        });

        suppliers.forEach(function(supplier, index) {
            // Find existing payment for this supplier
            const existingPayment = existingPayments.find(p => p.payment_category === 'Supplier');
            const isReadOnly = !!existingPayment;

            const chequeType = isReadOnly ? existingPayment.payment_type : '';
            const chequeNumber = isReadOnly ? existingPayment.cheque_number : '';
            const pdAccNumber = isReadOnly ? existingPayment.pd_acc_number : '';
            const ptmAmount = isReadOnly ? parseFloat(existingPayment.ptm_amount).toFixed(2) : parseFloat(supplier.invoice_amount).toFixed(2);
            const invoiceNumber = isReadOnly ? existingPayment.invoice_number : (supplier.invoice_number || '');
            const invoiceDate = isReadOnly ? existingPayment.invoice_date : (supplier.invoice_date || '');
            const paymentDate = isReadOnly ? existingPayment.payment_date : '';

            const disabledSelectAttr = isReadOnly ? 'disabled' : '';
            const disabledInputAttr = isReadOnly ? 'readonly disabled' : '';
            const ptmAmountReadonlyAttr = isReadOnly ? 'readonly' : '';

            const checkedAttr = isReadOnly ? 'checked' : '';
            
            // Create display name with invoice info
            let displayName = `Supplier: ${supplier.supplier_name}`;
            if (supplier.invoice_number) {
                displayName += ` (Invoice: ${supplier.invoice_number})`;
            }

            const newRow = `
                <tr class="payment-row" data-entity-type="supplier" data-entity-id="${index}" data-original-amount="${ptmAmount}">
                    <td><input type="checkbox" class="select_payment" title="Select Payment" ${checkedAttr}></td>
                    <td>${displayName}</td>
                    <td style="width: 18%;min-width: 135px;">
                        <select class="form-control cheque_type" name="cheque_type[]" ${disabledSelectAttr}>
                            <option value="">Select Type</option>
                            <option value="Cheque" ${chequeType === 'Cheque' ? 'selected' : ''}>Cheque</option>
                            <option value="RTGS" ${chequeType === 'RTGS' ? 'selected' : ''}>RTGS</option>
                        </select>
                    </td>
                    <td style="width: 13%;"><input type="text" class="form-control cheque_number" name="cheque_number[]" placeholder="Enter Cheque/RTGS Number" value="${chequeNumber}" ${disabledInputAttr}></td>
                    <td style="width: 12%;"><input type="number" class="form-control pd_acc_number" name="pd_acc_number[]" value="${pdAccNumber}" ${disabledInputAttr}></td>
                    <td style="width: 12%;min-width: 150px;"><input type="number" class="form-control ptm_amount" name="ptm_amount[]" min="0" step="0.01" value="${ptmAmount}" ${ptmAmountReadonlyAttr}></td>
                    <td style="width: 8%;"><input type="text" class="form-control invoice_number" name="invoice_number[]" value="${invoiceNumber}" readonly></td>
                    <td style="width: 7%;"><input type="date" class="form-control invoice_date" name="invoice_date[]" value="${invoiceDate}" readonly></td>
                    <td style="width: 7%;"><input type="date" class="form-control payment_date" name="payment_date[]" value="${paymentDate}" required></td>
                </tr>`;
            $('#payment_details_table tbody').append(newRow);
        });
    }

    function calculateJobCardAmounts() {
        let totalJobCardAmount = 0;
        $('#job_card_details_table tbody .jobcard-row').each(function() {
            let jcAmt = parseFloat($(this).find('.total_amount').val()) || 0;
            totalJobCardAmount += jcAmt;
        });
        $('#total_jc_amount').val(totalJobCardAmount.toFixed(2));
    }

    function calculateSupplierItemAmounts() {
        let totalGrandSupplierAmount = 0;
        $('#suppliers_container .supplier-group').each(function() {
            $(this).find('.supplier-item-table tbody .supplier-item-row').each(function() {
                let amount = parseFloat($(this).find('.item_amount').val()) || 0;
                totalGrandSupplierAmount += amount;
            });
        });
        $('#total_supplier_amount').val(totalGrandSupplierAmount.toFixed(2));
    }

    function calculatePaymentAmounts() {
        let totalPaymentAmount = 0;
        $('#payment_details_table tbody .payment-row').each(function() {
            if (!$(this).find('.ptm_amount').is(':disabled')) {
                let ptmAmount = parseFloat($(this).find('.ptm_amount').val()) || 0;
                totalPaymentAmount += ptmAmount;
            }
        });
        $('#total_ptm_amount').val(totalPaymentAmount.toFixed(2));
    }

    function checkTotalAmountsAgainstPO() {
        const poAmt = parseFloat($('#po_amt').val()) || 0;
        const totalJcAmount = parseFloat($('#total_jc_amount').val()) || 0;
        const totalSupplierItemsAmount = parseFloat($('#total_supplier_amount').val()) || 0;
        const combinedJcAndSupplierAmount = totalJcAmount + totalSupplierItemsAmount;
        const tenPercentPoAmt = poAmt * 1.10;
        const $validationMsg = $('#po_amt_validation_msg');

        $validationMsg.text('');
        $validationMsg.hide();

        if (poAmt === 0 && combinedJcAndSupplierAmount > 0) {
            $validationMsg.text('PO Amount cannot be zero if Job Card or Supplier Item amounts are entered.');
            $validationMsg.show();
        }
        else if (combinedJcAndSupplierAmount > tenPercentPoAmt) {
            $validationMsg.text(`Combined JC & Item Amount (${combinedJcAndSupplierAmount.toFixed(2)}) exceeds 110% of PO Amount (${poAmt.toFixed(2)}).`);
            $validationMsg.show();
        }
    }

    $('#vendorPayment_form').on('submit', function(e) {
        e.preventDefault();

        if (!$('#jci_number').val()) {
            toastr.error('Please select a Job Card Number before submitting.');
            return;
        }

        if ($('#payment_details_table tbody .select_payment:checked').length === 0) {
            toastr.error('Please select at least one payment row to save.');
            return;
        }

        submitFormData();
    });

    function submitFormData() {
        let formData = {};

        const formDataArray = $('#vendorPayment_form').serializeArray();
        $.each(formDataArray, function(_, field) {
            if (!field.name.includes('[]') && !field.name.includes('suppliers[')) {
                formData[field.name] = field.value;
            }
        });

        // Add jci_number explicitly to formData to ensure it is sent
        formData['jci_number'] = $('#jci_number').val();
        formData['pon_number'] = $('#po_number').val();
        formData['son_number'] = $('#son_number').val();
        formData['sell_order_number'] = $('#son_number').val();
        formData['po_amt'] = $('#po_amt').val();
        formData['soa_number'] = $('#soa_number').val();
        formData['supplier_name'] = ''; // supplier_name is not directly available here, will be handled in backend

        let jobCards = [];
        $('#job_card_details_table tbody .jobcard-row').each(function() {
            let jobCard = {
                id: $(this).data('jc-id'),
                jc_number: $(this).find('.jc_number').val(),
                jc_type: $(this).find('.jc_type').val(),
                contracture_name: $(this).find('.contracture_name').val(),
                labour_cost: $(this).find('.labour_cost').val(),
                quantity: $(this).find('.quantity').val(),
                total_amount: $(this).find('.total_amount').val(),
            };
            jobCards.push(jobCard);
        });
        formData['job_cards'] = JSON.stringify(jobCards);

        let suppliersData = [];
        const groupedSupplierItems = {};

        $('#suppliers_container .supplier-item-row').each(function() {
            const supplierId = $(this).data('supplier-id');
            const itemName = $(this).find('.item_name').val();
            const itemQuantity = $(this).find('.item_quantity').val();
            const itemPrice = $(this).find('.item_price').val();
            const itemAmount = $(this).find('.item_amount').val();
            const productType = $(this).find('.item_product_type').val() || ''; // Added product_type

            if (supplierId) {
                if (!groupedSupplierItems[supplierId]) {
                    const originalSupplier = (window.latestFetchedResponse && window.latestFetchedResponse.suppliers)
                                                    ? window.latestFetchedResponse.suppliers.find(s => s.id == supplierId)
                                                    : null;
                    groupedSupplierItems[supplierId] = {
                        id: supplierId,
                        supplier_name: originalSupplier ? originalSupplier.supplier_name : 'Unknown Supplier',
                        items: []
                    };
                }
                groupedSupplierItems[supplierId].items.push({
                    item_name: itemName,
                    item_quantity: itemQuantity,
                    item_price: itemPrice,
                    item_amount: itemAmount,
                    product_type: productType // Added product_type
                });
            }
        });
        suppliersData = Object.values(groupedSupplierItems);
        formData['suppliers'] = JSON.stringify(suppliersData);

        let paymentsData = [];
        $('#payment_details_table tbody .payment-row').each(function(index) {
            let row = $(this);
            let isSelected = row.find('.select_payment').is(':checked');
            if (isSelected) {
                paymentsData.push({
                    entity_type: row.data('entity-type'),
                    entity_id: row.data('entity-id'),
                    payee: row.find('td').eq(1).text().trim(),
                    invoice_number: row.find('.invoice_number').val(),
                    invoice_date: row.find('.invoice_date').val(),
                    payment_date: row.find('.payment_date').val(),
                    cheque_type: row.find('.cheque_type').val(),
                    cheque_number: row.find('.cheque_number').val(),
                    pd_acc_number: row.find('.pd_acc_number').val(),
                    ptm_amount: row.find('.ptm_amount').val(),
                    pon_number: $('#po_number').val(),
                    son_number: $('#son_number').val(),
                    po_amt: $('#po_amt').val(),
                    soa_number: $('#soa_number').val(),
                    jci_number: $('#jci_number').val(),
                    jci_type: '', // Add if available
                    created_by: '', // Add if available
                    jci_date: '', // Add if available
                    sell_order_number: $('#son_number').val()
                });
                if (formData['selected_payment_index'] === undefined) {
                    formData['selected_payment_index'] = index;
                }
            }
        });
        formData['payments'] = JSON.stringify(paymentsData);

        formData['grand_total_job_card_amount'] = $('#total_jc_amount').val();
        formData['grand_total_supplier_amount'] = $('#total_supplier_amount').val();
        formData['total_ptm_amount'] = $('#total_ptm_amount').val();

        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/payments/ajax_save_payment.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.payment_id) {
                        $('#payment_id').val(response.payment_id);
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error during save_payment: ", status, error, xhr.responseText);
                toastr.error('An error occurred while saving the payment. Please check the browser console for details.');
            }
        });
    }
});
</script>
