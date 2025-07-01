b <?php
include 'config/config.php';
$stmt = $conn->query('DESCRIBE job_cards');
while ($row = $stmt->fetch()) {
    echo implode(' | ', $row) . PHP_EOL;
}
?>
