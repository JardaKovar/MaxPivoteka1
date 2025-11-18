<?php
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check if user is MaxZ - deny access to clear activity logs
if (isset($_SESSION['username']) && $_SESSION['username'] === 'MaxZ') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. You do not have permission to clear activity logs.']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$username = $_SESSION['username'] ?? 'Unknown';
$success = true;
$errors = [];

try {
    if ($pdo) {
        // Begin transaction
        $pdo->beginTransaction();
        
        try {
            // Clear activity_logs table
            $stmt = $pdo->prepare("DELETE FROM activity_logs");
            $stmt->execute();
            $activityCount = $stmt->rowCount();
            
            // Clear login_sessions table
            $stmt = $pdo->prepare("DELETE FROM login_sessions");
            $stmt->execute();
            $sessionCount = $stmt->rowCount();
            
            // Clear current_activity_logs table if it exists
            $currentCount = 0;
            try {
                $stmt = $pdo->prepare("DELETE FROM current_activity_logs");
                $stmt->execute();
                $currentCount = $stmt->rowCount();
            } catch (PDOException $e) {
                // Table might not exist, that's okay
            }
            
            // Reset auto-increment counters
            $pdo->exec("ALTER TABLE activity_logs AUTO_INCREMENT = 1");
            $pdo->exec("ALTER TABLE login_sessions AUTO_INCREMENT = 1");
            try {
                $pdo->exec("ALTER TABLE current_activity_logs AUTO_INCREMENT = 1");
            } catch (PDOException $e) {
                // Table might not exist, that's okay
            }
            
            // Commit transaction
            $pdo->commit();
            
            // Log this clearing action
            logActivity($username, 'Clear History', 'System', "Cleared $activityCount activity logs, $sessionCount session logs, $currentCount current logs", $pdo);
            
        } catch (PDOException $e) {
            $pdo->rollback();
            throw $e;
        }
    }
    
    // Clear fallback log files if they exist
    $filesCleared = [];
    if (file_exists('activity.log')) {
        if (unlink('activity.log')) {
            $filesCleared[] = 'activity.log';
        } else {
            $errors[] = 'Failed to delete activity.log';
        }
    }
    
    if (file_exists('sessions.log')) {
        if (unlink('sessions.log')) {
            $filesCleared[] = 'sessions.log';
        } else {
            $errors[] = 'Failed to delete sessions.log';
        }
    }
    
    $response = [
        'success' => true,
        'message' => 'Activity log history cleared successfully',
        'details' => [
            'database_cleared' => $pdo ? true : false,
            'files_cleared' => $filesCleared,
            'activity_logs_deleted' => $activityCount ?? 0,
            'session_logs_deleted' => $sessionCount ?? 0,
            'current_logs_deleted' => $currentCount ?? 0
        ]
    ];
    
    if (!empty($errors)) {
        $response['warnings'] = $errors;
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to clear activity logs: ' . $e->getMessage(),
        'warnings' => $errors
    ]);
}
?>
