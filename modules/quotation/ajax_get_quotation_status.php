<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $quotation_id = isset($_GET['quotation_id']) ? intval($_GET['quotation_id']) : 0;

    if ($quotation_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid quotation ID']);
        exit;
    }

    try {
        /** @var PDO $conn */
        global $conn;

        /** @var PDOStatement $stmt */
        $stmt = $conn->prepare("SELECT qs.id, q.quotation_number, qs.status_text, qs.status_date FROM quotation_status qs JOIN quotations q ON qs.quotation_id = q.id WHERE qs.quotation_id = :quotation_id ORDER BY qs.id DESC");
        $stmt->execute([':quotation_id' => $quotation_id]);
        $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $statuses]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
