<?php
header('Content-Type: application/json');
include_once '../../../config/config.php';
include_once '../../../core/services/QuotationService.php';

$database = new Database();
$conn = $database->getConnection();
$quotationService = new QuotationService($conn);

$response = ['success' => false, 'message' => 'Unknown error occurred'];

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
        if ($approve !== null) {
            $stmt = $conn->prepare("UPDATE quotations SET approve = :approve WHERE id = :quotation_id");
            $stmt->execute([':approve' => $approve, ':quotation_id' => $quotation_id]);
        }

        if (!empty($new_status)) {
            if (!empty($status_date)) {
                $stmt2 = $conn->prepare("INSERT INTO quotation_status (quotation_id, status_text, status_date) VALUES (:quotation_id, :status_text, :status_date)");
                $stmt2->execute([':quotation_id' => $quotation_id, ':status_text' => $new_status, ':status_date' => $status_date]);
            } else {
                $stmt2 = $conn->prepare("INSERT INTO quotation_status (quotation_id, status_text, status_date) VALUES (:quotation_id, :status_text, CURDATE())");
                $stmt2->execute([':quotation_id' => $quotation_id, ':status_text' => $new_status]);
            }
        }

        $response = ['success' => true, 'message' => 'Status updated successfully'];
    } catch (PDOException $e) {
        error_log("Database error in QuotationController: " . $e->getMessage());
        $response = ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request method'];
}

echo json_encode($response);
exit;
?>
