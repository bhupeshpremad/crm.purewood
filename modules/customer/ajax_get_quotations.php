<?php
include_once __DIR__ . '/../../config/config.php';

if (!isset($_GET['lead_id']) || empty($_GET['lead_id'])) {
    echo '<p>Lead ID is required.</p>';
    exit;
}

$lead_id = intval($_GET['lead_id']);

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("SELECT quotation_number, quotation_date FROM quotations WHERE lead_id = ?");
    $stmt->execute([$lead_id]);
    $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$quotations) {
        echo '<p>No quotations found for this lead.</p>';
        exit;
    }

    echo '<table class="table table-bordered">';
    echo '<thead><tr><th>Quotation Number</th><th>Quotation Date</th></tr></thead>';
    echo '<tbody>';
    foreach ($quotations as $quotation) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($quotation['quotation_number']) . '</td>';
        echo '<td>' . htmlspecialchars($quotation['quotation_date']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

} catch (PDOException $e) {
    echo '<p>Error fetching quotations: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
