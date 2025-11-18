<?php
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check if user is MaxZ - deny access to activity logs
if (isset($_SESSION['username']) && $_SESSION['username'] === 'MaxZ') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. You do not have permission to view activity logs.']);
    exit;
}

header('Content-Type: application/json');

$type = $_GET['type'] ?? 'recent';
$limit = (int)($_GET['limit'] ?? 20);

try {
    if ($pdo) {
        // Ensure limit is a safe integer
        $limit = max(1, min(100, (int)$limit));
        
        switch ($type) {
            case 'sessions':
                $stmt = $pdo->prepare("
                    SELECT username, action, ip_address, timestamp 
                    FROM login_sessions 
                    ORDER BY timestamp DESC 
                    LIMIT " . $limit
                );
                $stmt->execute();
                $logs = $stmt->fetchAll();
                break;
                
            case 'changes':
                $stmt = $pdo->prepare("
                    SELECT username, action, section, details, ip_address, timestamp 
                    FROM activity_logs 
                    WHERE section != 'Authentication'
                    ORDER BY timestamp DESC 
                    LIMIT " . $limit
                );
                $stmt->execute();
                $logs = $stmt->fetchAll();
                break;
                
            case 'recent':
            default:
                $stmt = $pdo->prepare("
                    SELECT username, action, section, details, ip_address, timestamp 
                    FROM activity_logs 
                    ORDER BY timestamp DESC 
                    LIMIT " . $limit
                );
                $stmt->execute();
                $logs = $stmt->fetchAll();
                break;
        }
        
        echo json_encode(['success' => true, 'logs' => $logs]);
    } else {
        // Fallback to file-based logs if database is not available
        $fallback_logs = [];
        
        if ($type === 'sessions' && file_exists('sessions.log')) {
            $lines = array_slice(file('sessions.log', FILE_IGNORE_NEW_LINES), -$limit);
            foreach ($lines as $line) {
                $parts = explode(' - ', $line);
                if (count($parts) >= 4) {
                    $fallback_logs[] = [
                        'timestamp' => $parts[0],
                        'username' => $parts[1],
                        'action' => $parts[2],
                        'ip_address' => 'unknown'
                    ];
                }
            }
        } elseif (file_exists('activity.log')) {
            $lines = array_slice(file('activity.log', FILE_IGNORE_NEW_LINES), -$limit);
            foreach ($lines as $line) {
                $parts = explode(' - ', $line);
                if (count($parts) >= 5) {
                    $fallback_logs[] = [
                        'timestamp' => $parts[0],
                        'username' => $parts[1],
                        'action' => $parts[2],
                        'section' => $parts[3],
                        'details' => $parts[4] ?? '',
                        'ip_address' => 'unknown'
                    ];
                }
            }
        }
        
        echo json_encode(['success' => true, 'logs' => array_reverse($fallback_logs)]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch logs: ' . $e->getMessage()]);
}
?>
