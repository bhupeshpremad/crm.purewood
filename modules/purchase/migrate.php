<?php
include_once __DIR__ . '/../../config/config.php';

global $conn;

function runMigration($conn) {
    $sql = file_get_contents(__DIR__ . '/sql_purchase_relations.sql');
    if ($sql === false) {
        echo "Failed to read migration SQL file.\n";
        return false;
    }

    // Split SQL statements by semicolon for execution
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            if ($conn->exec($statement) === false) {
                $errorInfo = $conn->errorInfo();
                echo "Error executing statement: " . $errorInfo[2] . "\n";
                echo "Statement: " . $statement . "\n";
                return false;
            }
        }
    }
    return true;
}

if (runMigration($conn)) {
    echo "Migration completed successfully.\n";
} else {
    echo "Migration failed.\n";
}
?>
