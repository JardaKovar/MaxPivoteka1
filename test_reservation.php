<?php
// Test script to simulate reservation form submission

// Simulate POST data with future dates
$_POST = [
    'first_name' => 'Jan',
    'last_name' => 'Novák',
    'email' => 'jan.novak@test.cz',
    'phone' => '+420123456789',
    'rental_item' => 'PÍPY - PŘENOSNÉ CHLAZENÍ',
    'rental_period' => 'vikend',
    'rental_date_from' => date('Y-m-d', strtotime('+3 days')),
    'rental_date_to' => date('Y-m-d', strtotime('+5 days')),
    'additional_info' => 'Test reservation'
];

$_SERVER['REQUEST_METHOD'] = 'POST';

// Include the submission script directly
include 'submit_reservation.php';
?>
