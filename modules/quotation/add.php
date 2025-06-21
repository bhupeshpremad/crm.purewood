<?php
if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', realpath(__DIR__ . '/../../'));
}
include_once ROOT_DIR_PATH . '/config/config.php';
include_once ROOT_DIR_PATH . '/include/inc/header.php';
session_start();
$user_type = $_SESSION['user_type'] ?? 'guest';

if ($user_type === 'superadmin') {
    include_once __DIR__ . '/../../superadmin/sidebar.php';
} elseif ($user_type === 'salesadmin') {
    include_once __DIR__ . '/../../salesadmin/sidebar.php';
} else {
    // Default or guest sidebar or no sidebar
    // include_once __DIR__ . '/../../include/inc/sidebar.php';
}

$editMode = false;
$quotation = null;
$products = [];
$error = null; // Initialize error variable

$disableLeadDropdown = false;
$selectedLeadId = null;
if (isset($_GET['lead_id']) && is_numeric($_GET['lead_id'])) {
    $selectedLeadId = intval($_GET['lead_id']);
    $disableLeadDropdown = true;
}

global $conn;

// Essential: Check if $conn is a valid PDO object before proceeding
if (!$conn instanceof PDO) {
    $error = 'Database connection not established. Check config.php.';
    $approvedLeads = []; // Ensure approvedLeads is initialized even on connection failure
} else {
    try {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $editMode = true;
            $quotationId = intval($_GET['id']);

            $stmt = $conn->prepare("SELECT * FROM quotations WHERE id = :id");
            if ($stmt === false) {
                throw new Exception('Failed to prepare statement for fetching quotation.');
            }
            $stmt->execute([':id' => $quotationId]);
            $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($quotation) {
                $stmt2 = $conn->prepare("SELECT * FROM quotation_products WHERE quotation_id = :id");
                if ($stmt2 === false) {
                    throw new Exception('Failed to prepare statement for fetching quotation products.');
                }
                $stmt2->execute([':id' => $quotationId]);
                $products = $stmt2->fetchAll(PDO::FETCH_ASSOC); // This will now be called on a PDOStatement object
            } else {
                $error = "Quotation not found.";
            }
        }

        $stmt = $conn->prepare("SELECT * FROM leads WHERE approve = 1 ORDER BY lead_number ASC");
        if ($stmt === false) {
            throw new Exception('Failed to prepare statement for fetching approved leads.');
        }
        $stmt->execute();
        $approvedLeads = $stmt->fetchAll(PDO::FETCH_ASSOC); // This will now be called on a PDOStatement object
    } catch (PDOException $e) {
        $approvedLeads = [];
        $error = "Error fetching data: " . $e->getMessage();
    } catch (Exception $e) { // Catch the custom exceptions thrown for prepare failures
        $approvedLeads = [];
        $error = "Application error: " . $e->getMessage();
    }
}
?>

