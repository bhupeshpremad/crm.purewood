<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quotation_id = isset($_POST['quotation_id']) ? intval($_POST['quotation_id']) : 0;
    $new_status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $status_date = isset($_POST['status_date']) ? trim($_POST['status_date']) : '';
    $approve = isset($_POST['approve']) ? intval($_POST['approve']) : null;

    if ($quotation_id <= 0 || (empty($new_status) && $approve === null)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    try {
        $database = new Database();
        $conn = $database->getConnection();

        if ($approve !== null) {
            // Update approve column in quotations table
            $stmt = $conn->prepare("UPDATE quotations SET approve = :approve WHERE id = :quotation_id");
            $stmt->execute([':approve' => $approve, ':quotation_id' => $quotation_id]);

            if ($approve == 1) {
                // Generate PI number with format PI-YEAR-00001, reset annually
                $year = date('Y');
                $piPrefix = "PI-$year-";

                // Get last PI number for current year
                $stmtPi = $conn->prepare("SELECT pi_number FROM pi WHERE pi_number LIKE :prefix ORDER BY pi_id DESC LIMIT 1");
                $stmtPi->execute([':prefix' => $piPrefix . '%']);
                $lastPi = $stmtPi->fetch(PDO::FETCH_ASSOC);

                if ($lastPi) {
                    $lastNumber = intval(substr($lastPi['pi_number'], strlen($piPrefix)));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $newPiNumber = $piPrefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

                // Fetch quotation details
                $stmtQuotation = $conn->prepare("SELECT quotation_number, customer_name, customer_email, customer_phone, delivery_term, terms_of_delivery FROM quotations WHERE id = :quotation_id");
                $stmtQuotation->execute([':quotation_id' => $quotation_id]);
                $quotation = $stmtQuotation->fetch(PDO::FETCH_ASSOC);

                // Insert into pi table
                $stmtInsertPi = $conn->prepare("INSERT INTO pi (quotation_id, quotation_number, pi_number, status, date_of_pi_raised) VALUES (:quotation_id, :quotation_number, :pi_number, :status, CURDATE())");
                $stmtInsertPi->execute([
                    ':quotation_id' => $quotation_id,
                    ':quotation_number' => $quotation['quotation_number'],
                    ':pi_number' => $newPiNumber,
                    ':status' => 'Generated'
                ]);
            }
        }

        if (!empty($new_status)) {
            // Update quotation status in quotations table
            // $stmt = $conn->prepare("UPDATE quotations SET status = :status WHERE id = :quotation_id");
            // $stmt->execute([':status' => $new_status, ':quotation_id' => $quotation_id]);

            // Insert new status record in quotation_status table with optional status_date
            if (!empty($status_date)) {
                $stmt2 = $conn->prepare("INSERT INTO quotation_status (quotation_id, status_text, status_date) VALUES (:quotation_id, :status_text, :status_date)");
                $stmt2->execute([':quotation_id' => $quotation_id, ':status_text' => $new_status, ':status_date' => $status_date]);
            } else {
                $stmt2 = $conn->prepare("INSERT INTO quotation_status (quotation_id, status_text, status_date) VALUES (:quotation_id, :status_text, CURDATE())");
                $stmt2->execute([':quotation_id' => $quotation_id, ':status_text' => $new_status]);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } catch (PDOException $e) {
        error_log("Database error in ajax_update_quotation_status.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
