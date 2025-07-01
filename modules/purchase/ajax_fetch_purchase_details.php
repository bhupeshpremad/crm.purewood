<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$po_id = $_POST['po_id'] ?? null;

if (!$po_id) {
    echo json_encode(['success' => false, 'message' => 'PO ID is required']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT sell_order_number, jci_number FROM po_main WHERE id = ?");
    $stmt->execute([$po_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode([
            'success' => true,
            'sell_order_number' => $result['sell_order_number'] ?? '',
            'jci_number' => $result['jci_number'] ?? ''
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Purchase Order not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching purchase details: ' . $e->getMessage()]);
}
?>
