<?php
include 'config/config.php';

$stmt = $conn->query('DESCRIBE purchase_main');
while ($row = $stmt->fetch()) {
    echo implode(' | ', $row) . PHP_EOL;
}
?>
