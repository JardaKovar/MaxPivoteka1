<?php
session_start();
require_once __DIR__ . '/db_config.php';

// Hardcoded fallback accounts for 100% reliable login even without DB
$fallbackUsers = [
    'MaxP' => ['id' => 1, 'username' => 'MaxP', 'password' => 'BeneP04', 'diary_access' => true],
    'MaxZ' => ['id' => 2, 'username' => 'MaxZ', 'password' => 'FerdaBN25', 'diary_access' => false],
    'Admin' => ['id' => 3, 'username' => 'Admin', 'password' => 'NiggaFaggot1224', 'diary_access' => true]
];

// Initialize users table if database is connected
if ($pdo) {
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
        
        $insertStmt = $pdo->prepare("INSERT IGNORE INTO users (username, password_hash, diary_access) VALUES (?, ?, ?)");
        foreach ($fallbackUsers as $u) {
            $insertStmt->execute([$u['username'], password_hash($u['password'], PASSWORD_DEFAULT), $u['diary_access'] ? 1 : 0]);
        }
    } catch (Throwable $e) {
        error_log("Users table setup error: " . $e->getMessage());
    }
}

// Function to authenticate user
function authenticateUser($username, $password, $pdo, $fallbackUsers) {
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dbUser && password_verify($password, $dbUser['password_hash'])) {
                return $dbUser;
            }
        } catch (Throwable $e) {
            error_log("DB auth error: " . $e->getMessage());
        }
    }
    
    // Fallback to in-memory check
    if (isset($fallbackUsers[$username]) && $fallbackUsers[$username]['password'] === $password) {
        return $fallbackUsers[$username];
    }
    
    return false;
}

$error = '';

if (isset($_POST['username']) && isset($_POST['password'])) {
    if (!isset($_SESSION['failed_attempts'])) {
        $_SESSION['failed_attempts'] = 0;
        $_SESSION['last_attempt_time'] = time();
    }

    if (time() - $_SESSION['last_attempt_time'] > 900) {
        $_SESSION['failed_attempts'] = 0;
    }

    if ($_SESSION['failed_attempts'] >= 5) {
        $remainingLock = ceil((900 - (time() - $_SESSION['last_attempt_time'])) / 60);
        $error = "Příliš mnoho neúspěšných pokusů o přihlášení! Zkuste to znovu za $remainingLock minut.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $user = authenticateUser($username, $password, $pdo, $fallbackUsers);
        
        if ($user) {
            $_SESSION['failed_attempts'] = 0;
            @session_regenerate_id(true);
            
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['diary_access'] = !empty($user['diary_access']);
            $_SESSION['login_time'] = time();
            $_SESSION['session_token'] = bin2hex(random_bytes(32));
            
            if ($pdo) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO active_sessions (user_id, username, session_id, session_token, ip_address, user_agent, login_time) VALUES (?, ?, ?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE last_activity = NOW()");
                    $stmt->execute([
                        $user['id'],
                        $user['username'],
                        session_id(),
                        $_SESSION['session_token'],
                        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                    ]);
                } catch (Throwable $e) {}
            }
            
            logSession($user['username'], 'login', session_id(), $pdo);
            logActivity($user['username'], 'Logged in', 'Authentication', 'Successful login to dashboard', $pdo);
            
            header('Location: dashboard.php');
            exit;
        } else {
            usleep(500000);
            $_SESSION['failed_attempts']++;
            $_SESSION['last_attempt_time'] = time();
            $attemptsLeft = 5 - $_SESSION['failed_attempts'];

            $error = 'Nesprávné uživatelské jméno nebo heslo.';
            if ($attemptsLeft > 0 && $attemptsLeft < 5) {
                $error .= " Zbývá pokusů: $attemptsLeft.";
            }

            logActivity($username ?: 'unknown', 'Failed login attempt', 'Authentication', 'Invalid credentials provided', $pdo);
        }
    }
}

if (isset($_GET['logout'])) {
    $username = $_SESSION['username'] ?? 'unknown';
    logSession($username, 'logout', session_id(), $pdo);
    logActivity($username, 'Logged out', 'Authentication', 'User logged out', $pdo);
    
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE session_id = ?");
            $stmt->execute([session_id()]);
        } catch (Throwable $e) {}
    }
    
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    header('Location: login.php?logged_out=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení - MAX PIVOTÉKA</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ef4444;
            --primary-hover: #dc2626;
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(239, 68, 68, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.8) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: var(--text-main);
        }

        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-badge {
            background: linear-gradient(135deg, #ef4444, #991b1b);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 0.9rem;
            letter-spacing: 0.05em;
            display: inline-block;
            margin-bottom: 0.75rem;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .logo-container h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.25rem;
        }

        .logo-container p {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #cbd5e1;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i.input-icon {
            position: absolute;
            left: 1rem;
            color: var(--text-muted);
            font-size: 1rem;
            transition: color 0.2s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            color: var(--text-main);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
        }

        .input-wrapper input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
            background: rgba(15, 23, 42, 0.9);
        }

        .input-wrapper input:focus + i.input-icon {
            color: var(--primary);
        }

        .btn-submit {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--primary), #b91c1c);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--primary-hover), #991b1b);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: color 0.2s;
        }

        .back-link a:hover {
            color: var(--text-main);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-container">
            <div class="logo-badge">MAX PIVOTÉKA</div>
            <h1>Administrace</h1>
            <p>Přihlaste se ke správě webu</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['logged_out'])): ?>
            <div class="success-message">
                <i class="fa-solid fa-circle-check"></i>
                <span>Byli jste úspěšně odhlášeni.</span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Uživatelské jméno</label>
                <div class="input-wrapper">
                    <input type="text" id="username" name="username" required autocomplete="username" placeholder="Zadejte uživatelské jméno" autofocus>
                    <i class="fa-solid fa-user input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Heslo</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    <i class="fa-solid fa-lock input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span>Přihlásit se</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <div class="back-link">
            <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Zpět na hlavní stránku</a>
        </div>
    </div>

</body>
</html>
