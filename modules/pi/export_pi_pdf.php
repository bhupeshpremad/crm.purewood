<?php
include_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use \Mpdf\Mpdf;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit('Invalid PI ID');
}

$piId = intval($_GET['id']);

try {
    // $database = new Database();
    // $conn = $database->getConnection();

    global $conn;


    // Fetch PI data
    $stmt = $conn->prepare("SELECT * FROM pi WHERE pi_id = ?");
    $stmt->execute([$piId]);
    $pi = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pi) {
        exit('PI not found');
    }

    // Fetch products for this PI's quotation
    $stmt2 = $conn->prepare("SELECT * FROM quotation_products WHERE quotation_id = (SELECT id FROM quotations WHERE quotation_number = ?)");
    $stmt2->execute([$pi['quotation_number']]);
    $products = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $mpdf = new Mpdf();

    $html = '<h1 style="text-align:center;">PUREWOOD</h1>';

    $html .= '<h3>Seller Details</h3>';
    $html .= '<p>Purewood<br>
        G178 Special Economy Area (SEZ)<br>
        Export Promotion Industrial Park (EPIP)<br>
        Boranada, Jodhpur, Rajasthan<br>
        India (342001) GST: 08AAQFP4054K1ZQ</p>';

    $html .= '<h3>Buyer Details</h3>';
    // Fetch buyer details from quotations table
    $stmtBuyer = $conn->prepare("SELECT customer_name, customer_email, customer_phone FROM quotations WHERE quotation_number = ?");
    $stmtBuyer->execute([$pi['quotation_number']]);
    $buyer = $stmtBuyer->fetch(PDO::FETCH_ASSOC);

    $html .= '<p>' . htmlspecialchars($buyer['customer_name'] ?? '') . '<br>' .
        htmlspecialchars($buyer['customer_email'] ?? '') . '<br>' .
        htmlspecialchars($buyer['customer_phone'] ?? '') . '</p>';

    $html .= '<p><strong>Payment Terms:</strong> ' . htmlspecialchars($pi['delivery_term'] ?? '60 Days') . '<br>';
    $html .= '<strong>Terms of Delivery:</strong> ' . htmlspecialchars($pi['terms_of_delivery'] ?? 'FOB') . '<br>';
    $html .= '<strong>Payment Term:</strong> ' . htmlspecialchars($pi['payment_term'] ?? '30% advance 70% on DOCS') . '</p>';

    $html .= '<p><strong>PI Number:</strong> ' . htmlspecialchars($pi['pi_number'] ?? '') . '<br>';
    $html .= '<strong>Date of PI Raised:</strong> ' . htmlspecialchars($pi['date_of_pi_raised'] ?? '') . '</p>';

    // Table header
    $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
    $html .= '<thead style="background-color:#4B612C; color:#FFFFFF; text-align:center;">
        <tr>
            <th>Sno</th>
            <th>Image</th>
            <th>Item Name/ Code</th>
            <th>Description</th>
            <th>Assembly</th>
            <th>Item Dimension (H x W x D)</th>
            <th>Box Dimension (H x W x D)</th>
            <th>CBM</th>
            <th>Wood/ Marble Type</th>
            <th>No of Packet</th>
            <th>Iron Gauge</th>
            <th>MDF Finish</th>
            <th>MOQ</th>
            <th>Price USD</th>
            <th>Total</th>
            <th>Comments</th>
        </tr>
    </thead><tbody>';

    $serial = 1;
    $baseImagePath = realpath(__DIR__ . '/../../assets/images/upload/quotation/');

    $totalAmount = 0;
    foreach ($products as $product) {
        $html .= '<tr style="text-align:center;">';
        $html .= '<td>' . $serial . '</td>';

        // Image
        $imagePath = $baseImagePath . DIRECTORY_SEPARATOR . ($product['product_image_name'] ?? '');
        if (!empty($product['product_image_name']) && file_exists($imagePath)) {
            $imgData = base64_encode(file_get_contents($imagePath));
            $src = 'data:image/jpeg;base64,' . $imgData;
            $html .= '<td><img src="' . $src . '" style="height:50px; width:40px;" /></td>';
        } else {
            $html .= '<td></td>';
        }

        $html .= '<td>' . htmlspecialchars($product['item_name'] . "\n" . $product['item_code']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['description']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['assembly']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['item_h']) . ' x ' . htmlspecialchars($product['item_w']) . ' x ' . htmlspecialchars($product['item_d']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['box_h']) . ' x ' . htmlspecialchars($product['box_w']) . ' x ' . htmlspecialchars($product['box_d']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['cbm']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['wood_type']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['no_of_packet']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['iron_gauge']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['mdf_finish']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['moq']) . '</td>';
        $html .= '<td>$' . htmlspecialchars($product['price_usd']) . '</td>';
        $html .= '<td>$' . htmlspecialchars($product['total']) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['comments']) . '</td>';
        $html .= '</tr>';
        $totalAmount += $product['total'];
        $serial++;
    }

    $html .= '</tbody>';

    // Add total row
    $html .= '<tfoot><tr style="background-color:#4B612C; color:#FFFFFF; font-weight:bold; text-align:right;">';
    $html .= '<td colspan="13" style="text-align:center;">Total Amount</td>';
    $html .= '<td></td>'; // Price USD column empty
    $html .= '<td>$' . number_format($totalAmount, 2) . '</td>';
    $html .= '<td></td>'; // Comments column empty
    $html .= '</tr></tfoot>';

    $html .= '</table>';

    // Write HTML to PDF
    $mpdf->WriteHTML($html);

    // Output PDF to browser for download
    $filename = 'pi_' . ($pi['pi_number'] ?? $piId) . '.pdf';
    $mpdf->Output($filename, 'D');
    exit;

} catch (Exception $e) {
    file_put_contents(__DIR__ . '/export_pi_pdf_error.log', $e->getMessage());
    exit('Error generating PDF file.');
}
?>
