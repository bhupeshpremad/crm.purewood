<?php
include_once '../../../config/config.php';
include_once '../../../include/inc/header.php';
include_once '../../sidebar.php';
?>

<style>
    #item_details_table th, #item_details_table td {
        vertical-align: middle;
        padding: 0.5rem;
    }

    #item_details_table input.form-control {
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
        height: auto;
    }

    .add-item-row, .remove-item-row {
        font-size: 0.9rem;
        padding: 0.3rem 0.6rem;
        line-height: 1;
    }

    .fas {
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
    }
</style>

<div class="container-fluid">
    <?php include_once '../../../include/inc/topbar.php'; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary" id="formTitle">Add Payment Details</h6>
        </div>
        <div class="card-body">

            <form id="vendorPayment_form" autocomplete="off">
                <input type="hidden" name="payment_id" id="payment_id" value="">
                <input type="hidden" name="lead_id" id="lead_id" value="">
                <fieldset class="mb-2">
                    <div class="alert alert-primary" role="alert">
                        PO Information
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="pon_number" class="form-label">Purchase Order Number (PON)</label>
                            <input type="text" class="form-control" id="pon_number" name="pon_number">
                        </div>
                        <div class="col-md-4">
                            <label for="po_amt" class="form-label">Purchase Order Amount (PO AMT)</label>
                            <input type="number" class="form-control" id="po_amt" name="po_amt" required>
                        </div>
                        <div class="col-md-4">
                            <label for="son_number" class="form-label">Sale Order Number(SON)</label>
                            <input type="text" class="form-control" id="son_number" name="son_number" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="soa_number" class="form-label">Sale Order Amount(SOA)</label>
                            <input type="number" class="form-control" id="soa_number" name="soa_number" required>
                        </div>
                        <div class="col-md-4">
                            <label for="jc_number" class="form-label">Job Card Number (JC No.)</label>
                            <input type="text" class="form-control" id="jc_number" name="jc_number" required>
                        </div>
                        <div class="col-md-4">
                            <label for="jc_amt" class="form-label">Job Card Amount (JC AMT.)</label>
                            <input type="number" class="form-control" id="jc_amt" name="jc_amt" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="mb-2">
                    <div class="alert alert-primary" role="alert">
                        Supplier Information
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="supplier_name" class="form-label">Supplier Name</label>
                            <input type="text" class="form-control" id="supplier_name" name="supplier_name">
                        </div>
                        <div class="col-md-4">
                            <label for="invoice_number" class="form-label">Invoice Number</label>
                            <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
                        </div>    
                        <div class="col-md-4">
                            <label for="invoice_amount" class="form-label">Invoice Amount</label>
                            <input type="number" class="form-control" id="invoice_amount" name="invoice_amount" required>
                        </div>    
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Item Details</label>
                            <table class="table table-bordered" id="item_details_table">
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
                                    <tr class="item-row">
                                        <td><input type="text" class="form-control item_name" name="item_name[]" required></td>
                                        <td><input type="number" class="form-control item_quantity" name="item_quantity[]" min="0" required></td>
                                        <td><input type="number" class="form-control item_price" name="item_price[]" min="0" step="0.01" required></td>
                                        <td><input type="number" class="form-control item_amount" name="item_amount[]" readonly></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger remove-item-row"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Items Amount:</th>
                                        <th><input type="text" class="form-control" id="total_items_amount" name="total_items_amount" readonly></th>
                                        <th class="text-center">
                                            <button type="button" class="btn btn-success add-item-row"><i class="fas fa-plus"></i></button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="mb-2">
                    <div class="alert alert-primary" role="alert">
                        Payment Mode Information
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="cheque_number" class="form-label">Cheque/RTGS Number</label>
                            <input type="text" class="form-control" id="cheque_number" name="cheque_number">
                        </div>
                        <div class="col-md-4">
                            <label for="ptm_amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="ptm_amount" name="ptm_amount" required>
                        </div>
                        <div class="col-md-4">
                            <label for="pd_acc_number" class="form-label">PD ACC Number</label>
                            <input type="number" class="form-control" id="pd_acc_number" name="pd_acc_number" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="payment_invoice_date" class="form-label">Invoice Date</label>
                            <input type="date" class="form-control" id="payment_invoice_date" name="payment_invoice_date" required>
                        </div>
                    </div>
                </fieldset>
                <button type="submit" class="btn btn-primary mt-3" id="submitBtn">Submit</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {

    function calculateAmounts() {
        let totalAmount = 0;
        $('#item_details_table tbody .item-row').each(function() {
            let quantity = parseFloat($(this).find('.item_quantity').val()) || 0;
            let price = parseFloat($(this).find('.item_price').val()) || 0;
            let itemAmount = quantity * price;
            $(this).find('.item_amount').val(itemAmount.toFixed(2));
            totalAmount += itemAmount;
        });
        $('#total_items_amount').val(totalAmount.toFixed(2));
    }

    calculateAmounts();

    function addNewItemRow() {
        const newRow = `
            <tr class="item-row">
                <td><input type="text" class="form-control item_name" name="item_name[]" required></td>
                <td><input type="number" class="form-control item_quantity" name="item_quantity[]" min="0" required></td>
                <td><input type="number" class="form-control item_price" name="item_price[]" min="0" step="0.01" required></td>
                <td><input type="number" class="form-control item_amount" name="item_amount[]" readonly></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger remove-item-row"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
        $('#item_details_table tbody').append(newRow);
        calculateAmounts();
    }

    $(document).on('click', '.add-item-row', function() {
        addNewItemRow();
    });

    $(document).on('click', '.remove-item-row', function() {
        if ($('#item_details_table tbody .item-row').length > 1) { // Prevent removing last row
            $(this).closest('.item-row').remove();
            calculateAmounts();
        } else {
            toastr.warning('At least one item row is required.');
        }
    });

    $(document).on('input', '.item_quantity, .item_price', function() {
        calculateAmounts();
    });

    $('#vendorPayment_form').submit(function(e) {
        e.preventDefault();

        let formDataArray = $(this).serializeArray();
        let formData = {};
        $.each(formDataArray, function(_, field) {
            formData[field.name] = field.value;
        });

        let items = [];
        $('#item_details_table tbody .item-row').each(function() {
            let item = {
                name: $(this).find('.item_name').val(),
                quantity: $(this).find('.item_quantity').val(),
                price: $(this).find('.item_price').val(),
                amount: $(this).find('.item_amount').val()
            };
            items.push(item);
        });

        formData['items'] = JSON.stringify(items);

        $.ajax({
            url: '/php_erp/purewood/modules/payments/ajax_save_payment.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.payment_id) {
                        $('#payment_id').val(response.payment_id);
                    }
                    if (!$('#payment_id').val() || $('#payment_id').val() === '0') { // Only reset if new record
                        $('#vendorPayment_form')[0].reset();
                        $('#item_details_table tbody').empty();
                        addNewItemRow();
                        calculateAmounts();
                        $('#payment_id').val(''); // Clear payment_id after reset for next new entry
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error('An error occurred while saving the payment.');
            }
        });
    });

    function loadPaymentData(paymentId) {
        $.ajax({
            url: '/php_erp/purewood/modules/payments/ajax_get_payment.php',
            type: 'GET',
            data: { payment_id: paymentId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let data = response.data;
                    $('#payment_id').val(paymentId); // THIS IS THE CRITICAL LINE ADDED/CONFIRMED
                    $('#lead_id').val(data.lead_id);
                    $('#pon_number').val(data.pon_number);
                    $('#po_amt').val(data.po_amt);
                    $('#son_number').val(data.son_number);
                    $('#soa_number').val(data.soa_number);
                    $('#jc_number').val(data.jc_number);
                    $('#jc_amt').val(data.jc_amt);
                    $('#supplier_name').val(data.supplier_name);
                    $('#invoice_number').val(data.invoice_number);
                    $('#invoice_amount').val(data.invoice_amount);
                    $('#cheque_number').val(data.cheque_number);
                    $('#ptm_amount').val(data.ptm_amount);
                    $('#pd_acc_number').val(data.pd_acc_number);
                    $('#payment_invoice_date').val(data.payment_invoice_date);

                    $('#item_details_table tbody').empty();

                    if (data.items && data.items.length > 0) {
                        data.items.forEach(function(item) {
                            let newRow = `
                                <tr class="item-row">
                                    <td><input type="text" class="form-control item_name" name="item_name[]" value="${item.item_name}" required></td>
                                    <td><input type="number" class="form-control item_quantity" name="item_quantity[]" min="0" value="${item.item_quantity}" required></td>
                                    <td><input type="number" class="form-control item_price" name="item_price[]" min="0" step="0.01" value="${item.item_price}" required></td>
                                    <td><input type="number" class="form-control item_amount" name="item_amount[]" readonly value="${item.item_amount}"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger remove-item-row"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            $('#item_details_table tbody').append(newRow);
                        });
                    } else {
                        addNewItemRow();
                    }
                    calculateAmounts();
                } else {
                    toastr.error('Failed to load payment data.');
                }
            },
            error: function() {
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
        $('#formTitle').text('Edit Payment Details'); // Change title for edit mode
    } else {
        addNewItemRow(); // Add an empty row for new entry if no paymentId
    }

});
</script>