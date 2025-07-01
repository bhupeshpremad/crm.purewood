<?php
include_once '../../config/config.php';
header('Content-Type: application/json');
global $conn;

$response = ['success' => false, 'supplier_items' => []];

if (isset($_GET['po_id'])) {
    $po_id = intval($_GET['po_id']);
    try {
        // Fetch purchase_main id(s) for the given PO id
        $stmt_pm = $conn->prepare("SELECT id FROM purchase_main WHERE po_main_id = ?");
        $stmt_pm->execute([$po_id]);
        $purchase_ids = $stmt_pm->fetchAll(PDO::FETCH_COLUMN);

        $supplier_items = [];

        foreach ($purchase_ids as $purchase_id) {
            // Fetch wood items
            $stmt_wood = $conn->prepare("SELECT woodtype AS item_name, quantity, price, total AS amount FROM purchase_wood WHERE purchase_main_id = ?");
            $stmt_wood->execute([$purchase_id]);
            $wood_items = $stmt_wood->fetchAll(PDO::FETCH_ASSOC);

            // Fetch glow items
            $stmt_glow = $conn->prepare("SELECT glowtype AS item_name, quantity, price, total AS amount FROM purchase_glow WHERE purchase_main_id = ?");
            $stmt_glow->execute([$purchase_id]);
            $glow_items = $stmt_glow->fetchAll(PDO::FETCH_ASSOC);

            // Fetch plynydf items
            $stmt_plynydf = $conn->prepare("SELECT 'Ply/NYDF' AS item_name, quantity, price, total AS amount FROM purchase_plynydf WHERE purchase_main_id = ?");
            $stmt_plynydf->execute([$purchase_id]);
            $plynydf_items = $stmt_plynydf->fetchAll(PDO::FETCH_ASSOC);

            // Fetch hardware items
            $stmt_hardware = $conn->prepare("SELECT itemname AS item_name, quantity, price, totalprice AS amount FROM purchase_hardware WHERE purchase_main_id = ?");
            $stmt_hardware->execute([$purchase_id]);
            $hardware_items = $stmt_hardware->fetchAll(PDO::FETCH_ASSOC);

            $all_items = array_merge($wood_items, $glow_items, $plynydf_items, $hardware_items);

            if (!empty($all_items)) {
                $supplier_items[] = [
                    'purchase_id' => $purchase_id,
                    'items' => $all_items
                ];
            }
        }

        $response['success'] = true;
        $response['supplier_items'] = $supplier_items;
    } catch (Exception $e) {
        $response['message'] = 'Error fetching supplier items from purchase: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'PO ID not provided.';
}

echo json_encode($response);
?>
