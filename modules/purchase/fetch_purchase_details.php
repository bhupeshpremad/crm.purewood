<?php
include_once __DIR__ . '/../../config/config.php';

global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_id'])) {
    $purchase_id = filter_var($_POST['purchase_id'], FILTER_SANITIZE_NUMBER_INT);

    if (!$purchase_id) {
        echo '<div class="alert alert-danger">Invalid Purchase ID.</div>';
        exit;
    }

    $output = '';

    try {
        // Fetch main purchase data
        $stmt_main = $conn->prepare("SELECT p.id, pm_tbl.po_number, p.jci_number, p.sell_order_number 
                                     FROM purchase_main p 
                                     JOIN po_main pm_tbl ON p.po_main_id = pm_tbl.id 
                                     WHERE p.id = :purchase_id");
        $stmt_main->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $stmt_main->execute();
        $main_data = $stmt_main->fetch(PDO::FETCH_ASSOC);

        if (!$main_data) {
            echo '<div class="alert alert-warning">Purchase record not found.</div>';
            exit;
        }

        // --- UPDATED: Main Purchase Details in a Table ---
        $output .= '<h5>Main Purchase Details</h5>';
        $output .= '<table class="table table-sm table-bordered">';
        $output .= '<thead><tr><th>Purchase ID</th><th>PO Number</th><th>JCI Number</th><th>Sell Order Number</th></tr></thead>';
        $output .= '<tbody>';
        $output .= '<tr>';
        $output .= '<td>' . htmlspecialchars($main_data['id']) . '</td>';
        $output .= '<td>' . htmlspecialchars($main_data['po_number']) . '</td>';
        $output .= '<td>' . htmlspecialchars($main_data['jci_number']) . '</td>';
        $output .= '<td>' . htmlspecialchars($main_data['sell_order_number']) . '</td>';
        $output .= '</tr>';
        $output .= '</tbody></table>';
        $output .= '<hr>';


        // --- Fetch Wood Details ---
        $stmt_wood = $conn->prepare("SELECT * FROM purchase_wood WHERE purchase_main_id = :purchase_id");
        $stmt_wood->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $stmt_wood->execute();
        $wood_data = $stmt_wood->fetchAll(PDO::FETCH_ASSOC);

        $output .= '<h5>Wood Details</h5>';
        if ($wood_data) {
            $output .= '<table class="table table-sm table-bordered">';
            $output .= '<thead><tr><th>Type</th><th>Length (ft)</th><th>Width (inch)</th><th>Thickness (inch)</th><th>Qty</th><th>Price</th><th>CFT</th><th>Total</th></tr></thead>';
            $output .= '<tbody>';
            foreach ($wood_data as $row) {
                $output .= '<tr>';
                $output .= '<td>' . htmlspecialchars($row['woodtype']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['length_ft']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['width_ft'] * 12) . '</td>'; // Convert back to inch for display
                $output .= '<td>' . htmlspecialchars($row['thickness_inch']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['price']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['cft']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['total']) . '</td>';
                $output .= '</tr>';
            }
            $output .= '</tbody></table>';
        } else {
            $output .= '<p>No wood items found for this purchase.</p>';
        }
        $output .= '<hr>';

        // --- Fetch Glow Details ---
        $stmt_glow = $conn->prepare("SELECT * FROM purchase_glow WHERE purchase_main_id = :purchase_id");
        $stmt_glow->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $stmt_glow->execute();
        $glow_data = $stmt_glow->fetchAll(PDO::FETCH_ASSOC);

        $output .= '<h5>Glow Details</h5>';
        if ($glow_data) {
            $output .= '<table class="table table-sm table-bordered">';
            $output .= '<thead><tr><th>Type</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>';
            $output .= '<tbody>';
            foreach ($glow_data as $row) {
                $output .= '<tr>';
                $output .= '<td>' . htmlspecialchars($row['glowtype']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['price']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['total']) . '</td>';
                $output .= '</tr>';
            }
            $output .= '</tbody></table>';
        } else {
            $output .= '<p>No glow items found for this purchase.</p>';
        }
        $output .= '<hr>';

        // --- Fetch Plynydf Details ---
        $stmt_plynydf = $conn->prepare("SELECT * FROM purchase_plynydf WHERE purchase_main_id = :purchase_id");
        $stmt_plynydf->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $stmt_plynydf->execute();
        $plynydf_data = $stmt_plynydf->fetchAll(PDO::FETCH_ASSOC);

        $output .= '<h5>Ply/NYDF Details</h5>';
        if ($plynydf_data) {
            $output .= '<table class="table table-sm table-bordered">';
            $output .= '<thead><tr><th>Qty</th><th>Width</th><th>Length</th><th>Price</th><th>Total</th></tr></thead>';
            $output .= '<tbody>';
            foreach ($plynydf_data as $row) {
                $output .= '<tr>';
                $output .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['width']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['length']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['price']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['total']) . '</td>';
                $output .= '</tr>';
            }
            $output .= '</tbody></table>';
        } else {
            $output .= '<p>No ply/nydf items found for this purchase.</p>';
        }
        $output .= '<hr>';

        // --- Fetch Hardware Details ---
        $stmt_hardware = $conn->prepare("SELECT * FROM purchase_hardware WHERE purchase_main_id = :purchase_id");
        $stmt_hardware->bindValue(':purchase_id', $purchase_id, PDO::PARAM_INT);
        $stmt_hardware->execute();
        $hardware_data = $stmt_hardware->fetchAll(PDO::FETCH_ASSOC);

        $output .= '<h5>Hardware Details</h5>';
        if ($hardware_data) {
            $output .= '<table class="table table-sm table-bordered">';
            $output .= '<thead><tr><th>Item Name</th><th>Qty</th><th>Price</th><th>Total Price</th></tr></thead>';
            $output .= '<tbody>';
            foreach ($hardware_data as $row) {
                $output .= '<tr>';
                $output .= '<td>' . htmlspecialchars($row['itemname']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['quantity']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['price']) . '</td>';
                $output .= '<td>' . htmlspecialchars($row['totalprice']) . '</td>';
                $output .= '</tr>';
            }
            $output .= '</tbody></table>';
        } else {
            $output .= '<p>No hardware items found for this purchase.</p>';
        }

        echo $output;

    } catch (PDOException $e) {
        error_log("Error fetching purchase details: " . $e->getMessage());
        echo '<div class="alert alert-danger">Database error: Could not fetch details.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request.</div>';
}
?>