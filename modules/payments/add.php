<?php
error_reporting(0);
include_once '../../config/config.php';
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
?>

<div class="container-fluid">
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
                        <input type="text" class="form-control" id="pon_number" name="pon_number">
                    </div>
                    <div class="col-md-3">
                        <label for="po_amt" class="form-label">Purchase Order Amount (PO AMT)</label>
                        <input type="number" class="form-control" id="po_amt" name="po_amt" required>
                        <div id="po_amt_validation_msg" class="validation-message"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="son_number" class="form-label">Sale Order Number (SON)</label>
                        <input type="text" class="form-control" id="son_number" name="son_number" required>
                    </div>
                    <div class="col-md-3">
                        <label for="soa_number" class="form-label">Sale Order Amount (SOA)</label>
                        <input type="number" class="form-control" id="soa_number" name="soa_number" required>
                    </div>
                </div>

                <!-- Job Card Details -->
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Job Card Details</label>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="job_card_details_table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 35%;">Job Card No.</th>
                                    <th style="width: 35%;">JC Amount</th>
                                    <th style="width: 10%; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-end">Total Job Card Amount:</th>
                                    <th><input type="text" class="form-control" id="total_jc_amount" name="total_jc_amount" readonly></th>
                                    <th class="text-center">
                                        <button type="button" class="btn btn-success btn-sm add-jobcard-row"><i class="fas fa-plus"></i></button>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Supplier Information -->
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Supplier Information</label>
                    <div id="suppliers_container"></div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-primary btn-sm add-supplier-row"><i class="fas fa-plus"></i> Add Supplier</button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <table class="table table-bordered table-sm">
                                <tfoot>
                                    <tr>
                                        <th class="text-end">Grand Total Items Amount:</th>
                                        <th style="width: 20%;"><input type="text" class="form-control" id="grand_total_items_amount" name="grand_total_items_amount" readonly></th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment Mode Information -->
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Payment Mode Information</label>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="payment_details_table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 15%;">Payment Category</th>
                                    <th style="width: 15%;">Payment Type</th>
                                    <th style="width: 20%;">Cheque/RTGS Number</th>
                                    <th style="width: 15%;">PD ACC Number</th>
                                    <th style="width: 10%;">Full/Partial</th>
                                    <th style="width: 10%;">Amount</th>
                                    <th style="width: 10%;">Invoice Date</th>
                                    <th style="width: 5%; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Payment Amount:</th>
                                    <th>
                                        <input type="text" class="form-control d-inline-block w-auto" id="total_ptm_amount" name="total_ptm_amount" readonly>
                                    </th>
                                    <th colspan="2" class="text-center">
                                        <button type="button" class="btn btn-success btn-sm add-payment-row"><i class="fas fa-plus"></i></button>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="6" class="text-end">Margin:</th>
                                    <th><span id="margin_percentage" class="ms-2"></span></th>
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
        $('#job_card_details_table tbody .jobcard-row').each(function() {
            let jcAmt = parseFloat($(this).find('.jc_amt').val()) || 0;
            totalJobCardAmount += jcAmt;
        });
        $('#total_jc_amount').val(totalJobCardAmount.toFixed(2));
        updateJobCardSelects();
        checkTotalAmountsAgainstPO();
    }

    function addNewJobCardRow() {
        const newRow = `
            <tr class="jobcard-row">
                <td><input type="text" class="form-control jc_number" name="jc_number[]" required></td>
                <td><input type="number" class="form-control jc_amt" name="jc_amt[]" min="0" step="0.01" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger remove-row-btn remove-jobcard-row"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
        $('#job_card_details_table tbody').append(newRow);
        calculateJobCardAmounts();
    }

    function calculateSupplierItemAmounts() {
        let grandTotalItemsAmount = 0;
        $('#suppliers_container .supplier-group').each(function() {
            let supplierTotalAmount = 0;
            $(this).find('.supplier-item-row').each(function() {
                let quantity = parseFloat($(this).find('.item_quantity').val()) || 0;
                let price = parseFloat($(this).find('.item_price').val()) || 0;
                let itemAmount = quantity * price;
                $(this).find('.item_amount').val(itemAmount.toFixed(2));
                supplierTotalAmount += itemAmount;
            });
            $(this).find('.supplier_total_items_amount').val(supplierTotalAmount.toFixed(2));
            grandTotalItemsAmount += supplierTotalAmount;
        });
        $('#grand_total_items_amount').val(grandTotalItemsAmount.toFixed(2));
        checkTotalAmountsAgainstPO();
    }

    function addNewItemRowForSupplier($supplierGroup) {
        const index = $supplierGroup.data('supplier-index');
        const newRow = `
            <tr class="supplier-item-row">
                <td><input type="text" class="form-control item_name" name="suppliers[${index}][items][][name]" required></td>
                <td><input type="number" class="form-control item_quantity" name="suppliers[${index}][items][][quantity]" min="0" required></td>
                <td><input type="number" class="form-control item_price" name="suppliers[${index}][items][][price]" min="0" step="0.01" required></td>
                <td><input type="number" class="form-control item_amount" name="suppliers[${index}][items][][amount]" readonly></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger remove-row-btn remove-supplier-item-row"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
        $supplierGroup.find('.supplier-item-table tbody').append(newRow);
        calculateSupplierItemAmounts();
    }

    function addNewSupplierRow() {
        const currentSupplierIndex = supplierIndex++;
        const newSupplierGroup = `
            <div class="card card-body mb-3 supplier-group" data-supplier-index="${currentSupplierIndex}">
                <div class="row align-items-center mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" class="form-control supplier_name" name="suppliers[${currentSupplierIndex}][name]" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" class="form-control supplier_invoice_number" name="suppliers[${currentSupplierIndex}][invoice_number]" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Invoice Amount</label>
                        <input type="number" class="form-control supplier_invoice_amount" name="suppliers[${currentSupplierIndex}][invoice_amount]" min="0" step="0.01" required>
                    </div>
                    <div class="col-md-3 text-end">
                        <button type="button" class="btn btn-danger remove-row-btn remove-supplier-group mt-4"><i class="fas fa-trash"></i> Remove Supplier</button>
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
        $('#suppliers_container').append(newSupplierGroup);
        addNewItemRowForSupplier($('#suppliers_container .supplier-group').last()); 
        calculateSupplierItemAmounts();
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
                <option value="Job Card">Job Card</option>
                <option value="Supplier">Supplier</option>
            </select>
        </td>
        <td><input type="text" class="form-control payment_type" name="payment_type[]" required></td>
        <td><input type="text" class="form-control cheque_number" name="cheque_number[]"></td>
        <td><input type="number" class="form-control pd_acc_number" name="pd_acc_number[]" required></td>
        <td>
            <select class="form-control payment_full_partial" name="payment_full_partial[]" required>
                <option value="">Select</option>
                <option value="Full">Full</option>
                <option value="Partial">Partial</option>
            </select>
        </td>
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

    $(document).on('click', '.add-payment-row', addNewPaymentRow);
    $(document).on('click', '.remove-payment-row', function() {
        if ($('#payment_details_table tbody .payment-row').length > 1) {
            $(this).closest('.payment-row').remove();
            calculatePaymentAmounts();
        } else {
            toastr.warning('At least one payment row is required.');
        }
    });
    $(document).on('input', '.ptm_amount', calculatePaymentAmounts);

$(document).on('change', '.payment_full_partial', function() {
    let $row = $(this).closest('.payment-row');
    let selectedOption = $(this).val();
    let $amountInput = $row.find('.ptm_amount');

    $amountInput.prop('readonly', false);
    calculatePaymentAmounts();
});

$(document).on('change', '.payment_jc_number', function() {
    // Removed JC No. selection logic as JC No. is removed from payment details
});

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

        let payments = [];
        $('#payment_details_table tbody .payment-row').each(function() {
            let payment = {
                payment_jc_number: $(this).find('.payment_jc_number').val(),
                payment_type: $(this).find('.payment_type').val(),
                cheque_number: $(this).find('.cheque_number').val(),
                pd_acc_number: $(this).find('.pd_acc_number').val(),
                payment_full_partial: $(this).find('.payment_full_partial').val(),
                ptm_amount: $(this).find('.ptm_amount').val(),
                payment_invoice_date: $(this).find('.payment_invoice_date').val()
            };
            payments.push(payment);
        });
        formData['payments'] = JSON.stringify(payments);

        $.ajax({
            url: '/php_erp/modules/payments/ajax_save_payment.php',
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
            url: '/php_erp/modules/payments/ajax_get_payment.php',
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
                                    <td><input type="number" class="form-control jc_amt" name="jc_amt[]" min="0" step="0.01" value="${jobCard.jc_amt || ''}" required></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger remove-row-btn remove-jobcard-row"><i class="fas fa-trash"></i></button>
                                    </td>
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
                    if (data.payments && data.payments.length > 0) {
                        data.payments.forEach(function(payment) {
                            let newRow = `
                                <tr class="payment-row">
                                    <td>
                                        <select class="form-control payment_jc_number" name="payment_jc_number[]">
                                            <option value="">Select JC No.</option>
                                            ${Object.keys(jobCardAmounts).map(jc_num => `<option value="${jc_num}" ${jc_num === payment.payment_jc_number ? 'selected' : ''}>${jc_num}</option>`).join('')}
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control payment_type" name="payment_type[]" value="${payment.payment_type || ''}" required></td>
                                    <td><input type="text" class="form-control cheque_number" name="cheque_number[]" value="${payment.cheque_number || ''}"></td>
                                    <td><input type="number" class="form-control pd_acc_number" name="pd_acc_number[]" value="${payment.pd_acc_number || ''}" required></td>
                                    <td>
                                        <select class="form-control payment_full_partial" name="payment_full_partial[]" required>
                                            <option value="">Select</option>
                                            <option value="Full" ${payment.payment_full_partial === 'Full' ? 'selected' : ''}>Full</option>
                                            <option value="Partial" ${payment.payment_full_partial === 'Partial' ? 'selected' : ''}>Partial</option>
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control ptm_amount" name="ptm_amount[]" min="0" step="0.01" value="${payment.ptm_amount || ''}" ${payment.payment_full_partial === 'Full' ? 'readonly' : ''} required></td>
                                    <td><input type="date" class="form-control payment_invoice_date" name="payment_invoice_date[]" value="${payment.payment_invoice_date || ''}" required></td>
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
        addNewSupplierRow();
        addNewPaymentRow();
        
        calculateJobCardAmounts();
        calculateSupplierItemAmounts();
        calculatePaymentAmounts();
    }
});
</script>