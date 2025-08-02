<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (ob_get_length()) ob_end_clean();

require __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . '/../../config/config.php';

global $conn;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit('Invalid quotation ID');
}

$quotationId = intval($_GET['id']);

try {
    if (!$conn) throw new Exception('Database connection not initialized.');

    $stmt = $conn->prepare("SELECT * FROM quotations WHERE id = ?");
    $stmt->execute([$quotationId]);
    $quotation = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$quotation) exit('Quotation not found');

    $stmt2 = $conn->prepare("SELECT * FROM quotation_products WHERE quotation_id = ?");
    $stmt2->execute([$quotationId]);
    $products = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $mpdf = new \Mpdf\Mpdf([
        'orientation' => 'L',
        'tempDir' => __DIR__ . '/../../tmp',
        'allow_output_buffering' => true,
    ]);

    $siteUrl = 'https://crm.purewood.in';
    $baseImageWebPath = $siteUrl . '/assets/images/upload/quotation/';
    $logoWebUrl = $siteUrl . '/assets/images/Purewood-Joey Logo.png';

    $html = '<html><head><style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            word-wrap: break-word;
        }
        th {
            background-color: #4B612C;
            color: #fff;
            font-size: 10px;
            text-align: center;
            vertical-align: middle;
        }
        td {
            vertical-align: middle;
            text-align: center;
        }
        img.product-img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        .logo {
            height: 80px;
            max-width: 300px;
            object-fit: contain;
        }
        .seller-details-cell,
        .buyer-details-cell,
        .quote-info-cell {
            padding: 8px;
            border: 1px solid #000;
            text-align: left;
            vertical-align: top;
        }
        .header-section-table td {
            vertical-align: top;
            padding: 8px;
        }
        .header-section-table th {
            text-align: left;
            vertical-align: middle;
        }
    </style></head><body>';

    $html .= '<img src="' . htmlspecialchars($logoWebUrl) . '" class="logo" /><br><br>';

    $html .= '<table class="header-section-table" style="margin-bottom:20px; width:100%;">
        <tr>
            <th colspan="2" style="text-align:left;">Seller Details</th>
            <th colspan="2" style="text-align:left;">Buyer Details</th>
            <th colspan="2" style="text-align:left;">Quotation Info</th>
        </tr>
        <tr>
            <td colspan="2" class="seller-details-cell">
                <strong>Purewood</strong><br>
                G178 Special Economy Area (SEZ)<br>
                Export Promotion Industrial Park (EPIP)<br>
                Boranada, Jodhpur, Rajasthan<br>
                India (342001)<br>
                GST: 08AAQFP4054K1ZQ
            </td>
            <td colspan="2" class="buyer-details-cell">
                <strong>' . htmlspecialchars($quotation['customer_name'] ?? 'N/A') . '</strong><br>' .
                htmlspecialchars($quotation['customer_email'] ?? 'N/A') . '<br>' .
                htmlspecialchars($quotation['customer_phone'] ?? 'N/A') . '
            </td>
            <td class="quote-info-cell"><strong>Quote Number:</strong><br>' . htmlspecialchars($quotation['quotation_number'] ?? 'N/A') . '</td>
            <td class="quote-info-cell"><strong>Date:</strong><br>' . htmlspecialchars($quotation['quotation_date'] ?? 'N/A') . '</td>
        </tr>
        <tr>
            <td colspan="3" class="quote-info-cell"><strong>Payment Terms:</strong> ' . htmlspecialchars($quotation['delivery_term'] ?? '30% advance 70% on DOCS') . '</td>
            <td colspan="3" class="quote-info-cell"><strong>Time of Delivery:</strong> ' . htmlspecialchars($quotation['terms_of_delivery'] ?? 'FOB') . '</td>
        </tr>
    </table>';

    $html .= '<table style="margin-bottom:20px;">
        <thead>
            <tr>
                <th style="width: 3%;">Sno</th>
                <th style="width: 10%;">Image</th>
                <th style="width: 10%;">Item Name</th>
                <th style="width: 8%;">Item Code</th>
                <th style="width: 8%;">Assembly</th>
                <th style="width: 10%;">Item Dimension (W x D x H) in CMS</th>
                <th style="width: 10%;">Box Dimension (W x D x H) in CMS</th>
                <th style="width: 5%;">CBM</th>
                <th style="width: 6%;">Total CBM</th>
                <th style="width: 8%;">Material</th>
                <th style="width: 5%;">No of Packet</th>
                <th style="width: 5%;">Quantity</th>
                <th style="width: 6%;">Price USD</th>
                <th style="width: 6%;">Total USD</th>
                <th style="width: 6%;">Comments</th>
            </tr>
        </thead>
        <tbody>';

    $serial = 1;
    $totalQty = 0;
    $totalUsd = 0;
    $totalCbm = 0;

    foreach ($products as $product) {
        $html .= '<tr>';
        $html .= '<td>' . $serial . '</td>';

        $imageHtml = '<div style="color:gray; font-style:italic;">No Image</div>';
        if (!empty($product['product_image_name'])) {
            $imageUrl = $baseImageWebPath . $product['product_image_name'];
            $imageLocalPath = ROOT_DIR_PATH . 'assets/images/upload/quotation/' . $product['product_image_name'];
            $imageHtml = file_exists($imageLocalPath)
                ? '<img src="' . htmlspecialchars($imageLocalPath) . '" class="product-img" />'
                : '<img src="' . htmlspecialchars($imageUrl) . '" class="product-img" />';
        }
        $html .= '<td>' . $imageHtml . '</td>';

        $html .= '<td>' . htmlspecialchars($product['item_name'] ?? 'N/A') . '</td>';
        $html .= '<td>' . htmlspecialchars($product['item_code'] ?? 'N/A') . '</td>';
        $html .= '<td>' . htmlspecialchars($product['assembly'] ?? 'N/A') . '</td>';
        $html .= '<td>' . htmlspecialchars($product['item_w'] ?? 'N/A') . ' x ' . htmlspecialchars($product['item_d'] ?? 'N/A') . ' x ' . htmlspecialchars($product['item_h'] ?? 'N/A') . '</td>';
        $html .= '<td>' . htmlspecialchars($product['box_w'] ?? 'N/A') . ' x ' . htmlspecialchars($product['box_d'] ?? 'N/A') . ' x ' . htmlspecialchars($product['box_h'] ?? 'N/A') . '</td>';
        $html .= '<td>' . htmlspecialchars($product['cbm'] ?? 'N/A') . '</td>';

        $cbm = floatval($product['cbm'] ?? 0);
        $qty = intval($product['quantity'] ?? 0);
        $totalCbmRow = $cbm * $qty;
        $html .= '<td>' . number_format($totalCbmRow, 2) . '</td>';

        $html .= '<td>' . htmlspecialchars($product['wood_type'] ?? 'N/A') . '</td>';
        $html .= '<td>' . htmlspecialchars($product['no_of_packet'] ?? 'N/A') . '</td>';
        $html .= '<td>' . $qty . '</td>';

        $priceUsd = floatval($product['price_usd'] ?? 0);
        $totalUsdRow = $qty * $priceUsd;
        $html .= '<td>' . number_format($priceUsd, 2) . '</td>';
        $html .= '<td>' . number_format($totalUsdRow, 2) . '</td>';
        $html .= '<td>' . htmlspecialchars($product['comments'] ?? 'N/A') . '</td>';
        $html .= '</tr>';

        $totalQty += $qty;
        $totalUsd += $totalUsdRow;
        $totalCbm += $totalCbmRow;
        $serial++;
    }

    $html .= '</tbody>
    <tfoot>
        <tr style="font-weight:bold; background-color:#f0f0f0;">
            <td colspan="8" style="text-align:right;">Total CBM:</td>
            <td>' . number_format($totalCbm, 2) . '</td>
            
            <td colspan="2" style="text-align:right;">Total Quantity:</td>
            <td>' . $totalQty . '</td>
            
            <td style="text-align:right;">Total USD:</td>
            <td>' . number_format($totalUsd, 2) . '</td>
            
            <td></td>
        </tr>
    </tfoot>
    </table>';

    $html .= '</body></html>';

    $mpdf->WriteHTML($html);

    if (ob_get_length()) ob_end_clean();
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="quotation_' . ($quotation['quotation_number'] ?? $quotationId) . '.pdf"');
    $mpdf->Output();
    exit;

} catch (Exception $e) {
    file_put_contents(__DIR__ . '/export_quotation_pdf_error.log', date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . "\n", FILE_APPEND);
    exit('Error generating PDF file. Please check the error log for details.');
}