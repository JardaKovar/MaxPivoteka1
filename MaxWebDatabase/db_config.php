<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'maxpivoteka_dashboard';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Always ensure tables are properly set up
    try {
        // Create logs table if not exists
        $sql = file_get_contents('create_logs_table.sql');
        $pdo->exec($sql);
        
        // Create reservations table if not exists
        $reservations_sql = file_get_contents('create_reservations_table.sql');
        $pdo->exec($reservations_sql);
    } catch (PDOException $e3) {
        error_log("Failed to setup tables: " . $e3->getMessage());
    }
    
} catch (PDOException $e) {
    // If database doesn't exist, create it
    try {
        $pdo_create = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
        $pdo_create->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo_create->exec("CREATE DATABASE IF NOT EXISTS $db_name");
        
        // Now connect to the created database
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Create tables
        $sql = file_get_contents('create_logs_table.sql');
        $pdo->exec($sql);
        
        // Create reservations table
        $reservations_sql = file_get_contents('create_reservations_table.sql');
        $pdo->exec($reservations_sql);
    } catch (PDOException $e2) {
        // Fallback to file-based logging if database fails
        error_log("Database connection failed: " . $e2->getMessage());
        $pdo = null;
    }
}

// Function to log activities
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
        } catch (PDOException $e) {
            error_log("Failed to log activity: " . $e->getMessage());
        }
    } else {
        // Fallback to file logging
        $log_entry = date('Y-m-d H:i:s') . " - $username - $action - $section - $details\n";
        file_put_contents('activity.log', $log_entry, FILE_APPEND | LOCK_EX);
    }
}

// Function to log login/logout sessions
function logSession($username, $action, $session_id = '', $pdo = null) {
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO login_sessions (username, action, ip_address, user_agent, session_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $username,
                $action,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $session_id
            ]);
        } catch (PDOException $e) {
            error_log("Failed to log session: " . $e->getMessage());
        }
    } else {
        // Fallback to file logging
        $log_entry = date('Y-m-d H:i:s') . " - $username - $action - Session: $session_id\n";
        file_put_contents('sessions.log', $log_entry, FILE_APPEND | LOCK_EX);
    }
}

// Function to log reservation activities
function logReservationActivity($username, $action, $reservation_id, $details = '', $pdo = null) {
    logActivity($username, $action, 'Reservations', "Reservation ID: $reservation_id - $details", $pdo);
}

// Function to send reservation email
function sendReservationEmail($to_email, $subject, $message, $from_name = 'MAX PIVOTÉKA') {
    // For local development, simulate email sending
    $http_host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if ($http_host === 'localhost' || strpos($http_host, '127.0.0.1') !== false || php_sapi_name() === 'cli') {
        // Log email instead of sending in development
        $email_log = "EMAIL SIMULATION\n";
        $email_log .= "To: $to_email\n";
        $email_log .= "Subject: $subject\n";
        $email_log .= "From: $from_name\n";
        $email_log .= "Time: " . date('Y-m-d H:i:s') . "\n";
        $email_log .= "Message: " . strip_tags($message) . "\n";
        $email_log .= str_repeat('-', 50) . "\n\n";
        
        file_put_contents('email_log.txt', $email_log, FILE_APPEND | LOCK_EX);
        return true; // Simulate successful sending
    }
    
    // For production, use actual mail sending
    $from_email = 'sebastianpokorny@seznam.cz';
    $headers = "From: $from_name <$from_email>\r\n";
    $headers .= "Reply-To: $from_email\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($to_email, $subject, $message, $headers);
}
?>
