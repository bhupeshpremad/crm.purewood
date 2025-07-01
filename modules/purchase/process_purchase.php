<?php
include_once __DIR__ . '/../../config/config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$purchase_id = $data['purchase_id'] ?? null;
$po_main_po_id = $data['po_number'] ?? '';
$sell_order_number = $data['sell_order_number'] ?? '';
$jci_number = $data['jci_number'] ?? '';

$wood = $data['wood'] ?? [];
$glow = $data['glow'] ?? [];
$plynydf = $data['plynydf'] ?? [];
$hardware = $data['hardware'] ?? [];

$section_to_save = $_GET['section'] ?? '';

global $conn;

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection not available']);
    exit;
}

if (empty($po_main_po_id)) {
    echo json_encode(['status' => 'error', 'message' => 'PO Number selection is required.']);
    exit;
}

if (empty($sell_order_number) || empty($jci_number)) {
    echo json_encode(['status' => 'error', 'message' => 'Sell Order Number and JCI Number are required']);
    exit;
}

$conn->beginTransaction();

try {
    $existing = null;
    if ($purchase_id) {
        $stmt = $conn->prepare("SELECT id FROM purchase_main WHERE id = :purchase_id");
        $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conn->prepare("SELECT id FROM purchase_main WHERE po_main_id = :po_main_id");
        $stmt->bindValue(':po_main_id', $po_main_po_id, PDO::PARAM_INT);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($existing) {
        $purchase_id = $existing['id'];
        $stmt = $conn->prepare("UPDATE purchase_main SET po_main_id = :po_main_id, sell_order_number = :sell_order_number, jci_number = :jci_number WHERE id = :purchase_id");
        $stmt->bindValue(':po_main_id', $po_main_po_id, PDO::PARAM_INT);
        $stmt->bindValue(':sell_order_number', $sell_order_number, PDO::PARAM_STR);
        $stmt->bindValue(':jci_number', $jci_number, PDO::PARAM_STR);
        $stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO purchase_main (po_main_id, sell_order_number, jci_number) VALUES (:po_main_id, :sell_order_number, :jci_number)");
        $stmt->bindValue(':po_main_id', $po_main_po_id, PDO::PARAM_INT);
        $stmt->bindValue(':sell_order_number', $sell_order_number, PDO::PARAM_STR);
        $stmt->bindValue(':jci_number', $jci_number, PDO::PARAM_STR);
        $stmt->execute();
        $purchase_id = $conn->lastInsertId();
    }

    if ($section_to_save === 'wood') {
        $del_stmt = $conn->prepare("DELETE FROM purchase_wood WHERE purchase_main_id = :purchase_id");
        $del_stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $del_stmt->execute();

        if (!empty($wood)) {
            $stmt = $conn->prepare("INSERT INTO purchase_wood (purchase_main_id, woodtype, length_ft, width_ft, thickness_inch, quantity, price, cft, total) VALUES (:purchase_main_id, :woodtype, :length_ft, :width_ft, :thickness_inch, :quantity, :price, :cft, :total)");
            foreach ($wood as $item) {
                $width_ft = ($item['width'] ?? 0) / 12;
                $stmt->bindValue(':purchase_main_id', $purchase_id, PDO::PARAM_INT);
                $stmt->bindValue(':woodtype', $item['woodtype'] ?? '', PDO::PARAM_STR);
                $stmt->bindValue(':length_ft', $item['length'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':width_ft', $width_ft, PDO::PARAM_STR);
                $stmt->bindValue(':thickness_inch', $item['thickness'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':quantity', $item['quantity'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':price', $item['price'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':cft', $item['cft'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':total', $item['total'] ?? 0, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    } elseif ($section_to_save === 'glow') {
        $del_stmt = $conn->prepare("DELETE FROM purchase_glow WHERE purchase_main_id = :purchase_id");
        $del_stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $del_stmt->execute();

        if (!empty($glow)) {
            $stmt = $conn->prepare("INSERT INTO purchase_glow (purchase_main_id, glowtype, quantity, price, total) VALUES (:purchase_main_id, :glowtype, :quantity, :price, :total)");
            foreach ($glow as $item) {
                $stmt->bindValue(':purchase_main_id', $purchase_id, PDO::PARAM_INT);
                $stmt->bindValue(':glowtype', $item['glowtype'] ?? '', PDO::PARAM_STR);
                $stmt->bindValue(':quantity', $item['quantity'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':price', $item['price'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':total', $item['total'] ?? 0, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    } elseif ($section_to_save === 'plynydf') {
        $del_stmt = $conn->prepare("DELETE FROM purchase_plynydf WHERE purchase_main_id = :purchase_id");
        $del_stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $del_stmt->execute();

        if (!empty($plynydf)) {
            $stmt = $conn->prepare("INSERT INTO purchase_plynydf (purchase_main_id, quantity, width, length, price, total) VALUES (:purchase_main_id, :quantity, :width, :length, :price, :total)");
            foreach ($plynydf as $item) {
                $stmt->bindValue(':purchase_main_id', $purchase_id, PDO::PARAM_INT);
                $stmt->bindValue(':quantity', $item['quantity'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':width', $item['width'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':length', $item['length'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':price', $item['price'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':total', $item['total'] ?? 0, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    } elseif ($section_to_save === 'hardware') {
        $del_stmt = $conn->prepare("DELETE FROM purchase_hardware WHERE purchase_main_id = :purchase_id");
        $del_stmt->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $del_stmt->execute();

        if (!empty($hardware)) {
            $stmt = $conn->prepare("INSERT INTO purchase_hardware (purchase_main_id, itemname, quantity, price, totalprice) VALUES (:purchase_main_id, :itemname, :quantity, :price, :totalprice)");
            foreach ($hardware as $item) {
                $stmt->bindValue(':purchase_main_id', $purchase_id, PDO::PARAM_INT);
                $stmt->bindValue(':itemname', $item['itemname'] ?? '', PDO::PARAM_STR);
                $stmt->bindValue(':quantity', $item['quantity'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':price', $item['price'] ?? 0, PDO::PARAM_STR);
                $stmt->bindValue(':totalprice', $item['totalprice'] ?? 0, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }

    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Purchase data saved successfully', 'purchase_id' => $purchase_id]);

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Purchase processing error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    echo json_encode(['status' => 'error', 'message' => 'Failed to save purchase data. Please try again.', 'details' => $e->getMessage()]);
}
exit;