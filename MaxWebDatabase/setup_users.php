<?php
require_once 'db_config.php';

try {
    // Create users table
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            diary_access BOOLEAN DEFAULT FALSE,
            is_active BOOLEAN DEFAULT TRUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ');
    
    // Insert the new users
    $users = [
        ['MaxZ', 'FerdaBN25', false],
        ['Admin', 'NiggaFaggot1224', true],
        ['MaxP', 'BeneP04', true]
    ];
    
    $stmt = $pdo->prepare('INSERT IGNORE INTO users (username, password_hash, diary_access) VALUES (?, ?, ?)');
    foreach ($users as $user) {
        $stmt->execute([$user[0], password_hash($user[1], PASSWORD_DEFAULT), $user[2]]);
    }
    
    echo 'Users table created and users added successfully!<br>';
    
    // Verify users were created
    $stmt = $pdo->query('SELECT username, diary_access FROM users');
    $users = $stmt->fetchAll();
    echo 'Current users:<br>';
    foreach ($users as $user) {
        echo '- ' . $user['username'] . ' (diary access: ' . ($user['diary_access'] ? 'YES' : 'NO') . ')<br>';
    }
    
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage() . '<br>';
}
?>
