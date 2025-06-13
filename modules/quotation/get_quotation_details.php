<?php
include_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_GET['quotation_number']) || empty($_GET['quotation_number'])) {
    echo json_encode(['success' => false, 'message' => 'Quotation number is required']);
    exit;
}

$quotation_number = $_GET['quotation_number'];

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Fetch quotation details
    $stmtQuotation = $conn->prepare("SELECT * FROM quotations WHERE quotation_number = :quotation_number");
    $stmtQuotation->execute([':quotation_number' => $quotation_number]);
    $quotation = $stmtQuotation->fetch(PDO::FETCH_ASSOC);

    if (!$quotation) {
        echo '<p>Quotation not found.</p>';
        exit;
    }

    // Fetch quotation products
    $stmtProducts = $conn->prepare("SELECT * FROM quotation_products WHERE quotation_id = :quotation_id");
    $stmtProducts->execute([':quotation_id' => $quotation['id']]);
    $products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

    // Render HTML
    ?>
    <?php
        // Fetch PI number for this quotation
        $stmtPi = $conn->prepare("SELECT pi_number FROM pi WHERE quotation_id = :quotation_id ORDER BY pi_id DESC LIMIT 1");
        $stmtPi->execute([':quotation_id' => $quotation['id']]);
        $pi = $stmtPi->fetch(PDO::FETCH_ASSOC);
        $piNumber = $pi ? $pi['pi_number'] : 'N/A';
    ?>
    <h4 class="mb-4">Performa Invoice Details (PI Number: <?php echo htmlspecialchars($piNumber); ?>)</h4>
    <div class="row">
        <div class="col-md-4"><p><strong>Quotation Number:</strong> <?php echo htmlspecialchars($quotation['quotation_number']); ?></p></div>
        <div class="col-md-4"><p><strong>Date of Quote Raised:</strong> <?php echo htmlspecialchars($quotation['quotation_date']); ?></p></div>
        <div class="col-md-4"><p><strong>Customer Name:</strong> <?php echo htmlspecialchars($quotation['customer_name']); ?></p></div>
    </div>
    <div class="row">
        <div class="col-md-4"><p><strong>Customer Email:</strong> <?php echo htmlspecialchars($quotation['customer_email']); ?></p></div>
        <div class="col-md-4"><p><strong>Customer Phone:</strong> <?php echo htmlspecialchars($quotation['customer_phone']); ?></p></div>
        <div class="col-md-4"><p><strong>Payment Terms:</strong> <?php echo htmlspecialchars($quotation['delivery_term']); ?></p></div>
    </div>
    <div class="row">
        <div class="col-md-4"><p><strong>Terms of Delivery:</strong> <?php echo htmlspecialchars($quotation['terms_of_delivery']); ?></p></div>
    </div>

    <h6>Products:</h6>
    <div class="table-responsive">
    <table class="table table-hover table-bordered table-striped">
        <thead class="bg-gradient-primary text-white">
            <tr>
                <th rowspan="2" scope="col">Sno</th>
                <th rowspan="2" scope="col">Product Image</th>
                <th rowspan="2" scope="col">Item Name</th>
                <th rowspan="2" scope="col">Item Code</th>
                <th rowspan="2" scope="col">Assembly</th>
                <th colspan="3" scope="col" class="text-center">Item Dimension (cms)</th>
                <th colspan="3" scope="col" class="text-center">Box Dimension (cms)</th>
                <th rowspan="2" scope="col">CBM</th>
                <th rowspan="2" scope="col">Wood/Marble Type</th>
                <th rowspan="2" scope="col">No. of Packet</th>
                <th rowspan="2" scope="col">Iron Gauge</th>
                <th rowspan="2" scope="col">MDF Finish</th>
                <th rowspan="2" scope="col">MOQ</th>
                <th rowspan="2" scope="col">Price USD</th>
                <th rowspan="2" scope="col">Total USD</th>
                <th rowspan="2" scope="col">Comments</th>
                <th rowspan="2" scope="col">Action</th>
            </tr>
            <tr>
                <th scope="col">H</th>
                <th scope="col">W</th>
                <th scope="col">D</th>
                <th scope="col">H</th>
                <th scope="col">W</th>
                <th scope="col">D</th>
            </tr>
        </thead>
        <tbody>
            <?php $sno = 1; ?>
            <?php $totalPrice = 0; ?>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo $sno++; ?></td>
                <td>
                    <?php if (!empty($product['product_image_name'])): ?>
                        <img src="/php_erp/purewood/assets/images/upload/quotation/<?php echo htmlspecialchars($product['product_image_name']); ?>" alt="Product Image" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                    <?php else: ?>
                        N/A
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
                <td><?php echo htmlspecialchars($product['moq'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($product['price_usd']); ?></td>
                <td><?php echo htmlspecialchars($product['total_price_usd']); ?></td>
                <td><?php echo htmlspecialchars($product['comments']); ?></td>
                <td>No</td>
            </tr>
            <?php $totalPrice += $product['total_price_usd']; ?>
            <?php endforeach; ?>
            <tr>
                <td colspan="16" class="text-right font-weight-bold">Total Amount</td>
                <td class="font-weight-bold"><?php echo number_format($totalPrice, 2); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    </div>
    <?php

} catch (PDOException $e) {
    echo '<p>Error fetching quotation details: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
