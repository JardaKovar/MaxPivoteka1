<?php
session_start();
require_once 'db_config.php';

// Function to verify password using proper password verification
function verifyPassword($inputPassword, $storedHash) {
    return password_verify($inputPassword, $storedHash);
}

// Function to get user from database
function getUser($username, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

// Create users table if it doesn't exist
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            diary_access BOOLEAN DEFAULT FALSE,
            is_active BOOLEAN DEFAULT TRUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Create active_sessions table for concurrent session management
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS active_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            username VARCHAR(50) NOT NULL,
            session_id VARCHAR(128) NOT NULL UNIQUE,
            session_token VARCHAR(64) NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            login_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_activity DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE,
            INDEX idx_user_id (user_id),
            INDEX idx_session_id (session_id),
            INDEX idx_username (username)
        )
    ");
    
    // Insert users if they don't exist
    $users = [
        ['MaxZ', 'FerdaBN25', false],
        ['Admin', 'NiggaFaggot1224', true],
                ['MaxP', 'BeneP04', true]
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, password_hash, diary_access) VALUES (?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute([$user[0], password_hash($user[1], PASSWORD_DEFAULT), $user[2]]);
    }
    
} catch (PDOException $e) {
    error_log("Failed to create users table: " . $e->getMessage());
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    // Brute Force Protection: Rate limiting & delays
    if (!isset($_SESSION['failed_attempts'])) {
        $_SESSION['failed_attempts'] = 0;
        $_SESSION['last_attempt_time'] = time();
    }

    // Reset lock after 15 minutes (900 seconds)
    if (time() - $_SESSION['last_attempt_time'] > 900) {
        $_SESSION['failed_attempts'] = 0;
    }

    if ($_SESSION['failed_attempts'] >= 5) {
        $remainingLock = ceil((900 - (time() - $_SESSION['last_attempt_time'])) / 60);
        $error = "Příliš mnoho neúspěšných pokusů o přihlášení! Účet je dočasně uzamčen. Zkuste to znovu za $remainingLock minut.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $user = getUser($username, $pdo);
        
        if ($user && verifyPassword($password, $user['password_hash'])) {
            // Reset failed attempts on success
            $_SESSION['failed_attempts'] = 0;

            // Generate unique session ID for security
            session_regenerate_id(true);
            
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['diary_access'] = $user['diary_access'];
            $_SESSION['login_time'] = time();
            $_SESSION['session_token'] = bin2hex(random_bytes(32));
            
            // Store active session in database
            if ($pdo) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO active_sessions (user_id, username, session_id, session_token, ip_address, user_agent, login_time) VALUES (?, ?, ?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE last_activity = NOW()");
                    $stmt->execute([
                        $user['id'],
                        $username,
                        session_id(),
                        $_SESSION['session_token'],
                        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                    ]);
                } catch (PDOException $e) {
                    error_log("Failed to store active session: " . $e->getMessage());
                }
            }
            
            // Log successful login
            logSession($username, 'login', session_id(), $pdo);
            logActivity($username, 'Logged in', 'Authentication', 'Successful login to dashboard (Session: ' . session_id() . ')', $pdo);
            
            header('Location: dashboard.php');
            exit;
        } else {
            // Anti-bruteforce artificial delay (1.5 seconds)
            usleep(1500000);

            $_SESSION['failed_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            $attemptsLeft = 5 - $_SESSION['failed_attempts'];

            $error = 'Nespávné uživatelské jméno nebo heslo.';
            if ($attemptsLeft > 0 && $attemptsLeft < 5) {
                $error .= " Zbývá pokusů: $attemptsLeft.";
            }

            // Log failed login attempt
            if (isset($pdo)) {
                logActivity($username ?? 'unknown', 'Failed login attempt', 'Authentication', 'Invalid credentials provided', $pdo);
            }
        }
    }
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['username'])) {
        // Log logout
        logSession($_SESSION['username'], 'logout', session_id(), $pdo);
        logActivity($_SESSION['username'], 'Logged out', 'Authentication', 'User logged out from dashboard', $pdo);
        // Remove active session from active_sessions table
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE session_id = ?");
                $stmt->execute([session_id()]);
            } catch (PDOException $e) {
                error_log("Failed to remove active session: " . $e->getMessage());
            }
        }
    }
    session_destroy();
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - MAX PIVOTÉKA Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #222;
            color: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #333;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
            width: 300px;
        }
        h1 {
            margin-bottom: 1rem;
            font-weight: 700;
            text-align: center;
        }
        label {
            display: block;
            margin-top: 1rem;
            font-weight: 600;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.3rem;
            border-radius: 4px;
            border: none;
            font-size: 1rem;
        }
        button {
            margin-top: 1.5rem;
            width: 100%;
            padding: 0.6rem;
            font-weight: 700;
            border: none;
            border-radius: 4px;
            background-color: #fff;
            color: #222;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #ddd;
        }
        .error {
            margin-top: 1rem;
            color: #f44336;
            font-weight: 600;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus />
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />
            <button type="submit">Log In</button>
        </form>
    </div>
</body>
</html>
