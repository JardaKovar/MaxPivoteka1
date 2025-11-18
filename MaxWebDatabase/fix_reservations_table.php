<?php
require_once 'db_config.php';

echo "Fixing reservations table...\n";

if (!$pdo) {
    echo "Database connection failed!\n";
    exit;
}

try {
    // Drop the existing table
    echo "1. Dropping existing reservations table...\n";
    $pdo->exec("DROP TABLE IF EXISTS reservations");
    echo "   ✓ Table dropped successfully\n";
    
    // Create the new table with correct structure
    echo "2. Creating new reservations table...\n";
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
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_rental_date_from (rental_date_from),
        INDEX idx_rental_date_to (rental_date_to),
        INDEX idx_email (email)
    )";
    
    $pdo->exec($sql);
    echo "   ✓ New table created successfully\n";
    
    // Verify the table structure
    echo "3. Verifying table structure...\n";
    $stmt = $pdo->query("DESCRIBE reservations");
    $columns = $stmt->fetchAll();
    
    echo "   Table columns:\n";
    foreach ($columns as $column) {
        echo "   - " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    echo "\n✅ Reservations table fixed successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
