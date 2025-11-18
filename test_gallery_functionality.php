<?php
// Test script to verify gallery functionality
require_once 'db_config.php';

echo "=== Gallery Functionality Test ===\n\n";

// Test 1: Check if database connection works
echo "1. Testing database connection...\n";
try {
    if ($pdo) {
        echo "✓ Database connection successful\n";
    } else {
        echo "✗ Database connection failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Database connection error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check if gallery_images table exists
echo "\n2. Testing gallery_images table...\n";
try {
    $stmt = $pdo->query("DESCRIBE gallery_images");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ gallery_images table exists with columns: " . implode(', ', $columns) . "\n";
} catch (Exception $e) {
    echo "✗ gallery_images table error: " . $e->getMessage() . "\n";
}

// Test 3: Check if gallery_operations table exists
echo "\n3. Testing gallery_operations table...\n";
try {
    $stmt = $pdo->query("DESCRIBE gallery_operations");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ gallery_operations table exists with columns: " . implode(', ', $columns) . "\n";
} catch (Exception $e) {
    echo "✗ gallery_operations table error: " . $e->getMessage() . "\n";
}

// Test 4: Check if current_activity_logs table exists
echo "\n4. Testing current_activity_logs table...\n";
try {
    $stmt = $pdo->query("DESCRIBE current_activity_logs");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ current_activity_logs table exists with columns: " . implode(', ', $columns) . "\n";
} catch (Exception $e) {
    echo "✗ current_activity_logs table error: " . $e->getMessage() . "\n";
}

// Test 5: Test logActivity function
echo "\n5. Testing logActivity function...\n";
try {
    logActivity('test_user', 'Test gallery functionality', 'Gallery', 'Testing gallery system', $pdo);
    echo "✓ logActivity function works\n";
} catch (Exception $e) {
    echo "✗ logActivity function error: " . $e->getMessage() . "\n";
}

// Test 6: Test gallery directory structure
echo "\n6. Testing gallery directory structure...\n";
$galleryDir = __DIR__ . '/images/gallery/';
if (!is_dir($galleryDir)) {
    if (mkdir($galleryDir, 0755, true)) {
        echo "✓ Created gallery directory: $galleryDir\n";
    } else {
        echo "✗ Failed to create gallery directory: $galleryDir\n";
    }
} else {
    echo "✓ Gallery directory exists: $galleryDir\n";
}

// Test 7: Check existing gallery images
echo "\n7. Checking existing gallery images...\n";
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM gallery_images WHERE status = 'active'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Found " . $result['count'] . " active gallery images in database\n";
    
    // List actual files in directory
    $files = glob($galleryDir . '*');
    echo "✓ Found " . count($files) . " files in gallery directory\n";
} catch (Exception $e) {
    echo "✗ Error checking gallery images: " . $e->getMessage() . "\n";
}

// Test 8: Test Price List Management comparison
echo "\n8. Testing Price List Management table...\n";
try {
    $stmt = $pdo->query("DESCRIBE price_list");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ price_list table exists with columns: " . implode(', ', $columns) . "\n";
} catch (Exception $e) {
    echo "✗ price_list table error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
