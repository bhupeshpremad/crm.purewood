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

// Fetch PO numbers for select dropdown
$stmt = $conn->prepare("SELECT id, po_number, sell_order_number, jci_number FROM po_main ORDER BY po_number ASC");
$stmt->execute();
$po_numbers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid" style="width: 85%;">
    <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary" id="formTitle">Add Payment Details</h6>
        </div>
        <div class="card-body">
            <form id="vendorPayment_form" autocomplete="off">
                <input type="hidden" name="payment_id" id="payment_id" value="">
                <input type="hidden" name="lead_id" id="lead_id" value="">

                <!-- PO Information -->
                <div class="row mb-3">
                <div class="col-md-3">
                    <label for="pon_number" class="form-label">Purchase Order Number (PON)</label>
                    <select class="form-control" id="pon_number" name="pon_number" required>
                        <option value="">Select PO Number</option>
                        <?php
                        foreach ($po_numbers as $po) {
                            echo '<option value="' . htmlspecialchars($po['id']) . '" data-son="' . htmlspecialchars($po['sell_order_number']) . '" data-soa="' . htmlspecialchars($po['soa_number'] ?? '') . '">' . htmlspecialchars($po['po_number']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                    <div class="col-md-3">
                        <label for="po_amt" class="form-label">Purchase Order Amount (PO AMT)</label>
                    <input type="number" class="form-control" id="po_amt" name="po_amt" required readonly>
                        <div id="po_amt_validation_msg" class="validation-message"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="son_number" class="form-label">Sale Order Number (SON)</label>
                        <input type="text" class="form-control" id="son_number" name="son_number" required readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="soa_number" class="form-label">Sale Order Amount (SOA)</label>
                        <input type="number" class="form-control" id="soa_number" name="soa_number" required readonly>
                    </div>
                </div>

                <!-- Job Card Details -->
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

                <!-- Supplier Information -->
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
                                    <th style="width: 10%;">Payment Category</th>
                                    <th style="width: 10%;">Cheque/RTGS Type</th>
                                    <th style="width: 10%;">Cheque/RTGS Number</th>
                                    <th style="width: 10%;">PD ACC Number</th>
                                    <th style="width: 10%;">Full/Partial</th>
                                    <th style="width: 8%;">Partial Amount</th>
                                    <th style="width: 8%;">Outstanding Amount</th>
                                    <th style="width: 7%;">CGST %</th>
                                    <th style="width: 7%;">CGST Amount</th>
                                    <th style="width: 7%;">SGST %</th>
                                    <th style="width: 7%;">SGST Amount</th>
                                    <th style="width: 7%;">IGST %</th>
                                    <th style="width: 7%;">IGST Amount</th>
                                    <th style="width: 10%;">Amount</th>
                                    <th style="width: 10%;">Invoice Date</th>
                                    <th style="width: 5%; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Payment rows will be added dynamically by JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-end">Total Payment Amount:</th>
                                    <th colspan="2">
                                        <input type="text" class="form-control d-inline-block w-auto" id="total_ptm_amount" name="total_ptm_amount" readonly="">
                                    </th>
                                    <th colspan="6" class="text-center">
                                        <button type="button" class="btn btn-success btn-sm add-payment-row"><i class="fas fa-plus"></i></button>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="14" class="text-end">Margin:</th>
                                    <th><span id="margin_percentage" class="ms-2">N/A</span></th>
                                </tr>
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

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmation Required</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalMessage"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSaveBtn">Continue Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    .validation-message { color: red; font-weight: bold; margin-top: 5px; }
    .margin-success { color: green; font-weight: bold; }
    .margin-warning { color: orange; font-weight: bold; }
    .margin-danger { color: red; font-weight: bold; }
    .table th, .table td { vertical-align: middle; padding: 0.5rem; }
    .table input.form-control, .table select.form-control { border: 1px solid #ced4da; padding: 0.375rem 0.75rem; height: auto; }
    .supplier-item-table th, .supplier-item-table td { padding: 0.3rem; }
    .supplier-item-table input.form-control { padding: 0.25rem 0.5rem; }
    .add-row-btn, .remove-row-btn { font-size: 0.9rem; padding: 0.3rem 0.6rem; line-height: 1; }
    .fas { font-family: 'Font Awesome 5 Free'; font-weight: 900; }
</style>





<script>
        $(document).ready(function() {
            let formSubmittedViaModal = false;
            let jobCardAmounts = {};
            let supplierIndex = 0;

            function updateJobCardSelects() {
                jobCardAmounts = {};

                $('#job_card_details_table tbody .jobcard-row').each(function() {
                    let jc_number = $(this).find('.jc_number').val();
                    let jc_amt = parseFloat($(this).find('.jc_amt').val()) || 0;
                    if (jc_number) {
                        jobCardAmounts[jc_number] = jc_amt;
                    }
                });

                $('#payment_details_table tbody .payment-row').each(function() {
                    let $jcSelect = $(this).find('.payment_jc_number');
                    let currentVal = $jcSelect.val();

                    $jcSelect.empty();
                    $jcSelect.append('<option value="">Select JC No.</option>');
                    for (let jc_num in jobCardAmounts) {
                        $jcSelect.append(`<option value="${jc_num}">${jc_num}</option>`);
                    }
                    $jcSelect.val(currentVal);
                });
            }

            function calculateJobCardAmounts() {
                let totalJobCardAmount = 0;
                $('#job_card_details_table tbody tr').each(function() {
                    let jcAmt = parseFloat($(this).find('td').eq(5).text()) || 0;
                    totalJobCardAmount += jcAmt;
                });
                $('#total_jc_amount').val(totalJobCardAmount.toFixed(2));
                updateJobCardSelects();
                checkTotalAmountsAgainstPO();
            }

            // Remove addNewJobCardRow call on initial load to avoid empty row
            // addNewJobCardRow();

        function addNewJobCardRow() {
            const newRow = `
                <tr class="jobcard-row">
                    <td><input type="text" class="form-control jc_number" name="jc_number[]" required></td>
                    <td><input type="text" class="form-control jc_type" name="jc_type[]" required></td>
                    <td><input type="text" class="form-control contracture_name" name="contracture_name[]" required></td>
                    <td><input type="number" class="form-control labour_cost" name="labour_cost[]" min="0" step="0.01" required></td>
                    <td><input type="number" class="form-control quantity" name="quantity[]" min="0" required></td>
                    <td><input type="number" class="form-control total_amount" name="total_amount[]" min="0" step="0.01" required></td>
                </tr>
            `;
            $('#job_card_details_table tbody').append(newRow);
            calculateJobCardAmounts();
        }

        function calculateSupplierItemAmounts() {
            // Removed supplier item amounts calculation as per user request
        }

        function addNewItemRowForSupplier($supplierGroup) {
            // Removed add new item row for supplier as per user request
        }

        function addNewSupplierRow() {
            // Removed add new supplier row as per user request
        }

        function calculatePaymentAmounts() {
            let totalPaymentAmount = 0;
            $('#payment_details_table tbody .payment-row').each(function() {
                let ptmAmount = parseFloat($(this).find('.ptm_amount').val()) || 0;
                totalPaymentAmount += ptmAmount;
            });
            $('#total_ptm_amount').val(totalPaymentAmount.toFixed(2));
        }

        function addNewPaymentRow() {
            const newRow = `
            <tr class="payment-row">
                <td>
                    <select class="form-control payment_category" name="payment_category[]" required>
                        <option value="">Select</option>
                        <option value="Job Card">Job Card</option>
                        <option value="Supplier">Supplier</option>
                    </select>
                </td>
                <td>
                    <select class="form-control cheque_type" name="cheque_type[]" required>
                        <option value="">Select Type</option>
                        <option value="Cheque">Cheque</option>
                        <option value="RTGS">RTGS</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control cheque_number" name="cheque_number[]" placeholder="Enter Cheque/RTGS Number">
                </td>
                <td><input type="number" class="form-control pd_acc_number" name="pd_acc_number[]" required></td>
                <td>
                    <select class="form-control payment_full_partial" name="payment_full_partial[]" required>
                        <option value="">Select</option>
                        <option value="Full">Full</option>
                        <option value="Partial">Partial</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control partial_amount" name="partial_amount[]" min="0" step="0.01" style="display:none;">
                </td>
                <td>
                    <input type="number" class="form-control outstanding_amount" name="outstanding_amount[]" readonly style="display:none;">
                </td>
                <td><input type="number" class="form-control cgst_percentage" name="cgst_percentage[]" min="0" max="100" step="0.01"></td>
                <td><input type="number" class="form-control cgst_amount" name="cgst_amount[]" readonly></td>
                <td><input type="number" class="form-control sgst_percentage" name="sgst_percentage[]" min="0" max="100" step="0.01"></td>
                <td><input type="number" class="form-control sgst_amount" name="sgst_amount[]" readonly></td>
                <td><input type="number" class="form-control igst_percentage" name="igst_percentage[]" min="0" max="100" step="0.01"></td>
                <td><input type="number" class="form-control igst_amount" name="igst_amount[]" readonly></td>
                <td><input type="number" class="form-control ptm_amount" name="ptm_amount[]" min="0" step="0.01" required></td>
                <td><input type="date" class="form-control payment_invoice_date" name="payment_invoice_date[]" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger remove-row-btn remove-payment-row"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            `;
            $('#payment_details_table tbody').append(newRow);
            updateJobCardSelects();
            calculatePaymentAmounts();
            // --- Custom logic for Payment Category, Full/Partial, and tax calculations ---
            const $row = $('#payment_details_table tbody .payment-row:last');

            function getTotals() {
                let totalJobCard = parseFloat($('#total_jc_amount').val()) || 0;
                let totalSupplier = 0;
                $('.supplier_total_items_amount').each(function() {
                    totalSupplier += parseFloat($(this).val()) || 0;
                });
                return { totalJobCard, totalSupplier };
            }

            // On Payment Category change, set amount fields
            $row.find('.payment_category').on('change', function() {
                const cat = $(this).val();
                const totals = getTotals();
                if (cat === 'Job Card') {
                    setAmountFields(totals.totalJobCard, $row);
                } else if (cat === 'Supplier') {
                    setAmountFields(totals.totalSupplier, $row);
                } else {
                    setAmountFields(0, $row);
                }
            });

            // On Full/Partial change
            $row.find('.payment_full_partial').on('change', function() {
                const cat = $row.find('.payment_category').val();
                const totals = getTotals();
                let total = 0;
                if (cat === 'Job Card') total = totals.totalJobCard;
                if (cat === 'Supplier') total = totals.totalSupplier;
                if ($(this).val() === 'Partial') {
                    $row.find('.partial_amount, .outstanding_amount').show();
                    $row.find('.partial_amount').val('');
                    $row.find('.outstanding_amount').val(total.toFixed(2));
                    $row.find('.ptm_amount').val('');
                } else if ($(this).val() === 'Full') {
                    $row.find('.partial_amount, .outstanding_amount').hide();
                    $row.find('.ptm_amount').val(total.toFixed(2));
                    $row.find('.partial_amount').val('');
                    $row.find('.outstanding_amount').val('');
                } else {
                    $row.find('.partial_amount, .outstanding_amount').hide();
                    $row.find('.ptm_amount').val('');
                    $row.find('.partial_amount').val('');
                    $row.find('.outstanding_amount').val('');
                }
                calcTaxes($row);
            });

            // On Partial Amount input
            $row.find('.partial_amount').on('input', function() {
                const cat = $row.find('.payment_category').val();
                const totals = getTotals();
                let total = 0;
                if (cat === 'Job Card') total = totals.totalJobCard;
                if (cat === 'Supplier') total = totals.totalSupplier;
                let partial = parseFloat($(this).val()) || 0;
                let outstanding = total - partial;
                $row.find('.outstanding_amount').val(outstanding.toFixed(2));
                $row.find('.ptm_amount').val(partial > 0 ? partial.toFixed(2) : '');
                calcTaxes($row);
            });

            // On Amount input or tax % input
            $row.find('.ptm_amount, .cgst_percentage, .sgst_percentage, .igst_percentage').on('input', function() {
                calcTaxes($row);
            });

            // Helper to set fields
            function setAmountFields(total, $row) {
                const fullPartial = $row.find('.payment_full_partial').val();
                if (fullPartial === 'Full') {
                    $row.find('.ptm_amount').val(total.toFixed(2));
                    $row.find('.partial_amount, .outstanding_amount').hide();
                } else if (fullPartial === 'Partial') {
                    $row.find('.partial_amount, .outstanding_amount').show();
                    $row.find('.partial_amount').val('');
                    $row.find('.outstanding_amount').val(total.toFixed(2));
                    $row.find('.ptm_amount').val('');
                } else {
                    $row.find('.ptm_amount').val('');
                    $row.find('.partial_amount, .outstanding_amount').hide();
                }
                calcTaxes($row);
            }

            // Helper to calculate taxes
            function calcTaxes($row) {
                const amt = parseFloat($row.find('.ptm_amount').val()) || 0;
                const cgst = parseFloat($row.find('.cgst_percentage').val()) || 0;
                const sgst = parseFloat($row.find('.sgst_percentage').val()) || 0;
                const igst = parseFloat($row.find('.igst_percentage').val()) || 0;
                $row.find('.cgst_amount').val(((cgst/100)*amt).toFixed(2));
                $row.find('.sgst_amount').val(((sgst/100)*amt).toFixed(2));
                $row.find('.igst_amount').val(((igst/100)*amt).toFixed(2));
            }
            $('#payment_details_table tbody').append(newRow);
            updateJobCardSelects();
            calculatePaymentAmounts();
            // Attach event for Full/Partial logic
            $('#payment_details_table tbody .payment-row:last .payment_full_partial').on('change', function() {
                const $row = $(this).closest('tr');
                if ($(this).val() === 'Partial') {
                    $row.find('.partial_amount, .outstanding_amount').show();
                } else {
                    $row.find('.partial_amount, .outstanding_amount').hide();
                }
            });
            // Attach event for CGST % auto-calc
            $('#payment_details_table tbody .payment-row:last .cgst_percentage, #payment_details_table tbody .payment-row:last .ptm_amount').on('input', function() {
                const $row = $(this).closest('tr');
                const cgstPercent = parseFloat($row.find('.cgst_percentage').val()) || 0;
                const amount = parseFloat($row.find('.ptm_amount').val()) || 0;
                $row.find('.cgst_amount').val(((cgstPercent/100)*amount).toFixed(2));
            });
        }

        function calculateAndDisplayMargin() {
            const poAmt = parseFloat($('#po_amt').val()) || 0;
            const totalJcAmount = parseFloat($('#total_jc_amount').val()) || 0;
            const grandTotalItemsAmount = parseFloat($('#grand_total_items_amount').val()) || 0;
            const totalVendorAndItemAmount = totalJcAmount + grandTotalItemsAmount;
            const $marginSpan = $('#margin_percentage');

            $marginSpan.removeClass('margin-success margin-warning margin-danger');

            if (poAmt > 0) {
                const margin = ((poAmt - totalVendorAndItemAmount) / poAmt) * 100;
                $marginSpan.text(`${margin.toFixed(2)}%`);

                if (margin >= 10) {
                    $marginSpan.addClass('margin-success');
                } else if (margin >= 5 && margin < 10) {
                    $marginSpan.addClass('margin-warning');
                } else {
                    $marginSpan.addClass('margin-danger');
                }
            } else {
                $marginSpan.text('N/A');
            }
        }

        function checkTotalAmountsAgainstPO() {
            const poAmt = parseFloat($('#po_amt').val()) || 0;
            const totalJcAmount = parseFloat($('#total_jc_amount').val()) || 0;
            const grandTotalItemsAmount = parseFloat($('#grand_total_items_amount').val()) || 0;
            const totalVendorAndItemAmount = totalJcAmount + grandTotalItemsAmount;
            const tenPercentPoAmt = poAmt * 1.10;
            const $validationMsg = $('#po_amt_validation_msg');

            if (poAmt === 0 && (totalJcAmount > 0 || grandTotalItemsAmount > 0)) {
                $validationMsg.text('PO Amount cannot be zero if Job Card or Item amounts are entered.');
                $validationMsg.show();
            } else if (totalVendorAndItemAmount > tenPercentPoAmt) {
                $validationMsg.text(`Combined JC & Item Amount (${totalVendorAndItemAmount.toFixed(2)}) exceeds 110% of PO Amount (${poAmt.toFixed(2)}).`);
                $validationMsg.show();
            } else {
                $validationMsg.hide();
            }
            calculateAndDisplayMargin();
        }

        $(document).on('click', '.add-jobcard-row', addNewJobCardRow);
        $(document).on('click', '.remove-jobcard-row', function() {
            if ($('#job_card_details_table tbody .jobcard-row').length > 1) {
                $(this).closest('.jobcard-row').remove();
                calculateJobCardAmounts();
            } else {
                toastr.warning('At least one Job Card row is required.');
            }
        });
        $(document).on('input', '.jc_number, .jc_amt', calculateJobCardAmounts);

        $(document).on('click', '.add-supplier-row', addNewSupplierRow);
        $(document).on('click', '.remove-supplier-group', function() {
            if ($('#suppliers_container .supplier-group').length > 1) {
                $(this).closest('.supplier-group').remove();
                calculateSupplierItemAmounts();
            } else {
                toastr.warning('At least one Supplier is required.');
            }
        });
        $(document).on('click', '.add-supplier-item-row', function() {
            addNewItemRowForSupplier($(this).closest('.supplier-group'));
        });
        $(document).on('click', '.remove-supplier-item-row', function() {
            const $supplierGroup = $(this).closest('.supplier-group');
            if ($supplierGroup.find('.supplier-item-row').length > 1) {
                $(this).closest('.supplier-item-row').remove();
                calculateSupplierItemAmounts();
            } else {
                toastr.warning('At least one item row is required per supplier.');
            }
        });
        $(document).on('input', '.supplier_invoice_amount, .item_quantity, .item_price', calculateSupplierItemAmounts);



        $(document).on('input', '#po_amt', function() {
            checkTotalAmountsAgainstPO();
        });

        $('#vendorPayment_form').submit(function(e) {
            e.preventDefault();

            checkTotalAmountsAgainstPO();

            if ($('#po_amt_validation_msg').is(':visible') && $('#po_amt_validation_msg').text().trim() !== '') {
                let message = $('#po_amt_validation_msg').text();
                $('#modalMessage').html(message + '<br><br>Do you want to continue saving?');
                $('#confirmationModal').modal('show');
                return;
            }

            if (formSubmittedViaModal) {
                submitFormData();
                formSubmittedViaModal = false;
                return;
            }

            let poAmt = parseFloat($('#po_amt').val()) || 0;
            let totalPtmAmount = parseFloat($('#total_ptm_amount').val()) || 0;

            let validationMessages = [];

            if (totalPtmAmount > poAmt) {
                validationMessages.push(`<strong>Total Payment Amount (${totalPtmAmount.toFixed(2)})</strong> exceeds <strong>Purchase Order Amount (${poAmt.toFixed(2)})</strong>.`);
            }

            if (validationMessages.length > 0) {
                $('#modalMessage').html(validationMessages.join('<br>') + '<br><br>Do you want to continue saving?');
                $('#confirmationModal').modal('show');
            } else {
                submitFormData();
            }
        });

        $('#confirmSaveBtn').click(function() {
            formSubmittedViaModal = true;
            $('#confirmationModal').modal('hide');
            $('#vendorPayment_form').submit();
        });

            function submitFormData() {
                let formDataArray = $('#vendorPayment_form').serializeArray();
                let formData = {};

                $.each(formDataArray, function(_, field) {
                    if (!field.name.includes('[]') && !field.name.includes('suppliers[')) {
                        formData[field.name] = field.value;
                    }
                });

                let jobCards = [];
                $('#job_card_details_table tbody .jobcard-row').each(function() {
                    let jobCard = {
                        jc_number: $(this).find('.jc_number').val(),
                        jc_type: $(this).find('.jc_type').val(),
                        contracture_name: $(this).find('.contracture_name').val(),
                        labour_cost: $(this).find('.labour_cost').val(),
                        quantity: $(this).find('.quantity').val(),
                        total_amount: $(this).find('.total_amount').val(),
                        jc_amt: $(this).find('.jc_amt').val()
                    };
                    jobCards.push(jobCard);
                });
                formData['job_cards'] = JSON.stringify(jobCards);

            let suppliersData = [];
            $('#suppliers_container .supplier-group').each(function() {
                let supplier = {
                    name: $(this).find('.supplier_name').val(),
                    invoice_number: $(this).find('.supplier_invoice_number').val(),
                    invoice_amount: $(this).find('.supplier_invoice_amount').val(),
                    items: []
                };
                $(this).find('.supplier-item-row').each(function() {
                    let item = {
                        name: $(this).find('.item_name').val(),
                        quantity: $(this).find('.item_quantity').val(),
                        price: $(this).find('.item_price').val(),
                        amount: $(this).find('.item_amount').val()
                    };
                    supplier.items.push(item);
                });
                suppliersData.push(supplier);
            });
            formData['suppliers'] = JSON.stringify(suppliersData);

            // --- Collect payment details table data ---
            let paymentsData = [];
            $('#payment_details_table tbody .payment-row').each(function() {
                let row = $(this);
                paymentsData.push({
                    payment_category: row.find('.payment_category').val(),
                    cheque_type: row.find('.cheque_type').val(),
                    cheque_number: row.find('.cheque_number').val(),
                    pd_acc_number: row.find('.pd_acc_number').val(),
                    payment_full_partial: row.find('.payment_full_partial').val(),
                    partial_amount: row.find('.partial_amount').val(),
                    outstanding_amount: row.find('.outstanding_amount').val(),
                    cgst_percentage: row.find('.cgst_percentage').val(),
                    cgst_amount: row.find('.cgst_amount').val(),
                    sgst_percentage: row.find('.sgst_percentage').val(),
                    sgst_amount: row.find('.sgst_amount').val(),
                    igst_percentage: row.find('.igst_percentage').val(),
                    igst_amount: row.find('.igst_amount').val(),
                    ptm_amount: row.find('.ptm_amount').val(),
                    payment_invoice_date: row.find('.payment_invoice_date').val()
                });
            });
            formData['payments'] = JSON.stringify(paymentsData);

            $.ajax({
                
                // url: 'ajax_save_payment.php',
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
                        // Reset the form immediately after successful save
                        $('#vendorPayment_form')[0].reset();
                        $('#job_card_details_table tbody').empty();
                        $('#suppliers_container').empty();
                        $('#payment_details_table tbody').empty();
                        
                        addNewJobCardRow();
                        addNewSupplierRow();
                        addNewPaymentRow();
                        
                        calculateJobCardAmounts();
                        calculateSupplierItemAmounts();
                        calculatePaymentAmounts();
                        $('#po_amt_validation_msg').hide().text('');
                        $('#payment_id').val('');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error, xhr.responseText);
                    toastr.error('An error occurred while saving the payment. Check console for details.');
                }
            });
        }

        function loadPaymentData(paymentId) {
            $.ajax({

                url: '<?php echo BASE_URL; ?>modules/payments/ajax_get_payment.php',
                type: 'GET',
                data: { payment_id: paymentId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let data = response.data;
                        $('#payment_id').val(paymentId);
                        $('#lead_id').val(data.payment.lead_id);
                        $('#pon_number').val(data.payment.pon_number);
                        $('#po_amt').val(data.payment.po_amt);
                        $('#son_number').val(data.payment.son_number);
                        $('#soa_number').val(data.payment.soa_number);
                        
                        $('#job_card_details_table tbody').empty();
                        $('#suppliers_container').empty();
                        $('#payment_details_table tbody').empty();

                            if (data.job_cards && data.job_cards.length > 0) {
                                data.job_cards.forEach(function(jobCard) {
                                    let newRow = `
                                        <tr class="jobcard-row">
                                            <td><input type="text" class="form-control jc_number" name="jc_number[]" value="${jobCard.jc_number || ''}" required></td>
                                            <td><input type="text" class="form-control jc_type" name="jc_type[]" value="${jobCard.jc_type || ''}" required></td>
                                            <td><input type="text" class="form-control contracture_name" name="contracture_name[]" value="${jobCard.contracture_name || ''}" required></td>
                                            <td><input type="number" class="form-control labour_cost" name="labour_cost[]" min="0" step="0.01" value="${jobCard.labour_cost || ''}" required></td>
                                            <td><input type="number" class="form-control quantity" name="quantity[]" min="0" value="${jobCard.quantity || ''}" required></td>
                                            <td><input type="number" class="form-control total_amount" name="total_amount[]" min="0" step="0.01" value="${jobCard.total_amount || ''}" required></td>
                                        </tr>
                                    `;
                                    $('#job_card_details_table tbody').append(newRow);
                                });
                            } else {
                                addNewJobCardRow();
                            }
                        calculateJobCardAmounts();

                        if (data.suppliers && data.suppliers.length > 0) {
                            supplierIndex = 0;
                            data.suppliers.forEach(function(supplier) {
                                const currentSupplierIndex = supplierIndex++;
                                const supplierGroupHtml = `
                                    <div class="card card-body mb-3 supplier-group" data-supplier-index="${currentSupplierIndex}">
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Supplier Name</label>
                                                <input type="text" class="form-control supplier_name" name="suppliers[${currentSupplierIndex}][name]" value="${supplier.supplier_name || ''}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Invoice Number</label>
                                                <input type="text" class="form-control supplier_invoice_number" name="suppliers[${currentSupplierIndex}][invoice_number]" value="${supplier.invoice_number || ''}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Invoice Amount</label>
                                                <input type="number" class="form-control supplier_invoice_amount" name="suppliers[${currentSupplierIndex}][invoice_amount]" min="0" step="0.01" value="${supplier.invoice_amount || ''}" required>
                                            </div>
                                            <div class="col-md-3 text-end">
                                                <button type="button" class="btn btn-danger remove-row-btn remove-supplier-group"><i class="fas fa-trash"></i> Remove Supplier</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="form-label">Items from this Supplier</label>
                                                <table class="table table-bordered supplier-item-table">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 30%;">Item Name</th>
                                                            <th style="width: 15%;">Quantity</th>
                                                            <th style="width: 20%;">Price</th>
                                                            <th style="width: 20%;">Amount</th>
                                                            <th style="width: 15%; text-align: center;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="3" class="text-end">Total for this Supplier:</th>
                                                            <th><input type="text" class="form-control supplier_total_items_amount" readonly></th>
                                                            <th class="text-center">
                                                                <button type="button" class="btn btn-success add-row-btn add-supplier-item-row"><i class="fas fa-plus"></i></button>
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $('#suppliers_container').append(supplierGroupHtml);
                                const $currentSupplierGroup = $('#suppliers_container .supplier-group').last();

                                if (supplier.items && supplier.items.length > 0) {
                                    supplier.items.forEach(function(item) {
                                        const itemRowHtml = `
                                            <tr class="supplier-item-row">
                                                <td><input type="text" class="form-control item_name" name="suppliers[${currentSupplierIndex}][items][][name]" value="${item.item_name || ''}" required></td>
                                                <td><input type="number" class="form-control item_quantity" name="suppliers[${currentSupplierIndex}][items][][quantity]" min="0" value="${item.item_quantity || ''}" required></td>
                                                <td><input type="number" class="form-control item_price" name="suppliers[${currentSupplierIndex}][items][][price]" min="0" step="0.01" value="${item.item_price || ''}" required></td>
                                                <td><input type="number" class="form-control item_amount" name="suppliers[${currentSupplierIndex}][items][][amount]" readonly value="${item.item_amount || ''}"></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger remove-row-btn remove-supplier-item-row"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        `;
                                        $currentSupplierGroup.find('.supplier-item-table tbody').append(itemRowHtml);
                                    });
                                } else {
                                    addNewItemRowForSupplier($currentSupplierGroup);
                                }
                            });
                        } else {
                            addNewSupplierRow();
                        }
                        calculateSupplierItemAmounts();

                        updateJobCardSelects();
                    console.log('Loaded payments data:', data.payments);
                    if (data.payments && data.payments.length > 0) {
                        data.payments.forEach(function(payment) {
                            // Use fallback keys if original keys are missing
                            let paymentCategory = payment.payment_category || payment.category || 'Job Card';
                            let paymentJcNumber = payment.payment_jc_number || payment.jc_number || '';
                            let paymentType = payment.payment_type || payment.type || '';
                            let chequeNumber = payment.cheque_number || payment.cheque_no || '';
                            let pdAccNumber = payment.pd_acc_number || payment.account_number || '';
                            let paymentFullPartial = payment.payment_full_partial || payment.full_partial || '';
                            let cgstPercentage = payment.cgst_percentage || payment.cgst_percent || '';
                            let cgstAmount = payment.cgst_amount || payment.cgst_amt || '';
                            let sgstPercentage = payment.sgst_percentage || payment.sgst_percent || '';
                            let sgstAmount = payment.sgst_amount || payment.sgst_amt || '';
                            let igstPercentage = payment.igst_percentage || payment.igst_percent || '';
                            let igstAmount = payment.igst_amount || payment.igst_amt || '';
                            let ptmAmount = payment.ptm_amount || payment.amount || '';
                            let paymentInvoiceDate = payment.payment_invoice_date || payment.invoice_date || '';

                            let newRow = `
                                <tr class="payment-row">
                                    <td>
                                        <select class="form-control payment_jc_number" name="payment_jc_number[]">
                                            <option value="">Select JC No.</option>
                                            ${Object.keys(jobCardAmounts).map(jc_num => `<option value="${jc_num}" ${jc_num === paymentJcNumber ? 'selected' : ''}>${jc_num}</option>`).join('')}
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control payment_type" name="payment_type[]" value="${paymentType}" required></td>
                                    <td><input type="text" class="form-control cheque_number" name="cheque_number[]" value="${chequeNumber}"></td>
                                    <td><input type="number" class="form-control pd_acc_number" name="pd_acc_number[]" value="${pdAccNumber}" required></td>
                                    <td>
                                        <select class="form-control payment_full_partial" name="payment_full_partial[]" required>
                                            <option value="">Select</option>
                                            <option value="Full" ${paymentFullPartial === 'Full' ? 'selected' : ''}>Full</option>
                                            <option value="Partial" ${paymentFullPartial === 'Partial' ? 'selected' : ''}>Partial</option>
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control cgst_percentage" name="cgst_percentage[]" min="0" max="100" step="0.01" value="${cgstPercentage}"></td>
                                    <td><input type="number" class="form-control cgst_amount" name="cgst_amount[]" readonly value="${cgstAmount}"></td>
                                    <td><input type="number" class="form-control sgst_percentage" name="sgst_percentage[]" min="0" max="100" step="0.01" value="${sgstPercentage}"></td>
                                    <td><input type="number" class="form-control sgst_amount" name="sgst_amount[]" readonly value="${sgstAmount}"></td>
                                    <td><input type="number" class="form-control igst_percentage" name="igst_percentage[]" min="0" max="100" step="0.01" value="${igstPercentage}"></td>
                                    <td><input type="number" class="form-control igst_amount" name="igst_amount[]" readonly value="${igstAmount}"></td>
                                    <td><input type="number" class="form-control ptm_amount" name="ptm_amount[]" min="0" step="0.01" value="${ptmAmount}" ${paymentFullPartial === 'Full' ? 'readonly' : ''} required></td>
                                    <td><input type="date" class="form-control payment_invoice_date" name="payment_invoice_date[]" value="${paymentInvoiceDate}" required></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger remove-row-btn remove-payment-row"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            $('#payment_details_table tbody').append(newRow);
                        });
                    } else {
                        addNewPaymentRow();
                    }
                        calculatePaymentAmounts();
                        calculateAndDisplayMargin();
                    } else {
                        toastr.error('Failed to load payment data: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error, xhr.responseText);
                    toastr.error('An error occurred while loading payment data.');
                }
            });
        }

        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            let regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            let results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        let paymentId = getUrlParameter('payment_id');
        if (paymentId) {
            loadPaymentData(paymentId);
            $('#formTitle').text('Edit Payment Details');
        } else {
            addNewJobCardRow();
            // Removed initial addNewSupplierRow() call to prevent empty supplier group on page load
            // addNewSupplierRow();
            addNewPaymentRow();
            
            calculateJobCardAmounts();
            calculateSupplierItemAmounts();
            calculatePaymentAmounts();
        }
    });

    // New event handler for PO Number select change to update SON and JCI numbers
    $('#pon_number').on('change', function() {
        if ($('#payment_id').val()) {
            // In edit mode, do not overwrite loaded data on PO number change
            return;
        }
        var selectedOption = $(this).find('option:selected');
        var son = selectedOption.data('son') || '';
        var jci = selectedOption.data('jci') || '';
        $('#son_number').val(son);
        $('#jci_number').val(jci);

    // Fetch PO AMT, SON, SOA and JCI items dynamically via AJAX
    var poId = $(this).val();
    if (poId) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/payments/ajax_get_po_amounts.php',
            type: 'GET',
            data: { po_id: poId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#po_amt').val(response.po_amt);
                    $('#soa_number').val(response.soa);
                    $('#son_number').val(response.son_number);
                } else {
                    $('#po_amt').val('');
                    $('#soa_number').val('');
                    $('#son_number').val('');
                    console.warn(response.message || 'Failed to fetch PO amounts.');
                }
            },
            error: function(xhr, status, error) {
                $('#po_amt').val('');
                $('#soa_number').val('');
                $('#son_number').val('');
                console.error('AJAX error fetching PO amounts:', error);
            }
        });

        // Fetch JCI items by PO number
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/payments/ajax_fetch_jci_items_by_sell_order.php',
            type: 'GET',
            data: { sell_order_number: $('#pon_number option:selected').text() },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var tbody = $('#job_card_details_table tbody');
                    tbody.empty();
                    if (response.jci_items.length > 0) {
                        response.jci_items.forEach(function(item, index) {
                            var row = `
                                <tr class="jobcard-row">
                                    <td><input type="text" class="form-control jc_number" name="jc_number[]" value="${item.jci_number || ''}" required></td>
                                    <td><input type="text" class="form-control jc_type" name="jc_type[]" value="${item.jci_type || ''}" required></td>
                                    <td><input type="text" class="form-control contracture_name" name="contracture_name[]" value="${item.contracture_name || ''}" required></td>
                                    <td><input type="number" class="form-control labour_cost" name="labour_cost[]" min="0" step="0.01" value="${item.labour_cost || ''}" required></td>
                                    <td><input type="number" class="form-control quantity" name="quantity[]" min="0" value="${item.quantity || ''}" required></td>
                                    <td><input type="number" class="form-control total_amount" name="total_amount[]" min="0" step="0.01" value="${item.total_amount || ''}" required></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger remove-row-btn remove-jobcard-row"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                        // Calculate and display total job card amount
                        let totalAmount = 0;
                        $('#job_card_details_table tbody tr').each(function() {
                            let amount = parseFloat($(this).find('td').eq(5).text()) || 0;
                            totalAmount += amount;
                        });
                        $('#total_jc_amount').val(totalAmount.toFixed(2));
                    } else {
                        tbody.append('<tr><td colspan="6" class="text-center">No JCI items found for selected PO number.</td></tr>');
                        $('#total_jc_amount').val('0.00');
                    }
                } else {
                    console.warn(response.message || 'Failed to fetch JCI items.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error fetching JCI items:', error);
            }
        });

        // Fetch Supplier items from purchase module by PO number
        $.ajax({
            url: '<?php echo BASE_URL; ?>modules/payments/ajax_fetch_supplier_items_by_po.php',
            type: 'GET',
            data: { po_id: $('#pon_number').val() },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var container = $('#suppliers_container');
                    container.empty();
                    if (response.supplier_items.length > 0) {
                        response.supplier_items.forEach(function(supplier, index) {
                            var totalAmount = 0;
                            supplier.items.forEach(function(item) {
                                totalAmount += parseFloat(item.amount) || 0;
                            });
                            var supplierGroup = `
                                <div class="card card-body mb-3 supplier-group" data-supplier-index="${index}">
                                    <
                                        <div class="col-lg-4">
                                            <label class="form-label">Supplier Name</label>
                                            <input type="text" class="form-control supplier_name" name="suppliers[${index}][name]" value="${supplier.supplier_name || ''}">
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label">Invoice Number</label>
                                            <input type="text" class="form-control supplier_invoice_number" name="suppliers[${index}][invoice_number]" value="${supplier.invoice_number || ''}">
                                        </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Invoice Amount</label>
                                    <input type="number" class="form-control supplier_invoice_amount" name="suppliers[${index}][invoice_amount]" value="${supplier.invoice_amount || totalAmount.toFixed(2)}" readonly disabled>
                                </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label">Items from this Supplier</label>
                                            <table class="table table-bordered supplier-item-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 30%;">Item Name</th>
                                                        <th style="width: 15%;">Quantity</th>
                                                        <th style="width: 20%;">Price</th>
                                                        <th style="width: 20%;">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                            `;
                            supplier.items.forEach(function(item) {
                                supplierGroup += `
                                    <tr>
                                        <td><input type="text" class="form-control item_name" name="suppliers[${index}][items][][name]" value="${item.item_name || ''}" readonly></td>
                                        <td><input type="number" class="form-control item_quantity" name="suppliers[${index}][items][][quantity]" value="${item.quantity || ''}" readonly></td>
                                        <td><input type="number" class="form-control item_price" name="suppliers[${index}][items][][price]" value="${item.price || ''}" readonly></td>
                                        <td><input type="number" class="form-control item_amount" name="suppliers[${index}][items][][amount]" value="${item.amount || ''}" readonly></td>
                                    </tr>
                                `;
                            });
                            supplierGroup += `
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="3" class="text-end">Total for this Supplier:</th>
                                                        <th><input type="number" class="form-control supplier_total_items_amount" value="${totalAmount.toFixed(2)}" readonly></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.append(supplierGroup);
                        });
                    } else {
                        container.append('<p>No supplier items found for selected PO number.</p>');
                    }
                } else {
                    console.warn(response.message || 'Failed to fetch supplier items.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error fetching supplier items:', error);
            }
        });
    } else {
        $('#po_amt').val('');
        $('#soa_number').val('');
        $('#son_number').val('');
        $('#job_card_details_table tbody').empty();
    }
});

    // Initialize SON, JCI, PO AMT and SOA if PO Number is pre-selected
    var initialSelectedOption = $('#pon_number').find('option:selected');
    if (initialSelectedOption.val()) {
        $('#son_number').val(initialSelectedOption.data('son') || '');
        $('#jci_number').val(initialSelectedOption.data('jci') || '');

        // Fetch PO AMT and SOA for initial selection
        var initialPoId = initialSelectedOption.val();
        if (initialPoId) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>modules/payments/ajax_get_po_amounts.php',
                type: 'GET',
                data: { po_id: initialPoId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#po_amt').val(response.po_amt);
                        $('#soa_number').val(response.soa);
                    } else {
                        $('#po_amt').val('');
                        $('#soa_number').val('');
                        console.warn(response.message || 'Failed to fetch PO amounts.');
                    }
                },
                error: function(xhr, status, error) {
                    $('#po_amt').val('');
                    $('#soa_number').val('');
                    console.error('AJAX error fetching PO amounts:', error);
                }
            });
        }
    }
</script>
