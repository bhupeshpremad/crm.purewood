<?php
include_once __DIR__ . '/../../config/config.php';
if (!defined('ROOT_DIR_PATH')) {
    define('ROOT_DIR_PATH', __DIR__ . '/../../' . DIRECTORY_SEPARATOR);
}
session_start();
# require_once ROOT_DIR_PATH . 'include/inc/db_connect.php'; // Removed because config.php already sets up $conn

header('Content-Type: application/json');

global $conn;


try {
    $conn->beginTransaction();

    // --- 1. Get Main JCI Details from POST ---
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); // JCI Main ID if in edit mode
    $po_id = filter_input(INPUT_POST, 'po_id', FILTER_VALIDATE_INT);
    $sell_order_number = filter_input(INPUT_POST, 'sell_order_number', FILTER_SANITIZE_STRING);
    $base_jci_number = filter_input(INPUT_POST, 'base_jci_number', FILTER_SANITIZE_STRING);
    $created_by = filter_input(INPUT_POST, 'created_by', FILTER_SANITIZE_STRING);
    $jci_date = filter_input(INPUT_POST, 'jci_date', FILTER_SANITIZE_STRING); // Main JCI Date
    # $jci_type = filter_input(INPUT_POST, 'jci_type', FILTER_SANITIZE_STRING); // Main JCI Type

    // Validate main required fields
    if (!$po_id || !$base_jci_number || !$created_by || !$jci_date) {
        throw new Exception("Missing required main JCI details (PO ID, Base JCI Number, Created By, Job Card Date).");
    }

    $jci_main_id = $id; // Initialize jci_main_id

    // --- 2. Insert or Update jci_main ---
    if ($jci_main_id) {
        // UPDATE existing JCI main record
        $stmt = $conn->prepare("UPDATE jci_main SET
            po_id = ?,
            sell_order_number = ?,
            created_by = ?,
            jci_date = ?
            WHERE id = ?"
        );
        $stmt->execute([$po_id, $sell_order_number, $created_by, $jci_date, $jci_main_id]);
    } else {
        // INSERT new JCI main record
        $stmt = $conn->prepare("INSERT INTO jci_main (
            po_id,
            sell_order_number,
            jci_number, -- This will be the initial base number, updated later with suffix
            created_by,
            jci_date
        ) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$po_id, $sell_order_number, $base_jci_number, $created_by, $jci_date]);
        $jci_main_id = $conn->lastInsertId();

        if (!$jci_main_id) {
            throw new Exception("Failed to insert main JCI record.");
        }
    }

    // --- 3. Process JCI Items ---
    $submitted_item_ids = [];
    $jci_item_ids = $_POST['jci_item_id'] ?? []; // Array of existing item IDs (can be empty for new rows)
    $po_product_ids = $_POST['po_product_id'] ?? [];
    $product_names = $_POST['product_name'] ?? [];
    $item_codes = $_POST['item_code'] ?? [];
    $original_po_quantities = $_POST['original_po_quantity'] ?? [];
    $assigned_quantities = $_POST['assign_quantity'] ?? []; // 'assign_quantity' from form
    $labour_costs = $_POST['labour_cost'] ?? [];
    $totals = $_POST['total'] ?? [];
    $delivery_dates = $_POST['delivery_date'] ?? [];
    $item_job_card_dates = $_POST['job_card_date'] ?? [];
        # $item_job_card_types = $_POST['job_card_type'] ?? []; // Removed job_card_type as requested
    $contracture_names = $_POST['contracture_name'] ?? [];

    $num_items = count($po_product_ids); // Use one of the arrays to count items

    for ($i = 0; $i < $num_items; $i++) {
        $item_id = filter_var($jci_item_ids[$i], FILTER_VALIDATE_INT);
        $po_prod_id = filter_var($po_product_ids[$i], FILTER_VALIDATE_INT);
        $prod_name = filter_var($product_names[$i], FILTER_SANITIZE_STRING);
        $it_code = filter_var($item_codes[$i], FILTER_SANITIZE_STRING);
        $original_qty = filter_var($original_po_quantities[$i], FILTER_VALIDATE_FLOAT);
        $assign_qty = filter_var($assigned_quantities[$i], FILTER_VALIDATE_FLOAT);
        $lab_cost = filter_var($labour_costs[$i], FILTER_VALIDATE_FLOAT);
        $item_total = filter_var($totals[$i], FILTER_VALIDATE_FLOAT);
        $del_date = filter_var($delivery_dates[$i], FILTER_SANITIZE_STRING);
        $item_jci_date = filter_var($item_job_card_dates[$i], FILTER_SANITIZE_STRING);
        $item_jci_type = filter_var($item_job_card_types[$i], FILTER_SANITIZE_STRING);
        $contract_name = filter_var($contracture_names[$i], FILTER_SANITIZE_STRING);

        // Ensure mandatory item fields are present
        if (!$po_prod_id || !$prod_name || $assign_qty === false || $lab_cost === false || !$del_date || !$item_jci_date) {
            throw new Exception("Missing or invalid details for item " . ($i + 1) . ".");
        }

        // Handle contracture_name conditional requirement
        if ($item_jci_type === 'Contracture' && empty($contract_name)) {
            throw new Exception("Contracture Name is required for 'Contracture' type on item " . ($i + 1) . ".");
        }
        if ($item_jci_type !== 'Contracture') {
            $contract_name = NULL; // Clear contracture_name if type is not Contracture
        }

        if ($item_id) {
            // Update existing item
            $stmt_item = $conn->prepare("UPDATE jci_items SET
                po_product_id = ?,
                product_name = ?,
                item_code = ?,
                original_po_quantity = ?,
                quantity = ?, -- This is the 'assigned_quantity'
                labour_cost = ?,
                total_amount = ?,
                delivery_date = ?,
                job_card_date = ?,
                contracture_name = ?
                WHERE id = ? AND jci_id = ?"
            );
            $stmt_item->execute([
                $po_prod_id,
                $prod_name,
                $it_code,
                $original_qty,
                $assign_qty,
                $lab_cost,
                $item_total,
                $del_date,
                $item_jci_date,
                $contract_name,
                $item_id,
                $jci_main_id
            ]);
            $submitted_item_ids[] = $item_id; // Add to list of submitted IDs
        } else {
            // Insert new item
            $stmt_item = $conn->prepare("INSERT INTO jci_items (
                jci_id,
                po_product_id,
                product_name,
                item_code,
                original_po_quantity,
                quantity, -- This is the 'assigned_quantity'
                labour_cost,
                total_amount,
                delivery_date,
                job_card_date,
                contracture_name
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_item->execute([
                $jci_main_id,
                $po_prod_id,
                $prod_name,
                $it_code,
                $original_qty,
                $assign_qty,
                $lab_cost,
                $item_total,
                $del_date,
                $item_jci_date,
                $contract_name
            ]);
            // If it's a new item, we don't need its ID for deletion logic,
            // as it won't be in the DB before this transaction.
        }
    }

    // --- 4. Delete Removed Items (Only if in edit mode) ---
    if ($id) { // Only run this if we are editing an existing JCI (has an $id)
        if (!empty($submitted_item_ids)) {
            // Convert array of submitted IDs to a comma-separated string for IN clause
            $placeholders = implode(',', array_fill(0, count($submitted_item_ids), '?'));
            $stmt_delete = $conn->prepare("DELETE FROM jci_items WHERE jci_id = ? AND id NOT IN ($placeholders)");
            $stmt_delete->execute(array_merge([$jci_main_id], $submitted_item_ids));
        } else {
            // If no items were submitted (all removed), delete all items for this JCI
            $stmt_delete_all = $conn->prepare("DELETE FROM jci_items WHERE jci_id = ?");
            $stmt_delete_all->execute([$jci_main_id]);
        }
    }

    // --- 5. Generate and Update Final JCI Number (if not in edit mode or if base number changed) ---
    // This logic ensures the JCI number gets its suffix (e.g., -A, -B)
    $final_jci_number = $base_jci_number; // Start with the base number

    // Check if the JCI number needs a suffix (e.g., if multiple job cards are created for the same PO)
    $stmt_count_jci = $conn->prepare("SELECT COUNT(*) FROM jci_main WHERE po_id = ? AND id <= ?");
    $stmt_count_jci->execute([$po_id, $jci_main_id]);
    $existing_count_for_po = $stmt_count_jci->fetchColumn();

    if ($existing_count_for_po > 1) {
        $suffix = chr(64 + $existing_count_for_po); // A, B, C...
        $final_jci_number = "JOB-" . date('Y', strtotime($jci_date)) . "-" . substr($base_jci_number, -4) . "-" . $suffix;
    } else {
        // If it's the first JCI for this PO, or if it's an update, ensure it's 'JOB-YEAR-00XX' without suffix
        $final_jci_number = "JOB-" . date('Y', strtotime($jci_date)) . "-" . substr($base_jci_number, -4);
    }
    
    // Update jci_main with the final JCI number
    $stmt_update_jci_num = $conn->prepare("UPDATE jci_main SET jci_number = ? WHERE id = ?");
    $stmt_update_jci_num->execute([$final_jci_number, $jci_main_id]);


    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => "Job Card saved successfully!",
        'jci_id' => $jci_main_id,
        'new_base_jci_number' => generateBaseJCINumber($conn) // For next new entry
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'message' => "Application error: " . $e->getMessage()
    ]);
} finally {
    if ($conn) {
        $conn = null;
    }
}

// Function to generate the next base JCI number (like JCI-2025-0001)
// This function needs to be available within this script as well for the success response.
function generateBaseJCINumber($conn) {
    $year = date('Y');
    $prefix = "JCI-$year-";
    // Modified query to correctly find the max sequence from both JCI- and JOB- formats
    $stmt = $conn->prepare("SELECT MAX(
        CASE
            WHEN jci_number LIKE 'JOB-{$year}-%-%' THEN CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(jci_number, '-', 3), '-', -1) AS UNSIGNED)
            WHEN jci_number LIKE 'JCI-{$year}-%' THEN CAST(SUBSTRING_INDEX(jci_number, '-', -1) AS UNSIGNED)
            ELSE 0
        END
    ) AS last_seq FROM jci_main
    WHERE jci_number LIKE 'JOB-{$year}-%' OR jci_number LIKE 'JCI-{$year}-%';");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_seq = (int)$result['last_seq'];
    $next_seq = $last_seq + 1;
    $seqFormatted = str_pad($next_seq, 4, '0', STR_PAD_LEFT);
    return $prefix . $seqFormatted;
}

?>