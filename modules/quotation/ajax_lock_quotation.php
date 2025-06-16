<?php
include_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quotationId = $_POST['quotation_id'] ?? null;

    if (!$quotationId) {
        echo json_encode(['success' => false, 'message' => 'Quotation ID is required.']);
        exit;
    }

    try {
        // $database = new Database();
        // $conn = $database->getConnection();

        global $conn;

        // Update the locked status to 1 (locked)
        $stmt = $conn->prepare("UPDATE quotations SET locked = 1 WHERE id = ?");
        $stmt->execute([$quotationId]);

        echo json_encode(['success' => true, 'message' => 'Quotation locked successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error locking quotation: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
