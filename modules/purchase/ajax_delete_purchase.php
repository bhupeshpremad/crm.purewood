<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$po_number = $_POST['po_number'] ?? null;

if (empty($po_number)) {
    echo json_encode(['success' => false, 'message' => 'PO Number is required for deletion.']);
    exit;
}

global $conn;
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection not available.']);
    exit;
}

try {
    $conn->beginTransaction();

    // The foreign key constraints with ON DELETE CASCADE will handle deleting
    // from purchase_wood, purchase_glow, purchase_plynydf, purchase_hardware.
    // So, we only need to delete from purchase_main.

    $stmt = $conn->prepare("DELETE FROM purchase_main WHERE po_number = :po_number");
    $stmt->bindValue(':po_number', $po_number, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Purchase record and associated items deleted successfully.']);
    } else {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Purchase record not found or could not be deleted.']);
    }

} catch (PDOException $e) {
    $conn->rollBack();
    error_log("Database error deleting purchase record: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    $conn->rollBack();
    error_log("Error deleting purchase record: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
}
exit;