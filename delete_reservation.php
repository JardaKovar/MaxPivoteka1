<?php
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        echo json_encode(['success' => false, 'error' => 'Missing reservation ID']);
        exit;
    }
    
    $reservationId = (int)$input['id'];
    
    if (!$pdo) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    // Get reservation details before deletion for logging
    $stmt = $pdo->prepare("SELECT first_name, last_name, email FROM reservations WHERE id = ?");
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        echo json_encode(['success' => false, 'error' => 'Reservation not found']);
        exit;
    }
    
    // Delete the reservation
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $result = $stmt->execute([$reservationId]);
    
    if ($result && $stmt->rowCount() > 0) {
        // Log the activity
        if (function_exists('logActivity')) {
            $customerName = $reservation['first_name'] . ' ' . $reservation['last_name'];
            logActivity($pdo, $_SESSION['username'], 'Reservation Deleted', 'Reservations', "Deleted reservation for {$customerName} ({$reservation['email']})");
        }
        
        echo json_encode(['success' => true, 'message' => 'Reservation deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete reservation']);
    }
    
} catch (PDOException $e) {
    error_log("Database error in delete_reservation.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Error in delete_reservation.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred']);
}
?>
