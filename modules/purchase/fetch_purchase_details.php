<?php
include_once __DIR__ . '/../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$purchase_id = $_POST['purchase_id'] ?? null;
$section = $_POST['section'] ?? null;

if (!$purchase_id || !$section) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

global $conn;

function fetchWoodDetails($conn, $purchase_id) {
    $stmt = $conn->prepare("SELECT woodtype, length_ft as length, width_ft as width, thickness_inch as thickness, quantity, price, cft, total FROM purchase_wood WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '<table class="table table-bordered">';
    $html .= '<thead><tr><th>Wood Type</th><th>Length (ft)</th><th>Width (ft)</th><th>Thickness (inch)</th><th>Quantity</th><th>Price</th><th>CFT</th><th>Total</th></tr></thead><tbody>';

    foreach ($rows as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['woodtype']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['length']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['width']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['thickness']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['price']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['cft']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

function fetchGlowDetails($conn, $purchase_id) {
    $stmt = $conn->prepare("SELECT glowtype, quantity, price, total FROM purchase_glow WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '<table class="table table-bordered">';
    $html .= '<thead><tr><th>Glow Type</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead><tbody>';

    foreach ($rows as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['glowtype']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['price']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

function fetchPlyNydfDetails($conn, $purchase_id) {
    $stmt = $conn->prepare("SELECT quantity, width, length, price, total FROM purchase_plynydf WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '<table class="table table-bordered">';
    $html .= '<thead><tr><th>Quantity</th><th>Width</th><th>Length</th><th>Price</th><th>Total</th></tr></thead><tbody>';

    foreach ($rows as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['width']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['length']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['price']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

function fetchHardwareDetails($conn, $purchase_id) {
    $stmt = $conn->prepare("SELECT itemname, quantity, price, totalprice FROM purchase_hardware WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '<table class="table table-bordered">';
    $html .= '<thead><tr><th>Item Name</th><th>Quantity</th><th>Price</th><th>Total Price</th></tr></thead><tbody>';

    foreach ($rows as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['itemname']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['price']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['totalprice']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

function calculateTotal($rows, $totalKey) {
    $sum = 0;
    foreach ($rows as $row) {
        $sum += floatval($row[$totalKey]);
    }
    return $sum;
}

function fetchWoodDetailsWithTotal($conn, $purchase_id) {
    $stmt = $conn->prepare("SELECT woodtype, length_ft as length, width_ft as width, thickness_inch as thickness, quantity, price, cft, total FROM purchase_wood WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalSum = calculateTotal($rows, 'total');

    $html = '<h5>Wood</h5>';
    $html .= '<table class="table table-bordered">';
    $html .= '<thead><tr><th>Wood Type</th><th>Length (ft)</th><th>Width (ft)</th><th>Thickness (inch)</th><th>Quantity</th><th>Price</th><th>CFT</th><th>Total</th></tr></thead><tbody>';

    foreach ($rows as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['woodtype']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['length']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['width']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['thickness']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['price']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['cft']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '<tr><td colspan="7" class="text-right font-weight-bold">Total</td><td class="font-weight-bold">' . number_format($totalSum, 2) . '</td></tr>';
    $html .= '</tbody></table>';
    return [$html, $totalSum];
}

function fetchGlueDetailsWithTotal($conn, $purchase_id) {
    $stmt = $conn->prepare("SELECT glowtype, quantity, price, total FROM purchase_glow WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalSum = calculateTotal($rows, 'total');

    $html = '<h5>Glue</h5>';
    $html .= '<table class="table table-bordered">';
    $html .= '<thead><tr><th>Glue Type</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead><tbody>';

    foreach ($rows as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['glowtype']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['price']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '<tr><td colspan="3" class="text-right font-weight-bold">Total</td><td class="font-weight-bold">' . number_format($totalSum, 2) . '</td></tr>';
    $html .= '</tbody></table>';
    return [$html, $totalSum];
}

function fetchPlyNydfDetailsWithTotal($conn, $purchase_id) {
    $stmt = $conn->prepare("SELECT quantity, width, length, price, total FROM purchase_plynydf WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalSum = calculateTotal($rows, 'total');

    $html = '<h5>PLY/NYDF</h5>';
    $html .= '<table class="table table-bordered">';
    $html .= '<thead><tr><th>Quantity</th><th>Width</th><th>Length</th><th>Price</th><th>Total</th></tr></thead><tbody>';

    foreach ($rows as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['width']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['length']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['price']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '<tr><td colspan="4" class="text-right font-weight-bold">Total</td><td class="font-weight-bold">' . number_format($totalSum, 2) . '</td></tr>';
    $html .= '</tbody></table>';
    return [$html, $totalSum];
}

function fetchHardwareDetailsWithTotal($conn, $purchase_id) {
    $stmt = $conn->prepare("SELECT itemname, quantity, price, totalprice FROM purchase_hardware WHERE purchase_main_id = :purchase_id");
    $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalSum = calculateTotal($rows, 'totalprice');

    $html = '<h5>Hardware</h5>';
    $html .= '<table class="table table-bordered">';
    $html .= '<thead><tr><th>Item Name</th><th>Quantity</th><th>Price</th><th>Total Price</th></tr></thead><tbody>';

    foreach ($rows as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['itemname']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['price']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['totalprice']) . '</td>';
        $html .= '</tr>';
    }
    $html .= '<tr><td colspan="3" class="text-right font-weight-bold">Total</td><td class="font-weight-bold">' . number_format($totalSum, 2) . '</td></tr>';
    $html .= '</tbody></table>';
    return [$html, $totalSum];
}

switch ($section) {
    case 'wood':
        echo fetchWoodDetails($conn, $purchase_id);
        break;
    case 'glow':
        echo fetchGlowDetails($conn, $purchase_id);
        break;
    case 'plynydf':
        echo fetchPlyNydfDetails($conn, $purchase_id);
        break;
    case 'hardware':
        echo fetchHardwareDetails($conn, $purchase_id);
        break;
    case 'all':
        list($woodHtml, $woodTotal) = fetchWoodDetailsWithTotal($conn, $purchase_id);
        list($glueHtml, $glueTotal) = fetchGlueDetailsWithTotal($conn, $purchase_id);
        list($plyNydfHtml, $plyNydfTotal) = fetchPlyNydfDetailsWithTotal($conn, $purchase_id);
        list($hardwareHtml, $hardwareTotal) = fetchHardwareDetailsWithTotal($conn, $purchase_id);

        $grandTotal = $woodTotal + $glueTotal + $plyNydfTotal + $hardwareTotal;

        echo $woodHtml;
        echo $glueHtml;
        echo $plyNydfHtml;
        echo $hardwareHtml;

        echo '<h4 class="text-right font-weight-bold">Grand Total: ' . number_format($grandTotal, 2) . '</h4>';
        break;
    default:
        echo "Invalid section";
        break;
}
?>
