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

    $stmt = $conn->prepare("
        SELECT p.pi_number, p.date_of_pi_raised
        FROM pi p
        INNER JOIN quotations q ON p.quotation_id = q.id
        WHERE q.lead_id = ?
    ");
    $stmt->execute([$lead_id]);
    $pis = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$pis) {
        echo '<p>No PIs found for this lead.</p>';
        exit;
    }

    echo '<table class="table table-bordered">';
    echo '<thead><tr><th>PI Number</th><th>Date of PI Raised</th></tr></thead>';
    echo '<tbody>';
    foreach ($pis as $pi) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($pi['pi_number']) . '</td>';
        echo '<td>' . htmlspecialchars($pi['date_of_pi_raised']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

} catch (PDOException $e) {
    echo '<p>Error fetching PIs: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
