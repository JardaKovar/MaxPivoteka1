<?php
// Test form submission directly
require_once 'db_config.php';

// Simulate form data
$_POST = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'phone' => '+420123456789',
    'rental_item' => 'GRILY - PLYNOVÝ GRIL',
    'rental_period' => 'den',
    'rental_date_from' => '2025-02-01',
    'rental_date_to' => '2025-02-02',
    'additional_info' => 'Test reservation'
];

$_SERVER['REQUEST_METHOD'] = 'POST';

// Capture output
ob_start();
include 'submit_reservation.php';
$output = ob_get_clean();

echo "Form submission test result:\n";
echo $output;
echo "\n\nChecking database for the reservation...\n";

// Check if reservation was saved
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM reservations ORDER BY id DESC LIMIT 1");
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reservation) {
            echo "✅ Reservation found in database:\n";
            echo "ID: " . $reservation['id'] . "\n";
            echo "Name: " . $reservation['first_name'] . " " . $reservation['last_name'] . "\n";
            echo "Email: " . $reservation['email'] . "\n";
            echo "Status: " . $reservation['status'] . "\n";
            echo "Created: " . $reservation['created_at'] . "\n";
        } else {
            echo "❌ No reservation found in database\n";
        }
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Database connection failed\n";
}
?>
