<?php
require_once 'db_config.php';

try {
    if ($pdo) {
        $sql = file_get_contents('create_reservations_table.sql');
        $pdo->exec($sql);
        echo "Reservations table created successfully!\n";
    } else {
        echo "Database connection failed!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
