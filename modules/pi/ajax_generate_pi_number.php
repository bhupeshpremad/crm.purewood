<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (empty($_GET['quotation_id'])) {
    echo json_encode(['success' => false, 'message' => 'Quotation ID is required']);
    exit;
}

$quotationId = intval($_GET['quotation_id']);

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Check if PI record exists for this quotation_id
    $checkStmt = $conn->prepare("SELECT pi_number FROM pi WHERE quotation_id = ?");
    $checkStmt->execute([$quotationId]);
    $existingPi = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingPi) {
        $piNumber = $existingPi['pi_number'];
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
    }

    echo json_encode(['success' => true, 'pi_number' => $piNumber]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error generating PI number: ' . $e->getMessage()]);
}
?>
