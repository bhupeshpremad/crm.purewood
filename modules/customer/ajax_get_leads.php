<?php
include_once __DIR__ . '/../../config/config.php';

if (!isset($_GET['lead_id']) || empty($_GET['lead_id'])) {
    echo '<p>Lead ID is required.</p>';
    exit;
}

$lead_id = intval($_GET['lead_id']);

try {
    // $database = new Database();
    // $conn = $database->getConnection();

    global $conn;


    $stmt = $conn->prepare("SELECT lead_number, entry_date FROM leads WHERE id = ?");
    $stmt->execute([$lead_id]);
    $lead = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lead) {
        echo '<p>Lead not found.</p>';
        exit;
    }

    // Return table format for consistency
    echo '<table class="table table-bordered">';
    echo '<thead><tr><th>Lead Number</th><th>Entry Date</th></tr></thead>';
    echo '<tbody>';
    echo '<tr>';
    echo '<td>' . htmlspecialchars($lead['lead_number']) . '</td>';
    echo '<td>' . htmlspecialchars($lead['entry_date']) . '</td>';
    echo '</tr>';
    echo '</tbody></table>';

} catch (PDOException $e) {
    echo '<p>Error fetching lead details: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
