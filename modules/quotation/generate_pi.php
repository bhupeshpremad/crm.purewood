<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$requiredFields = ['quotation_id', 'payment_term', 'inspection', 'date_of_pi_raised'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Field $field is required."]);
        exit;
    }
}

$quotationId = intval($_POST['quotation_id']);
$paymentTerm = trim($_POST['payment_term']);
$inspection = trim($_POST['inspection']);
$dateOfPiRaised = trim($_POST['date_of_pi_raised']);
$sampleApprovalDate = isset($_POST['sample_approval_date']) ? trim($_POST['sample_approval_date']) : null;
$discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0.0;

try {
    $database = new Database();
    $conn = $database->getConnection();

    $conn->beginTransaction();

    // Check if PI record exists for this quotation_id
    $checkStmt = $conn->prepare("SELECT pi_id, pi_number FROM pi WHERE quotation_id = ?");
    $checkStmt->execute([$quotationId]);
    $existingPi = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingPi) {
        // Use existing pi_number
        $piNumber = $existingPi['pi_number'];

        // Update existing PI record
        $updateStmt = $conn->prepare("UPDATE pi SET payment_term = ?, inspection = ?, date_of_pi_raised = ?, sample_approval_date = ?, discount = ? WHERE pi_id = ?");
        $updateStmt->execute([
            $paymentTerm,
            $inspection,
            $dateOfPiRaised,
            $sampleApprovalDate,
            $discount,
            $existingPi['pi_id']
        ]);
    } else {
        // Generate PI number in format PI-YEAR-00001 with yearly reset
        $currentYear = date('Y');
        $stmtMax = $conn->prepare("SELECT MAX(pi_number) AS max_pi_number FROM pi WHERE pi_number LIKE ?");
        $likePattern = "PI-$currentYear-%";
        $stmtMax->execute([$likePattern]);
        $row = $stmtMax->fetch(PDO::FETCH_ASSOC);
        $maxPiNumber = $row['max_pi_number'] ?? null;

        if ($maxPiNumber) {
            $parts = explode('-', $maxPiNumber);
            $lastNumber = intval($parts[2]);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $piNumber = sprintf("PI-%s-%05d", $currentYear, $newNumber);

        // Insert PI data into pi table with pi_number
        $stmt = $conn->prepare("INSERT INTO pi (quotation_id, pi_number, payment_term, inspection, date_of_pi_raised, sample_approval_date, discount) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $quotationId,
            $piNumber,
            $paymentTerm,
            $inspection,
            $dateOfPiRaised,
            $sampleApprovalDate,
            $discount
        ]);
    }

    // Update quotation approve status to 1 (approved)
    $updateStmt = $conn->prepare("UPDATE quotations SET approve = 1 WHERE id = ?");
    $updateStmt->execute([$quotationId]);

    // Insert status record "Approved" with current date
    $statusStmt = $conn->prepare("INSERT INTO quotation_status (quotation_id, status_text, status_date) VALUES (?, ?, CURDATE())");
    $statusStmt->execute([$quotationId, 'Approved']);

    $conn->commit();

    // Return success with PI number
    echo json_encode(['success' => true, 'pi_number' => $piNumber]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error saving PI data: ' . $e->getMessage()]);
}
?>
