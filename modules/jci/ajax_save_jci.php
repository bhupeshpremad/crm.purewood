<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? null;
$edit_mode = ($id !== null && $id !== '');

$po_id = $_POST['po_id'] ?? null;
$jci_number = $_POST['jci_number'] ?? '';
$jci_type = $_POST['jci_type'] ?? '';
$created_by = $_POST['created_by'] ?? '';
$jci_date = $_POST['jci_date'] ?? '';
$sell_order_number = $_POST['sell_order_number'] ?? '';

if (empty($po_id) || !$jci_number || !$jci_type || !$created_by || !$jci_date) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required main fields including PO Number.']);
    exit;
}

if ($jci_type === 'Contracture' && empty($_POST['contracture_name'] ?? [])) {
    echo json_encode(['success' => false, 'message' => 'For Contracture type, at least one contracture detail is required.']);
    exit;
}

try {
    $conn->beginTransaction();

    if ($edit_mode) {
        $stmt = $conn->prepare("UPDATE jci_main SET po_id = ?, jci_number = ?, jci_type = ?, created_by = ?, jci_date = ?, sell_order_number = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$po_id, $jci_number, $jci_type, $created_by, $jci_date, $sell_order_number, $id]);
        $jci_id = $id;

        $stmtDeleteItems = $conn->prepare("DELETE FROM jci_items WHERE jci_id = ?");
        $stmtDeleteItems->execute([$jci_id]);
    } else {
        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM jci_main WHERE jci_number = ?");
        $stmtCheck->execute([$jci_number]);
        if ($stmtCheck->fetchColumn() > 0) {
            throw new Exception('Job Card number already exists. Please try again or use a new number.');
        }

        $stmt = $conn->prepare("INSERT INTO jci_main (po_id, jci_number, jci_type, created_by, jci_date, sell_order_number, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$po_id, $jci_number, $jci_type, $created_by, $jci_date, $sell_order_number]);
        $jci_id = $conn->lastInsertId();
    }

    if ($jci_type === 'Contracture') {
        $contracture_names = $_POST['contracture_name'] ?? [];
        $labour_costs = $_POST['labour_cost'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $totals = $_POST['total'] ?? [];
        $delivery_dates = $_POST['delivery_date'] ?? [];

        $stmtItem = $conn->prepare("INSERT INTO jci_items (jci_id, contracture_name, labour_cost, quantity, total_amount, delivery_date) VALUES (?, ?, ?, ?, ?, ?)");

        for ($i = 0; $i < count($contracture_names); $i++) {
            $contracture_name = trim($contracture_names[$i]);
            $labour_cost = filter_var($labour_costs[$i], FILTER_VALIDATE_FLOAT);
            $quantity = filter_var($quantities[$i], FILTER_VALIDATE_INT);
            $total_amount = filter_var($totals[$i], FILTER_VALIDATE_FLOAT);
            $delivery_date = trim($delivery_dates[$i]);

            if (empty($contracture_name) || $labour_cost === false || $labour_cost < 0 || $quantity === false || $quantity < 0 || $total_amount === false || $total_amount < 0 || empty($delivery_date)) {
                throw new Exception("Invalid contracture data for row " . ($i + 1) . ". Please check all item fields.");
            }

            $stmtItem->execute([$jci_id, $contracture_name, $labour_cost, $quantity, $total_amount, $delivery_date]);
        }
    }

    $conn->commit();

    if (!$edit_mode) {
        $new_jci_number_for_next_add = generateJCINumber($conn);
        echo json_encode(['success' => true, 'message' => 'Job Card saved successfully.', 'new_jci_number' => $new_jci_number_for_next_add]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Job Card updated successfully.']);
    }
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Error saving Job Card: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error saving Job Card: ' . $e->getMessage()]);
    exit;
}

function generateJCINumber($conn) {
    $year = date('Y');
    $prefix = "JCI-$year-";
    $stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING_INDEX(jci_number, '-', -1) AS UNSIGNED)) AS last_seq FROM jci_main WHERE jci_number LIKE ?");
    $stmt->execute(["JCI-$year-%"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_seq = (int)$result['last_seq'];
    $next_seq = $last_seq + 1;
    $seqFormatted = str_pad($next_seq, 4, '0', STR_PAD_LEFT);
    return $prefix . $seqFormatted;
}