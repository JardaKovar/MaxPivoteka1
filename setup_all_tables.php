<?php
require_once 'db_config.php';

echo "<h1>Database Tables Setup</h1>\n";
echo "<pre>\n";

try {
    if ($pdo) {
        // Create events table
        $sql = "CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            date VARCHAR(20),
            title VARCHAR(255),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        echo "✓ Events table created successfully\n";
        
        // Create taplist table
        $sql = "CREATE TABLE IF NOT EXISTS taplist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            number INT,
            brewery VARCHAR(255),
            beer VARCHAR(255),
            alc VARCHAR(50),
            epm VARCHAR(50),
            ibu VARCHAR(50),
            ebc VARCHAR(50),
            price_05l DECIMAL(10, 2),
            price_03l DECIMAL(10, 2)
        )";
        $pdo->exec($sql);
        echo "✓ Taplist table created successfully\n";
        
        // Create rentallist table
        $sql = "CREATE TABLE IF NOT EXISTS rentallist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            number INT,
            desc1 VARCHAR(255),
            image VARCHAR(255),
            desc2 VARCHAR(255),
            deposit VARCHAR(50),
            day VARCHAR(50),
            weekend VARCHAR(50),
            week VARCHAR(50),
            month VARCHAR(50)
        )";
        $pdo->exec($sql);
        echo "✓ Rentallist table created successfully\n";
        
        // Create activity_logs table (if not exists)
        $sql = "CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            action VARCHAR(100) NOT NULL,
            section VARCHAR(50) NOT NULL,
            details TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        echo "✓ Activity logs table created successfully\n";
        
        // Create login_sessions table (if not exists)
        $sql = "CREATE TABLE IF NOT EXISTS login_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            action ENUM('login', 'logout') NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            session_id VARCHAR(128),
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        echo "✓ Login sessions table created successfully\n";
        
        // Create current_activity_logs table (if not exists)
        $sql = "CREATE TABLE IF NOT EXISTS current_activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            section VARCHAR(50) NOT NULL,
            details TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $pdo->exec($sql);
        echo "✓ Current activity logs table created successfully\n";
        
        // Show table counts
        echo "\n=== Table Status ===\n";
        $tables = ['events', 'taplist', 'rentallist', 'activity_logs', 'login_sessions', 'current_activity_logs'];
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch()['count'];
                echo "$table: $count records\n";
            } catch (Exception $e) {
                echo "$table: Error - " . $e->getMessage() . "\n";
            }
        }
        
    } else {
        echo "✗ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Setup completed ===\n";
echo "</pre>\n";
?>
