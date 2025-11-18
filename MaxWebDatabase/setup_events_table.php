<?php
require_once 'db_config.php';

echo "<h1>Events Table Setup</h1>\n";
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
        
        // Check if table exists and show structure
        $stmt = $pdo->query("DESCRIBE events");
        $columns = $stmt->fetchAll();
        
        echo "\nTable structure:\n";
        foreach ($columns as $column) {
            echo "- {$column['Field']}: {$column['Type']}\n";
        }
        
        // Check current data
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM events");
        $count = $stmt->fetch()['count'];
        echo "\nCurrent records in events table: $count\n";
        
    } else {
        echo "✗ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Setup completed ===\n";
echo "</pre>\n";
?>
