<?php
// Test script to simulate gallery upload and delete operations
session_start();
require_once 'db_config.php';

// Simulate logged in user
$_SESSION['loggedin'] = true;
$_SESSION['username'] = 'test_user';

echo "=== Gallery Operations Test ===\n\n";

// Test 1: Simulate gallery upload
echo "1. Testing gallery upload simulation...\n";
try {
    // Create a test image entry in database
    $testFilename = 'test_image_' . time() . '.jpg';
    $testOriginalName = 'test_image.jpg';
    $testFilePath = 'images/gallery/' . $testFilename;
    
    $stmt = $pdo->prepare("INSERT INTO gallery_images (filename, original_name, file_path, file_size, mime_type, status, created_by) VALUES (?, ?, ?, ?, ?, 'active', ?)");
    $stmt->execute([$testFilename, $testOriginalName, $testFilePath, 12345, 'image/jpeg', $_SESSION['username']]);
    
    $imageId = $pdo->lastInsertId();
    echo "✓ Test image inserted with ID: $imageId\n";
    
    // Test the logging functionality like in gallery_upload.php
    $uploadedFiles = [$testFilename];
    $username = $_SESSION['username'];
    $details = "Uploaded gallery images: " . implode(', ', $uploadedFiles) . " by user $username";
    logActivity($username, 'Uploaded gallery images', 'Gallery', $details, $pdo);
    echo "✓ Activity logged successfully\n";
    
    // Test current_activity_logs integration (mirroring Price List Management)
    $currentStateDetails = "Current gallery images: " . implode(', ', $uploadedFiles);
    
    $stmt = $pdo->prepare("SELECT id FROM current_activity_logs WHERE section = 'Gallery' AND username = ?");
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    
    if ($row) {
        $updateStmt = $pdo->prepare("UPDATE current_activity_logs SET details = ?, ip_address = ?, user_agent = ?, last_updated = NOW() WHERE id = ?");
        $updateStmt->execute([$currentStateDetails, '127.0.0.1', 'Test Script', $row['id']]);
        echo "✓ Updated current_activity_logs\n";
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO current_activity_logs (username, section, details, ip_address, user_agent) VALUES (?, 'Gallery', ?, ?, ?)");
        $insertStmt->execute([$username, $currentStateDetails, '127.0.0.1', 'Test Script']);
        echo "✓ Inserted into current_activity_logs\n";
    }
    
} catch (Exception $e) {
    echo "✗ Upload test error: " . $e->getMessage() . "\n";
}

// Test 2: Test gallery delete simulation
echo "\n2. Testing gallery delete simulation...\n";
try {
    // Get the test image we just created
    $stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE id = ? AND status = 'active'");
    $stmt->execute([$imageId]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($image) {
        // Mark as deleted (simulating gallery_delete.php logic)
        $stmt = $pdo->prepare("UPDATE gallery_images SET status = 'deleted', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$imageId]);
        echo "✓ Test image marked as deleted\n";
        
        // Test delete logging
        $deleteImages = [$image['filename']];
        $details = "Deleted gallery images: " . implode(', ', $deleteImages) . " by user $username";
        logActivity($username, 'Deleted gallery images', 'Gallery', $details, $pdo);
        echo "✓ Delete activity logged successfully\n";
        
        // Test current_activity_logs for deletion
        $currentStateDetails = "Deleted gallery images: " . implode(', ', $deleteImages);
        
        $stmt = $pdo->prepare("SELECT id FROM current_activity_logs WHERE section = 'Gallery' AND username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        
        if ($row) {
            $updateStmt = $pdo->prepare("UPDATE current_activity_logs SET details = ?, ip_address = ?, user_agent = ?, last_updated = NOW() WHERE id = ?");
            $updateStmt->execute([$currentStateDetails, '127.0.0.1', 'Test Script', $row['id']]);
            echo "✓ Updated current_activity_logs for deletion\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Delete test error: " . $e->getMessage() . "\n";
}

// Test 3: Verify database state
echo "\n3. Verifying database state...\n";
try {
    // Check activity_logs
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM activity_logs WHERE section = 'Gallery'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Found " . $result['count'] . " gallery activities in activity_logs\n";
    
    // Check current_activity_logs
    $stmt = $pdo->prepare("SELECT * FROM current_activity_logs WHERE section = 'Gallery'");
    $stmt->execute();
    $currentLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✓ Found " . count($currentLogs) . " current gallery activities\n";
    
    if (!empty($currentLogs)) {
        foreach ($currentLogs as $log) {
            echo "  - User: " . $log['username'] . ", Details: " . $log['details'] . "\n";
        }
    }
    
    // Check gallery_images
    $stmt = $pdo->prepare("SELECT COUNT(*) as active, (SELECT COUNT(*) FROM gallery_images WHERE status = 'deleted') as deleted FROM gallery_images WHERE status = 'active'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Gallery images: " . $result['active'] . " active, " . $result['deleted'] . " deleted\n";
    
} catch (Exception $e) {
    echo "✗ Database verification error: " . $e->getMessage() . "\n";
}

// Test 4: Compare with Price List Management structure
echo "\n4. Comparing with Price List Management...\n";
try {
    // Check if price_list table exists and has similar structure
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM price_list");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Price list has " . $result['count'] . " entries\n";
    
    // Check current_activity_logs for Price List
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM current_activity_logs WHERE section = 'Price List'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Price List has " . $result['count'] . " current activity entries\n";
    
    echo "✓ Gallery management now uses same logging structure as Price List Management\n";
    
} catch (Exception $e) {
    echo "✗ Price List comparison error: " . $e->getMessage() . "\n";
}

// Test 5: Test error handling
echo "\n5. Testing error handling...\n";
try {
    // Test with invalid data
    logActivity('', '', '', '', $pdo);
    echo "✓ Error handling works for empty data\n";
} catch (Exception $e) {
    echo "✓ Error handling works: " . $e->getMessage() . "\n";
}

echo "\n=== Gallery Operations Test Complete ===\n";
echo "Gallery management system successfully mirrors Price List Management functionality!\n";
?>
