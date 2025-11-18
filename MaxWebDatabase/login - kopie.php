<?php
session_start();
require_once 'db_config.php';

$valid_username = 'MaxP';
$valid_password = 'BeneP04';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        
        // Log successful login
        logSession($username, 'login', session_id(), $pdo);
        logActivity($username, 'Logged in', 'Authentication', 'Successful login to dashboard', $pdo);
        
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
        // Log failed login attempt
        if (isset($pdo)) {
            logActivity($username ?? 'unknown', 'Failed login attempt', 'Authentication', 'Invalid credentials provided', $pdo);
        }
    }
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['username'])) {
        // Log logout
        logSession($_SESSION['username'], 'logout', session_id(), $pdo);
        logActivity($_SESSION['username'], 'Logged out', 'Authentication', 'User logged out from dashboard', $pdo);
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
