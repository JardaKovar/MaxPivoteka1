<?php
echo "Testing database connection...\n";

require_once 'db_config.php';

if ($pdo) {
    echo "Database connection successful!\n";
    
    try {
        // Check if reservations table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'reservations'");
        $table_exists = $stmt->fetch();
        
        if ($table_exists) {
            echo "Reservations table exists!\n";
            
            // Check table structure
            $stmt = $pdo->query("DESCRIBE reservations");
            $columns = $stmt->fetchAll();
            echo "Table columns:\n";
            foreach ($columns as $column) {
                echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
            }
            
            // Check current data
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM reservations");
            $result = $stmt->fetch();
            echo "Current reservations count: " . $result['count'] . "\n";
            
        } else {
            echo "Reservations table does not exist!\n";
            echo "Creating table...\n";
            
            // Create table
            $sql = "CREATE TABLE IF NOT EXISTS reservations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20),
                rental_item VARCHAR(255) NOT NULL,
                rental_period VARCHAR(50) NOT NULL,
                rental_date_from DATE NOT NULL,
                rental_date_to DATE NOT NULL,
                additional_info TEXT,
                status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            $pdo->exec($sql);
            echo "Table created successfully!\n";
        }
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "Database connection failed!\n";
}
?>
