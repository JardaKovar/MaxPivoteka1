<?php
session_start();
require_once __DIR__ . '/db_config.php';

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

header('Content-Type: application/json; charset=utf-8');

$type = $_GET['type'] ?? 'recent';
$limit = (int)($_GET['limit'] ?? 50);
$limit = max(1, min(100, $limit));

$logs = [];

if ($pdo) {
    try {
        if ($type === 'sessions') {
            try {
                $stmt = $pdo->prepare("SELECT username, action, ip_address, timestamp FROM current_logs ORDER BY timestamp DESC LIMIT " . $limit);
                $stmt->execute();
                $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Throwable $e1) {
                $stmt = $pdo->prepare("SELECT username, action, ip_address, timestamp FROM activity_logs WHERE section = 'Authentication' ORDER BY timestamp DESC LIMIT " . $limit);
                $stmt->execute();
                $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } elseif ($type === 'changes') {
            $stmt = $pdo->prepare("SELECT username, action, section, details, ip_address, timestamp FROM activity_logs WHERE section != 'Authentication' ORDER BY timestamp DESC LIMIT " . $limit);
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->prepare("SELECT username, action, section, details, ip_address, timestamp FROM activity_logs ORDER BY timestamp DESC LIMIT " . $limit);
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Throwable $e) {
        error_log("Failed to load logs from DB: " . $e->getMessage());
    }
}

// Fallback / supplement from file logs if database logs are empty
if (empty($logs)) {
    $logDir = __DIR__ . '/data';
    
    if ($type === 'sessions') {
        $sessionFiles = [$logDir . '/sessions.log', __DIR__ . '/sessions.log'];
        foreach ($sessionFiles as $sFile) {
            if (file_exists($sFile)) {
                $lines = array_reverse(file($sFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                foreach ($lines as $line) {
                    $parts = explode(' - ', $line);
                    if (count($parts) >= 3) {
                        $logs[] = [
                            'timestamp' => trim($parts[0]),
                            'username' => trim($parts[1]),
                            'action' => trim($parts[2]),
                            'ip_address' => 'localhost'
                        ];
                    }
                    if (count($logs) >= $limit) break;
                }
                if (!empty($logs)) break;
            }
        }
    } else {
        $activityFiles = [$logDir . '/activity.log', __DIR__ . '/activity.log'];
        foreach ($activityFiles as $aFile) {
            if (file_exists($aFile)) {
                $lines = array_reverse(file($aFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                foreach ($lines as $line) {
                    $parts = explode(' - ', $line);
                    if (count($parts) >= 4) {
                        $section = trim($parts[3] ?? '');
                        if ($type === 'changes' && $section === 'Authentication') {
                            continue;
                        }
                        $logs[] = [
                            'timestamp' => trim($parts[0]),
                            'username' => trim($parts[1]),
                            'action' => trim($parts[2]),
                            'section' => $section,
                            'details' => trim($parts[4] ?? ''),
                            'ip_address' => 'localhost'
                        ];
                    }
                    if (count($logs) >= $limit) break;
                }
                if (!empty($logs)) break;
            }
        }
    }
}

echo json_encode(['success' => true, 'logs' => $logs]);
exit;
?>