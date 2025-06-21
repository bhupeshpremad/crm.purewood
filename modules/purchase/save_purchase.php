<?php
include_once __DIR__ . '/../../config/config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$po_number = $_POST['po_number'] ?? '';
$sell_order_number = $_POST['sell_order_number'] ?? '';
$jci_number = $_POST['jci_number'] ?? '';

$wood = $_POST['wood'] ?? [];
$glow = $_POST['glow'] ?? [];
$plynydf = $_POST['plynydf'] ?? [];
$hardware = $_POST['hardware'] ?? [];

if (empty($po_number) || empty($sell_order_number) || empty($jci_number)) {
    echo json_encode(['error' => 'PO Number, Sell Order Number, and JCI Number are required']);
    exit;
}

$conn = $GLOBALS['conn'] ?? null;
if (!$conn) {
    echo json_encode(['error' => 'Database connection not available']);
    exit;
}

$conn->beginTransaction();

try {
    // Check if purchase_main entry exists by po_number
    $stmt = $conn->prepare("SELECT id FROM purchase_main WHERE po_number = ?");
    $stmt->execute([$po_number]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $purchase_id = $existing['id'];
        // Update existing record
        $stmt = $conn->prepare("UPDATE purchase_main SET sell_order_number = ?, jci_number = ? WHERE id = ?");
        $stmt->execute([$sell_order_number, $jci_number, $purchase_id]);
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO purchase_main (po_number, sell_order_number, jci_number) VALUES (?, ?, ?)");
        $stmt->execute([$po_number, $sell_order_number, $jci_number]);
        $purchase_id = $conn->lastInsertId();
    }

    if (!empty($wood)) {
        $del_stmt = $conn->prepare("DELETE FROM purchase_wood WHERE purchase_main_id = ?");
        $del_stmt->execute([$purchase_id]);

$stmt = $conn->prepare("INSERT INTO purchase_wood (purchase_main_id, woodtype, length_ft, width_ft, thickness_inch, quantity, price, cft, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($wood as $item) {
    $width_ft = $item['width'] / 12; // convert width from inches to feet
    $stmt->execute([
        $purchase_id,
        $item['woodtype'],
        $item['length'],
        $width_ft,
        $item['thickness'],
        $item['quantity'],
        $item['price'],
        $item['cft'],
        $item['total']
    ]);
}
    }
    if (!empty($glow)) {
        $del_stmt = $conn->prepare("DELETE FROM purchase_glow WHERE purchase_main_id = ?");
        $del_stmt->execute([$purchase_id]);

        $stmt = $conn->prepare("INSERT INTO purchase_glow (purchase_main_id, glowtype, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
        foreach ($glow as $item) {
            $stmt->execute([
                $purchase_id,
                $item['glowtype'],
                $item['quantity'],
                $item['price'],
                $item['total']
            ]);
        }
    }
    if (!empty($plynydf)) {
        $del_stmt = $conn->prepare("DELETE FROM purchase_plynydf WHERE purchase_main_id = ?");
        $del_stmt->execute([$purchase_id]);

        $stmt = $conn->prepare("INSERT INTO purchase_plynydf (purchase_main_id, quantity, width, length, price, total) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($plynydf as $item) {
            $stmt->execute([
                $purchase_id,
                $item['quantity'],
                $item['width'],
                $item['length'],
                $item['price'],
                $item['total']
            ]);
        }
    }
    if (!empty($hardware)) {
        $del_stmt = $conn->prepare("DELETE FROM purchase_hardware WHERE purchase_main_id = ?");
        $del_stmt->execute([$purchase_id]);

        $stmt = $conn->prepare("INSERT INTO purchase_hardware (purchase_main_id, itemname, quantity, price, totalprice) VALUES (?, ?, ?, ?, ?)");
        foreach ($hardware as $item) {
            $stmt->execute([
                $purchase_id,
                $item['itemname'],
                $item['quantity'],
                $item['price'],
                $item['totalprice']
            ]);
        }
    }

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Purchase data saved successfully']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['error' => 'Failed to save purchase data', 'details' => $e->getMessage()]);
}
exit;
?>