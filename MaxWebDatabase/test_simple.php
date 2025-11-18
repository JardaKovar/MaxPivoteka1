<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing reservation system...\n";

// Test 1: Check if db_config.php loads without errors
echo "1. Loading db_config.php...\n";
require_once 'db_config.php';
echo "   ✓ db_config.php loaded successfully\n";

// Test 2: Check if functions exist
echo "2. Checking functions...\n";
if (function_exists('logReservationActivity')) {
    echo "   ✓ logReservationActivity function exists\n";
} else {
    echo "   ✗ logReservationActivity function missing\n";
}

if (function_exists('sendReservationEmail')) {
    echo "   ✓ sendReservationEmail function exists\n";
} else {
    echo "   ✗ sendReservationEmail function missing\n";
}

// Test 3: Check database connection
echo "3. Checking database connection...\n";
if (isset($pdo) && $pdo) {
    echo "   ✓ Database connection successful\n";
} else {
    echo "   ✗ Database connection failed\n";
}

// Test 4: Check if reservations table exists
if (isset($pdo) && $pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'reservations'");
        if ($stmt->rowCount() > 0) {
            echo "   ✓ Reservations table exists\n";
        } else {
            echo "   ✗ Reservations table missing\n";
        }
    } catch (Exception $e) {
        echo "   ✗ Error checking reservations table: " . $e->getMessage() . "\n";
    }
}

echo "\nTest completed!\n";
?>
