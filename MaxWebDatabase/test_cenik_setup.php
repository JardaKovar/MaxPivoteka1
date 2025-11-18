<?php
// Test script to trigger database recreation
require_once 'db_config.php';

echo "Testing Price Management Database Setup...\n";

if ($pdo) {
    try {
        // Check if the table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'price_list'");
        $tableExists = $stmt->rowCount() > 0;
        
        if ($tableExists) {
            echo "✅ price_list table exists\n";
        } else {
            echo "❌ price_list table does not exist\n";
        }
        
        // Check if the stored procedure exists
        $stmt = $pdo->query("SHOW PROCEDURE STATUS WHERE Name = 'upsert_price_list_image'");
        $procedureExists = $stmt->rowCount() > 0;
        
        if ($procedureExists) {
            echo "✅ upsert_price_list_image procedure exists\n";
        } else {
            echo "❌ upsert_price_list_image procedure does not exist\n";
        }
        
        // Test the stored procedure
        if ($procedureExists) {
            $stmt = $pdo->prepare("CALL upsert_price_list_image(?)");
            $stmt->execute(['test_image.jpg']);
            echo "✅ Stored procedure executed successfully\n";
            
            // Check if data was inserted
            $stmt = $pdo->query("SELECT * FROM price_list WHERE id = 1");
            $result = $stmt->fetch();
            if ($result) {
                echo "✅ Data inserted: " . $result['image_filename'] . "\n";
            }
        }
        
    } catch (PDOException $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Database connection failed\n";
}

echo "\nPrice Management setup test completed.\n";
?>
