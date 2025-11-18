<?php
require_once 'db_config.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create gallery_images table
    $sql1 = "CREATE TABLE IF NOT EXISTS gallery_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_size INT DEFAULT 0,
        mime_type VARCHAR(100) DEFAULT NULL,
        upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('active', 'deleted') DEFAULT 'active',
        created_by VARCHAR(100) DEFAULT 'admin',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_filename (filename),
        INDEX idx_status (status),
        INDEX idx_upload_date (upload_date)
    )";
    
    $pdo->exec($sql1);
    echo "✅ Created gallery_images table\n";
    
    // Create gallery_operations table
    $sql2 = "CREATE TABLE IF NOT EXISTS gallery_operations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        operation_type ENUM('upload', 'delete', 'change', 'rename') NOT NULL,
        old_filename VARCHAR(255) DEFAULT NULL,
        new_filename VARCHAR(255) DEFAULT NULL,
        file_path VARCHAR(500) DEFAULT NULL,
        operation_details TEXT DEFAULT NULL,
        user_id VARCHAR(100) DEFAULT 'admin',
        ip_address VARCHAR(45) DEFAULT NULL,
        user_agent TEXT DEFAULT NULL,
        operation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('success', 'failed', 'pending') DEFAULT 'pending',
        error_message TEXT DEFAULT NULL,
        INDEX idx_operation_type (operation_type),
        INDEX idx_operation_date (operation_date),
        INDEX idx_status (status)
    )";
    
    $pdo->exec($sql2);
    echo "✅ Created gallery_operations table\n";
    
    // Insert existing gallery images
    $insertSql = "INSERT IGNORE INTO gallery_images (filename, original_name, file_path, status) VALUES
        ('galerie1.jpg', 'galerie1.jpg', 'images/gallery/galerie1.jpg', 'active'),
        ('galerie2.jpg', 'galerie2.jpg', 'images/gallery/galerie2.jpg', 'active'),
        ('galerie3.jpg', 'galerie3.jpg', 'images/gallery/galerie3.jpg', 'active'),
        ('galerie4.jpg', 'galerie4.jpg', 'images/gallery/galerie4.jpg', 'active'),
        ('galerie5.jpg', 'galerie5.jpg', 'images/gallery/galerie5.jpg', 'active'),
        ('galerie6.jpg', 'galerie6.jpg', 'images/gallery/galerie6.jpg', 'active'),
        ('galerie7.jpg', 'galerie7.jpg', 'images/gallery/galerie7.jpg', 'active'),
        ('galerie8.jpg', 'galerie8.jpg', 'images/gallery/galerie8.jpg', 'active')";
    
    $pdo->exec($insertSql);
    echo "✅ Inserted existing gallery images\n";
    
    echo "\n🎉 Gallery database setup completed successfully!\n";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
