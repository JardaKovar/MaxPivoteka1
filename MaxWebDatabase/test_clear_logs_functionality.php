<?php
// Test script for activity log clearing functionality
session_start();
require_once 'db_config.php';

echo "<h1>Activity Log Clearing Functionality Test</h1>\n";
echo "<pre>\n";

// Simulate logged in user for testing
$_SESSION['loggedin'] = true;
$_SESSION['username'] = 'TestUser';

echo "=== TESTING ACTIVITY LOG CLEARING FUNCTIONALITY ===\n\n";

// Test 1: Check if database connection works
echo "1. Testing database connection...\n";
if ($pdo) {
    echo "   ✓ Database connection successful\n";
} else {
    echo "   ✗ Database connection failed\n";
}

// Test 2: Create test log entries
echo "\n2. Creating test log entries...\n";
try {
    if ($pdo) {
        // Insert test data into activity_logs
        $stmt = $pdo->prepare("INSERT INTO activity_logs (username, action, section, details, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['TestUser', 'Test Action 1', 'Test Section', 'Test details 1', '127.0.0.1']);
        $stmt->execute(['TestUser', 'Test Action 2', 'Test Section', 'Test details 2', '127.0.0.1']);
        
        // Insert test data into login_sessions
        $stmt = $pdo->prepare("INSERT INTO login_sessions (username, action, ip_address, session_id) VALUES (?, ?, ?, ?)");
        $stmt->execute(['TestUser', 'login', '127.0.0.1', 'test_session_1']);
        $stmt->execute(['TestUser', 'logout', '127.0.0.1', 'test_session_2']);
        
        // Try to insert into current_activity_logs if table exists
        try {
            $stmt = $pdo->prepare("INSERT INTO current_activity_logs (username, section, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->execute(['TestUser', 'Test Section', 'Current test details', '127.0.0.1']);
            echo "   ✓ Test data inserted into all tables\n";
        } catch (PDOException $e) {
            echo "   ⚠ current_activity_logs table might not exist (this is OK)\n";
        }
    }
    
    // Create test log files
    file_put_contents('activity.log', "2024-01-01 12:00:00 - TestUser - Test Action - Test Section - Test details\n", FILE_APPEND);
    file_put_contents('sessions.log', "2024-01-01 12:00:00 - TestUser - login - test_session\n", FILE_APPEND);
    echo "   ✓ Test log files created\n";
    
} catch (Exception $e) {
    echo "   ✗ Error creating test data: " . $e->getMessage() . "\n";
}

// Test 3: Check log counts before clearing
echo "\n3. Checking log counts before clearing...\n";
try {
    if ($pdo) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM activity_logs");
        $activityCount = $stmt->fetch()['count'];
        echo "   Activity logs: $activityCount\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM login_sessions");
        $sessionCount = $stmt->fetch()['count'];
        echo "   Session logs: $sessionCount\n";
        
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM current_activity_logs");
            $currentCount = $stmt->fetch()['count'];
            echo "   Current logs: $currentCount\n";
        } catch (PDOException $e) {
            echo "   Current logs: table not found (OK)\n";
        }
    }
    
    echo "   Activity log file exists: " . (file_exists('activity.log') ? 'Yes' : 'No') . "\n";
    echo "   Sessions log file exists: " . (file_exists('sessions.log') ? 'Yes' : 'No') . "\n";
    
} catch (Exception $e) {
    echo "   ✗ Error checking log counts: " . $e->getMessage() . "\n";
}

// Test 4: Test the clear functionality
echo "\n4. Testing clear functionality...\n";
try {
    // Simulate POST request to clear_activity_logs.php
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    // Capture output from clear script
    ob_start();
    include 'clear_activity_logs.php';
    $clearOutput = ob_get_clean();
    
    $clearResult = json_decode($clearOutput, true);
    
    if ($clearResult && $clearResult['success']) {
        echo "   ✓ Clear operation successful\n";
        if (isset($clearResult['details'])) {
            echo "   Details: " . json_encode($clearResult['details'], JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "   ✗ Clear operation failed\n";
        if (isset($clearResult['error'])) {
            echo "   Error: " . $clearResult['error'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ✗ Error testing clear functionality: " . $e->getMessage() . "\n";
}

// Test 5: Verify logs are cleared
echo "\n5. Verifying logs are cleared...\n";
try {
    if ($pdo) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM activity_logs");
        $activityCount = $stmt->fetch()['count'];
        echo "   Activity logs after clear: $activityCount " . ($activityCount == 0 ? "✓" : "✗") . "\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM login_sessions");
        $sessionCount = $stmt->fetch()['count'];
        echo "   Session logs after clear: $sessionCount " . ($sessionCount == 0 ? "✓" : "✗") . "\n";
        
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM current_activity_logs");
            $currentCount = $stmt->fetch()['count'];
            echo "   Current logs after clear: $currentCount " . ($currentCount == 0 ? "✓" : "✗") . "\n";
        } catch (PDOException $e) {
            echo "   Current logs: table not found (OK)\n";
        }
    }
    
    echo "   Activity log file exists after clear: " . (file_exists('activity.log') ? 'Yes ✗' : 'No ✓') . "\n";
    echo "   Sessions log file exists after clear: " . (file_exists('sessions.log') ? 'Yes ✗' : 'No ✓') . "\n";
    
} catch (Exception $e) {
    echo "   ✗ Error verifying cleared logs: " . $e->getMessage() . "\n";
}

// Test 6: Test authentication requirement
echo "\n6. Testing authentication requirement...\n";
try {
    // Simulate unauthenticated request
    unset($_SESSION['loggedin']);
    
    ob_start();
    include 'clear_activity_logs.php';
    $unauthOutput = ob_get_clean();
    
    $unauthResult = json_decode($unauthOutput, true);
    
    if ($unauthResult && isset($unauthResult['error']) && $unauthResult['error'] === 'Unauthorized') {
        echo "   ✓ Authentication requirement working correctly\n";
    } else {
        echo "   ✗ Authentication requirement not working\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ Error testing authentication: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "</pre>\n";
?>
