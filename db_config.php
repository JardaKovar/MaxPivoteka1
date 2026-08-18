<?php
// Function to load .env variables for security and flexibility
if (!function_exists('loadEnv')) {
    function loadEnv($path) {
        if (!file_exists($path)) return;
        $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) return;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
}

// Load environment variables from .env file
loadEnv(__DIR__ . '/.env');

// Database configuration loaded from environment or defaults
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: 'maxpivoteka_dashboard';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$pdo = null;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Ensure basic tables exist
    try {
        if (file_exists(__DIR__ . '/create_logs_table.sql')) {
            $sql = @file_get_contents(__DIR__ . '/create_logs_table.sql');
            if ($sql) $pdo->exec($sql);
        }
        if (file_exists(__DIR__ . '/create_reservations_table.sql')) {
            $reservations_sql = @file_get_contents(__DIR__ . '/create_reservations_table.sql');
            if ($reservations_sql) $pdo->exec($reservations_sql);
        }
    } catch (Throwable $e3) {
        error_log("Failed to setup tables: " . $e3->getMessage());
    }
    
} catch (Throwable $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $pdo = null;
}

// Function to log activities safely
if (!function_exists('logActivity')) {
    function logActivity($username, $action, $section, $details = '', $pdo = null) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO activity_logs (username, action, section, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $username,
                    $action,
                    $section,
                    $details,
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
                return;
            } catch (Throwable $e) {
                error_log("Failed to log activity to DB: " . $e->getMessage());
            }
        }
        
        // Fallback to file logging in data directory
        try {
            $logDir = __DIR__ . '/data';
            if (!file_exists($logDir)) @mkdir($logDir, 0755, true);
            $log_entry = date('Y-m-d H:i:s') . " - $username - $action - $section - $details\n";
            @file_put_contents($logDir . '/activity.log', $log_entry, FILE_APPEND | LOCK_EX);
        } catch (Throwable $ex) {}
    }
}

// Function to log login/logout sessions safely
if (!function_exists('logSession')) {
    function logSession($username, $action, $session_id, $pdo = null) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO current_logs (username, action, session_id, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $username,
                    $action,
                    $session_id,
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
                return;
            } catch (Throwable $e) {
                error_log("Failed to log session to DB: " . $e->getMessage());
            }
        }
        
        try {
            $logDir = __DIR__ . '/data';
            if (!file_exists($logDir)) @mkdir($logDir, 0755, true);
            $log_entry = date('Y-m-d H:i:s') . " - $username - $action - Session: $session_id\n";
            @file_put_contents($logDir . '/sessions.log', $log_entry, FILE_APPEND | LOCK_EX);
        } catch (Throwable $ex) {}
    }
}
?>