<?php
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    if (!$pdo) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    // Get all reservations ordered by creation date (newest first)
    $stmt = $pdo->prepare("SELECT * FROM reservations ORDER BY created_at DESC");
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data for display
    $formatted_reservations = [];
    foreach ($reservations as $reservation) {
        $formatted_reservations[] = [
            'id' => $reservation['id'],
            'first_name' => $reservation['first_name'],
            'last_name' => $reservation['last_name'],
            'email' => $reservation['email'],
            'phone' => $reservation['phone'] ?? '',
            'rental_item' => $reservation['rental_item'],
            'rental_period' => $reservation['rental_period'],
            'rental_date_from' => $reservation['rental_date_from'],
            'rental_date_to' => $reservation['rental_date_to'],
            'additional_info' => $reservation['additional_info'] ?? '',
            'status' => $reservation['status'],
            'created_at' => $reservation['created_at'],
            'updated_at' => $reservation['updated_at']
        ];
    }

    echo json_encode([
        'success' => true,
        'reservations' => $formatted_reservations,
        'count' => count($formatted_reservations)
    ]);

} catch (PDOException $e) {
    error_log("Get reservations error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error loading reservations: ' . $e->getMessage()
    ]);
}
?>
