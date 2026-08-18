<?php
session_start();
require_once __DIR__ . '/db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (isset($_SESSION['username']) && $_SESSION['username'] === 'MaxZ') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. You do not have permission to clear activity logs.']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$username = $_SESSION['username'] ?? 'User';

if ($pdo) {
    try {
        @$pdo->exec("DELETE FROM activity_logs");
        @$pdo->exec("DELETE FROM current_logs");
        @$pdo->exec("DELETE FROM active_sessions");
    } catch (Throwable $e) {}
}

// Clear log files
$filesToClear = [
    __DIR__ . '/data/activity.log',
    __DIR__ . '/data/sessions.log',
    __DIR__ . '/activity.log',
    __DIR__ . '/sessions.log'
];

foreach ($filesToClear as $f) {
    if (file_exists($f)) {
        @file_put_contents($f, '');
    }
}

logActivity($username, 'Clear History', 'System', 'Cleared all activity & session logs', $pdo);

echo json_encode([
    'success' => true,
    'message' => 'Historie aktivit byla úspěšně vymazána'
]);
exit;
?>