<?php if ($error): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="container-fluid" style="width: 85%;">
    <?php include_once ROOT_DIR_PATH . '/include/inc/topbar.php'; ?>
    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <form id="quotationForm" enctype="multipart/form-data">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?php echo $editMode ? 'Edit' : 'Add'; ?> Quotation</h6>
            </div>
            <div class="card-body">
                <fieldset class="mb-4">
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="lead_number" class="form-label">Lead Number</label>
                            <select class="form-control" id="lead_number" name="lead_id" required <?php echo $disableLeadDropdown ? 'disabled' : ''; ?>>
                                <option value="">Select Lead</option>
                                <?php foreach ($approvedLeads as $lead) : ?>
                                    <option value="<?php echo htmlspecialchars($lead['id']); ?>"
                                        data-company-name="<?php echo htmlspecialchars($lead['company_name']); ?>"
                                        data-contact-email="<?php echo htmlspecialchars($lead['contact_email']); ?>"
                                        data-contact-phone="<?php echo htmlspecialchars($lead['contact_phone']); ?>"
                                        <?php
                                        if ($disableLeadDropdown && $selectedLeadId == $lead['id']) {
                                            echo 'selected';
                                        } elseif ($editMode && $quotation && $quotation['lead_id'] == $lead['id']) {
                                            echo 'selected';
                                        }
                                        ?>
                                    >
                                        <?php echo htmlspecialchars($lead['lead_number'] . ' - ' . $lead['company_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quotation_date" class="form-label">Date of Quote Raised</label>
                            <input type="date" class="form-control" id="quotation_date" name="quotation_date" value="<?php echo $editMode && $quotation ? htmlspecialchars($quotation['quotation_date']) : date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quotation_number" class="form-label">Quotation Number</label>
                            <input type="text" class="form-control" id="quotation_number" name="quotation_number" readonly value="<?php echo $editMode && $quotation ? htmlspecialchars($quotation['quotation_number']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" readonly value="<?php echo $editMode && $quotation ? htmlspecialchars($quotation['customer_name']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="customer_email" class="form-label">Customer Email</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email" readonly value="<?php echo $editMode && $quotation ? htmlspecialchars($quotation['customer_email']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="customer_phone" class="form-label">Customer Phone</label>
                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone" readonly value="<?php echo $editMode && $quotation ? htmlspecialchars($quotation['customer_phone']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="delivery_term" class="form-label">Payment Terms</label>
                            <input type="text" class="form-control" id="delivery_term" name="delivery_term" value="<?php echo $editMode && $quotation ? htmlspecialchars($quotation['delivery_term']) : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="terms_of_delivery" class="form-label">Terms of Delivery</label>
                            <input type="text" class="form-control" id="terms_of_delivery" name="terms_of_delivery" value="<?php echo $editMode && $quotation ? htmlspecialchars($quotation['terms_of_delivery']) : ''; ?>">
                        </div>
                    </div>
                </fieldset>

                <div>
                    <button type="button" class="btn btn-primary mt-3 mb-3" data-toggle="modal" data-target="#addProductModal">Add Product</button>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive" style="overflow-x:auto; max-width: 100%; white-space: nowrap;">
                                <table id="productTable" class="table table-bordered table-striped table-hover" style="min-width: 1200px;">
                                    <thead>
                                        <tr>
                                            <th>Sno</th>
                                            <th>Product Image</th>
                                            <th>Item Name</th>
                                            <th>Item Code</th>
                                            <th>Assembly</th>
                                            <th colspan="3" class="text-center">Item Dimension (cms)</th>
                                            <th colspan="3" class="text-center">Box Dimension (cms)</th>
                                            <th>CBM</th>
                                            <th>Wood/Marble Type</th>
                                            <th>No. of Packet</th>
                                            <th>Iron Gauge</th>
                                            <th>MDF Finish</th>
                                            <th>MOQ</th>
                                            <th>Price USD</th>
                                            <th>Total USD</th>
                                            <th>Comments</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <th></th><th></th><th></th><th></th><th></th>
                                            <th class="small">H</th><th class="small">W</th><th class="small">D</th>
                                            <th class="small">H</th><th class="small">W</th><th class="small">D</th>
                                            <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($editMode && !empty($products)) : ?>
                                            <?php foreach ($products as $index => $product) : ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td data-image-name="<?php echo htmlspecialchars($product['product_image_name']); ?>">
                                                        <?php if (!empty($product['product_image_name'])) : ?>
                                                            <img src="<?php echo '/assets/images/upload/quotation/' . htmlspecialchars($product['product_image_name']); ?>" alt="Product Image" style="max-width: 80px; max-height: 80px;">
                                                        <?php else : ?>
                                                            <img src="/php_erp/purewood/assets/images/no-image.png" alt="No Image" style="max-width: 80px; max-height: 80px;">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($product['item_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['item_code']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['assembly']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['item_h']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['item_w']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['item_d']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['box_h']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['box_w']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['box_d']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['cbm']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['wood_type']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['no_of_packet']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['iron_gauge']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['mdf_finish']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['price_usd']); ?></td>
                                                    <td><?php echo number_format($product['quantity'] * $product['price_usd'], 2); ?></td>
                                                    <td><?php echo !empty($product['comments']) ? htmlspecialchars($product['comments']) : 'No Comment'; ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-sm editProductBtn me-2" title="Edit"><i class="fas fa-edit"></i></button>
                                                        <button type="button" class="btn btn-danger btn-sm removeProductBtn" title="Remove"><i class="fas fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="lead_id" id="lead_id" value="<?php echo $editMode && $quotation ? htmlspecialchars($quotation['lead_id']) : ''; ?>">

            <button type="submit" class="btn btn-primary mt-3 mb-3 mx-3"><?php echo $editMode ? 'Update Quotation' : 'Submit Quotation'; ?></button>
            <?php if ($editMode): ?>
                <input type="hidden" name="quotation_id" value="<?php echo htmlspecialchars($quotation['id']); ?>">
            <?php endif; ?>
        </form>

    </div>

    
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modal_item_name" class="form-label">Item Name</label>
                            <input type="text" class="form-control rounded-0" id="modal_item_name">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_item_code" class="form-label">Item Code</label>
                            <input type="text" class="form-control rounded-0" id="modal_item_code">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modal_description" class="form-label">Description</label>
                        <textarea class="form-control rounded-0" id="modal_description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="modal_product_image" class="form-label">Product Image</label>
                        <input type="file" class="form-control rounded-0" id="modal_product_image" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="modal_assembly" class="form-label">Assembly</label>
                        <input type="text" class="form-control rounded-0" id="modal_assembly">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="modal_item_h" class="form-label">Item H (cms)</label>
                            <input type="number" class="form-control rounded-0" id="modal_item_h" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_item_w" class="form-label">Item W (cms)</label>
                            <input type="number" class="form-control rounded-0" id="modal_item_w" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_item_d" class="form-label">Item D (cms)</label>
                            <input type="number" class="form-control rounded-0" id="modal_item_d" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_cbm" class="form-label">CBM</label>
                            <input type="number" class="form-control rounded-0" id="modal_cbm" step="0.0001" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="modal_wood_type" class="form-label">Wood Type</label>
                            <input type="text" class="form-control rounded-0" id="modal_wood_type">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_no_of_packet" class="form-label">No. of Packet</label>
                            <input type="number" class="form-control rounded-0" id="modal_no_of_packet" step="1">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_iron_gauge" class="form-label">Iron Gauge</label>
                            <input type="number" class="form-control rounded-0" id="modal_iron_gauge" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_product_finish" class="form-label">Product Finish</label>
                            <input type="text" class="form-control rounded-0" id="modal_product_finish">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control rounded-0" id="modal_quantity" step="1">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_price_per_unit" class="form-label">Price Per Unit (USD)</label>
                            <input type="number" class="form-control rounded-0" id="modal_price_per_unit" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label for="modal_total_usd" class="form-label">Total USD</label>
                            <input type="number" class="form-control rounded-0" id="modal_total_usd" step="0.01" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="modal_comments" class="form-label">Comments</label>
                            <textarea class="form-control rounded-0" id="modal_comments" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn bg-gradient-primary text-white rounded-0" id="addProductButton">Add Product</button>
                </div>
            </div>
        </div>
    </div>

    <div>
        <?php include_once ROOT_DIR_PATH . '/include/inc/footer.php'; ?>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        var productImages = [];
        
        var editMode = <?php echo $editMode ? 'true' : 'false'; ?>;
        var disableLeadDropdown = <?php echo $disableLeadDropdown ? 'true' : 'false'; ?>;

            if (editMode) {
                var existingProductCount = $('#productTable tbody tr').length;
                for (var i = 0; i < existingProductCount; i++) {
                    productImages.push(null);
                }
            }

            if (disableLeadDropdown) {
                // Trigger change event on lead_number to auto-populate fields on page load
                $('#lead_number').trigger('change');
            }

        function populateLeadDetails() {
            var selectedOption = $('#lead_number').find('option:selected');
            console.log('Selected lead option:', selectedOption);
            $('#customer_name').val(selectedOption.data('company-name') || '');
            $('#customer_email').val(selectedOption.data('contact-email') || '');
            $('#customer_phone').val(selectedOption.data('contact-phone') || '');

            // Fetch latest quotation number via AJAX when lead changes
            $.ajax({
                url: '/php_erp/modules/quotation/get_latest_quotation_number.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('AJAX response for latest quotation number:', data);
                    if (data.success && data.latest_quotation_number) {
                        $('#quotation_number').val(data.latest_quotation_number);
                    } else {
                        $('#quotation_number').val('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error fetching latest quotation number:', error);
                    $('#quotation_number').val('');
                }
            });
        }

        $('#lead_number').change(function() {
            populateLeadDetails();
        });

        if (disableLeadDropdown) {
            // Trigger auto-population directly on page load before disabling dropdown
            populateLeadDetails();
            $('#lead_number').prop('disabled', true);
        }

        function calculateTotalUSD() {
            var price = parseFloat($('#modal_price_per_unit').val()) || 0;
            var quantity = parseInt($('#modal_quantity').val()) || 0;
            $('#modal_total_usd').val((price * quantity).toFixed(2));
        }

        function calculateCBM() {
            var boxH = parseFloat($('#modal_box_h').val()) || 0;
            var boxW = parseFloat($('#modal_box_w').val()) || 0;
            var boxD = parseFloat($('#modal_box_d').val()) || 0;
            var cbm = (boxH * boxW * boxD) / 1000000; // convert cubic cm to cubic meters
            $('#modal_cbm').val(cbm.toFixed(2));
        }

        $('#modal_box_h, #modal_box_w, #modal_box_d').on('input', calculateCBM);

        $('#modal_price_per_unit, #modal_quantity').on('input', calculateTotalUSD);

        $('#addProductButton').click(function() {
            if ($(this).data('edit-index') !== undefined) {
                // Update existing product row
                var editIndex = $(this).data('edit-index');
                var productData = {
                    item_name: $('#modal_item_name').val(),
                    item_code: $('#modal_item_code').val(),
                    description: $('#modal_description').val(),
                    assembly: $('#modal_assembly').val(),
                    item_h: $('#modal_item_h').val(),
                    item_w: $('#modal_item_w').val(),
                    item_d: $('#modal_item_d').val(),
                    box_h: $('#modal_box_h').val(),
                    box_w: $('#modal_box_w').val(),
                    box_d: $('#modal_box_d').val(),
                    cbm: $('#modal_cbm').val(),
                    wood_type: $('#modal_wood_type').val(),
                    no_of_packet: $('#modal_no_of_packet').val(),
                    iron_gauge: $('#modal_iron_gauge').val(),
                    mdf_finish: $('#modal_product_finish').val(),
                    quantity: $('#modal_quantity').val(),
                    price_usd: $('#modal_price_per_unit').val(),
                    comments: $('#modal_comments').val()
                };

                var imageFile = $('#modal_product_image')[0].files[0];
                var imageUrl = '';
                if (imageFile) {
                    imageUrl = URL.createObjectURL(imageFile);
                    productImages[editIndex] = imageFile;
                }

                var $row = $('#productTable tbody tr').eq(editIndex);
                $row.find('td').eq(1).html('<img src="' + (imageUrl || $row.find('td').eq(1).find('img').attr('src')) + '" alt="Product Image" style="max-width: 80px; max-height: 80px;">');
                $row.find('td').eq(2).text(productData.item_name + ' / ' + productData.item_code);
                $row.find('td').eq(3).text(productData.description);
                $row.find('td').eq(4).text(productData.assembly);
                $row.find('td').eq(5).text(productData.item_h);
                $row.find('td').eq(6).text(productData.item_w);
                $row.find('td').eq(7).text(productData.item_d);
                $row.find('td').eq(8).text(productData.box_h);
                $row.find('td').eq(9).text(productData.box_w);
                $row.find('td').eq(10).text(productData.box_d);
                $row.find('td').eq(11).text(productData.cbm);
                $row.find('td').eq(12).text(productData.wood_type);
                $row.find('td').eq(13).text(productData.no_of_packet);
                $row.find('td').eq(14).text(productData.iron_gauge);
                $row.find('td').eq(15).text(productData.mdf_finish);
                $row.find('td').eq(16).text(productData.quantity);
                $row.find('td').eq(17).text(productData.price_usd);
                $row.find('td').eq(18).text((productData.quantity * productData.price_usd).toFixed(2));
                $row.find('td').eq(19).text(productData.comments);

                $('#addProductModal').modal('hide');
                $('#addProductModal input, #addProductModal textarea').val('');
                $('#modal_product_image').val('');
                $('#addProductButton').removeData('edit-index').text('Add Product');
                updateTotalAmount();
            } else {
                var productData = {
                    item_name: $('#modal_item_name').val(),
                    item_code: $('#modal_item_code').val(),
                    description: $('#modal_description').val(),
                    assembly: $('#modal_assembly').val(),
                    item_h: $('#modal_item_h').val(),
                    item_w: $('#modal_item_w').val(),
                    item_d: $('#modal_item_d').val(),
                    box_h: $('#modal_box_h').val(),
                    box_w: $('#modal_box_w').val(),
                    box_d: $('#modal_box_d').val(),
                    cbm: $('#modal_cbm').val(),
                    wood_type: $('#modal_wood_type').val(),
                    no_of_packet: $('#modal_no_of_packet').val(),
                    iron_gauge: $('#modal_iron_gauge').val(),
                    mdf_finish: $('#modal_product_finish').val(),
                    quantity: $('#modal_quantity').val(),
                    price_usd: $('#modal_price_per_unit').val(),
                    comments: $('#modal_comments').val()
                };

                var rowCount = $('#productTable tbody tr').length;

                var imageFile = $('#modal_product_image')[0].files[0];
                var imageUrl = '';
                if (imageFile) {
                    imageUrl = URL.createObjectURL(imageFile);
                    productImages.splice(rowCount, 0, imageFile);
                } else {
                    productImages.splice(rowCount, 0, null);
                }

                var newRow = '<tr>' +
                    '<td>' + (rowCount + 1) + '</td>' +
                    '<td><img src="' + imageUrl + '" alt="Product Image" style="max-width: 80px; max-height: 80px;"></td>' +
                    '<td>' + productData.item_name + ' / ' + productData.item_code + '</td>' +
                    '<td>' + productData.description + '</td>' +
                    '<td>' + productData.assembly + '</td>' +
                    '<td>' + productData.item_h + '</td>' +
                    '<td>' + productData.item_w + '</td>' +
                    '<td>' + productData.item_d + '</td>' +
                    '<td>' + productData.box_h + '</td>' +
                    '<td>' + productData.box_w + '</td>' +
                    '<td>' + productData.box_d + '</td>' +
                    '<td>' + productData.cbm + '</td>' +
                    '<td>' + productData.wood_type + '</td>' +
                    '<td>' + productData.no_of_packet + '</td>' +
                    '<td>' + productData.iron_gauge + '</td>' +
                    '<td>' + productData.mdf_finish + '</td>' +
                    '<td>' + productData.quantity + '</td>' +
                    '<td>' + productData.price_usd + '</td>' +
                    '<td class="totalUsdCell">' + (productData.quantity * productData.price_usd).toFixed(2) + '</td>' +
                    '<td>' + productData.comments + '</td>' +
                '<td><button type="button" class="btn btn-primary btn-sm editProductBtn me-2" title="Edit"><i class="fas fa-edit"></i></button><button type="button" class="btn btn-danger btn-sm removeProductBtn" title="Remove"><i class="fas fa-trash-alt"></i></button></td>' +
                    '</tr>';

                $('#productTable tbody').append(newRow);

                $('#addProductModal').modal('hide');

                $('#addProductModal input, #addProductModal textarea').val('');
                $('#modal_product_image').val('');
                updateTotalAmount();
            }
        });

        // Adjust buttons inline style with gap
        $('#productTable').on('mouseenter', 'td > button', function() {
            $(this).parent().find('button').css({'display': 'inline-block', 'margin-right': '5px', 'vertical-align': 'middle'});
        });
        // Edit product button click handler
        $('#productTable').on('click', '.editProductBtn', function() {
            var $row = $(this).closest('tr');
            var index = $row.index();

            // Populate modal fields from table row safely
            var itemNameCode = $row.find('td').eq(2).text().split(' / ');
            $('#modal_item_name').val(itemNameCode.length > 0 ? itemNameCode[0].trim() : '');
            $('#modal_item_code').val(itemNameCode.length > 1 ? itemNameCode[1].trim() : '');
            $('#modal_description').val($row.find('td').eq(3).text() ? $row.find('td').eq(3).text().trim() : '');
            $('#modal_assembly').val($row.find('td').eq(4).text() ? $row.find('td').eq(4).text().trim() : '');
            $('#modal_item_h').val($row.find('td').eq(5).text() ? $row.find('td').eq(5).text().trim() : '');
            $('#modal_item_w').val($row.find('td').eq(6).text() ? $row.find('td').eq(6).text().trim() : '');
            $('#modal_item_d').val($row.find('td').eq(7).text() ? $row.find('td').eq(7).text().trim() : '');
            $('#modal_box_h').val($row.find('td').eq(8).text() ? $row.find('td').eq(8).text().trim() : '');
            $('#modal_box_w').val($row.find('td').eq(9).text() ? $row.find('td').eq(9).text().trim() : '');
            $('#modal_box_d').val($row.find('td').eq(10).text() ? $row.find('td').eq(10).text().trim() : '');
            $('#modal_cbm').val($row.find('td').eq(11).text() ? $row.find('td').eq(11).text().trim() : '');
            $('#modal_no_of_packet').val($row.find('td').eq(13).text() ? $row.find('td').eq(13).text().trim() : '');
            $('#modal_quantity').val($row.find('td').eq(16).text() ? $row.find('td').eq(16).text().trim() : '');

            // Recalculate CBM based on box dimensions
            calculateCBM();

            $('#modal_wood_type').val($row.find('td').eq(12).text() ? $row.find('td').eq(12).text().trim() : '');
            $('#modal_no_of_packet').val($row.find('td').eq(13).text() ? $row.find('td').eq(13).text().trim() : '');
            $('#modal_iron_gauge').val($row.find('td').eq(14).text() ? $row.find('td').eq(14).text().trim() : '');
            $('#modal_product_finish').val($row.find('td').eq(15).text() ? $row.find('td').eq(15).text().trim() : '');
            $('#modal_quantity').val($row.find('td').eq(16).text() ? $row.find('td').eq(16).text().trim() : '');
            $('#modal_price_per_unit').val($row.find('td').eq(17).text() ? $row.find('td').eq(17).text().trim() : '');
            $('#modal_comments').val($row.find('td').eq(19).text() ? $row.find('td').eq(19).text().trim() : '');

            // Clear file input (cannot set value programmatically for security reasons)
            $('#modal_product_image').val('');

            // Set edit index on addProductButton
            $('#addProductButton').data('edit-index', index).text('Update Product');

            // Show modal
            $('#addProductModal').modal('show');
        });

        $('#productTable').on('click', '.removeProductBtn', function() {
            var rowIndex = $(this).closest('tr').index();
            productImages.splice(rowIndex, 1);
            $(this).closest('tr').remove();
            $('#productTable tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        });

        $('#quotationForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData();

            formData.append('lead_id', $('#lead_number').val());
            formData.append('quotation_date', $('#quotation_date').val());
            formData.append('quotation_number', $('#quotation_number').val());
            formData.append('customer_name', $('#customer_name').val());
            formData.append('customer_email', $('#customer_email').val());
            formData.append('customer_phone', $('#customer_phone').val());
            formData.append('delivery_term', $('#delivery_term').val());
            formData.append('terms_of_delivery', $('#terms_of_delivery').val());

            var editMode = <?php echo $editMode ? 'true' : 'false'; ?>;
            if (editMode) {
                formData.append('quotation_id', '<?php echo $quotation ? $quotation["id"] : ""; ?>');
            }

            var products = [];
            $('#productTable tbody tr').each(function(index, tr) {
                var $tr = $(tr);
                var product = {
                    item_name: $tr.find('td').eq(2).text().split(' / ')[0].trim(),
                    item_code: $tr.find('td').eq(2).text().split(' / ')[1].trim(),
                    description: $tr.find('td').eq(3).text().trim(),
                    assembly: $tr.find('td').eq(4).text().trim(),
                    item_h: $tr.find('td').eq(5).text().trim(),
                    item_w: $tr.find('td').eq(6).text().trim(),
                    item_d: $tr.find('td').eq(7).text().trim(),
                    box_h: $tr.find('td').eq(8).text().trim(),
                    box_w: $tr.find('td').eq(9).text().trim(),
                    box_d: $tr.find('td').eq(10).text().trim(),
                    cbm: $tr.find('td').eq(11).text().trim(),
                    wood_type: $tr.find('td').eq(12).text().trim(),
                    no_of_packet: $tr.find('td').eq(13).text().trim(),
                    iron_gauge: $tr.find('td').eq(14).text().trim(),
                    mdf_finish: $tr.find('td').eq(15).text().trim(),
                    quantity: $tr.find('td').eq(16).text().trim(),
                    price_usd: $tr.find('td').eq(17).text().trim(),
                    comments: $tr.find('td').eq(19).text().trim()
                };
                products.push(product);
            });

            formData.append('products', JSON.stringify(products));

            for (var i = 0; i < productImages.length; i++) {
                if (productImages[i]) {
                    formData.append('products[' + i + '][image]', productImages[i]);
                }
            }

            $.ajax({
                url: '/php_erp/modules/quotation/store.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        var res = JSON.parse(response);
                        if (res.success) {
                            $('#liveToast .toast-body').text(res.message);
                            var toast = new bootstrap.Toast(document.getElementById('liveToast'));
                            toast.show();
                            toast._element.addEventListener('hidden.bs.toast', function () {
                                window.location.href = 'index.php';
                            });
                        } else {
                            $('#liveToast .toast-body').text('Error: ' + res.message);
                            var toast = new bootstrap.Toast(document.getElementById('liveToast'));
                            toast.show();
                        }
                    } catch (e) {
                        $('#liveToast .toast-body').text('Unexpected response from server.');
                        var toast = new bootstrap.Toast(document.getElementById('liveToast'));
                        toast.show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#liveToast .toast-body').text('AJAX error: ' + error);
                    var toast = new bootstrap.Toast(document.getElementById('liveToast'));
                    toast.show();
                }
            });
        });

        // Function to update total amount in the footer
        function updateTotalAmount() {
            var total = 0;
            $('#productTable tbody tr').each(function() {
                var text = $(this).find('td').eq(18).text().replace(/,/g, '');
                var rowTotal = parseFloat(text) || 0;
                total += rowTotal;
            });
            $('#totalAmountCell').text(total.toFixed(2));
        }

        // Update total amount on page load
        updateTotalAmount();

        // Update total amount when a product is added
        $('#addProductButton').click(function() {
            setTimeout(updateTotalAmount, 100); // Delay to ensure row is added
        });

        // Update total amount when a product is removed
        $('#productTable').on('click', '.removeProductBtn', function() {
            setTimeout(updateTotalAmount, 100); // Delay to ensure row is removed
        });

    });
</script>