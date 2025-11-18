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
    
    if (!$input || !isset($input['id']) || !isset($input['status'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
        exit;
    }
    
    $reservationId = (int)$input['id'];
    $status = $input['status'];
    
    // Validate status
    $validStatuses = ['pending', 'confirmed', 'cancelled'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        exit;
    }
    
    if (!$pdo) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }
    
    // Update reservation status
    $stmt = $pdo->prepare("UPDATE reservations SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $result = $stmt->execute([$status, $reservationId]);
    
    if ($result && $stmt->rowCount() > 0) {
        // Log the activity
        if (function_exists('logActivity')) {
            logActivity($pdo, $_SESSION['username'], 'Reservation Status Updated', 'Reservations', "Updated reservation ID {$reservationId} to {$status}");
        }
        
        echo json_encode(['success' => true, 'message' => 'Reservation status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Reservation not found or no changes made']);
    }
    
} catch (PDOException $e) {
    error_log("Database error in update_reservation_status.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("Error in update_reservation_status.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred']);
}
?>
