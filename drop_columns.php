<?php
require_once 'db_config.php';

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Drop the unused columns
    $conn->query("ALTER TABLE taplist DROP COLUMN ibu");
    $conn->query("ALTER TABLE taplist DROP COLUMN ebc");
    $conn->query("ALTER TABLE taplist DROP COLUMN price_03l");

    echo "Columns dropped successfully";

    $conn->close();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
