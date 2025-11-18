<?php
echo "=== Debug Events Script ===\n";

// Test database connection
require_once 'db_config.php';

echo "1. Testing database connection...\n";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo "ERROR: Database connection failed: " . $conn->connect_error . "\n";
    exit;
} else {
    echo "SUCCESS: Database connected\n";
}

echo "2. Checking if events table exists...\n";
$result = $conn->query("SHOW TABLES LIKE 'events'");
if ($result->num_rows > 0) {
    echo "SUCCESS: Events table exists\n";
} else {
    echo "ERROR: Events table does not exist\n";
    exit;
}

echo "3. Checking events table structure...\n";
$result = $conn->query("DESCRIBE events");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Column: " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "ERROR: Could not describe events table\n";
}

echo "4. Counting events in table...\n";
$result = $conn->query("SELECT COUNT(*) as count FROM events");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Events count: " . $row['count'] . "\n";
} else {
    echo "ERROR: Could not count events\n";
}

echo "5. Fetching all events...\n";
$result = $conn->query("SELECT * FROM events ORDER BY id ASC");
if ($result) {
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
        echo "Event ID: " . $row['id'] . ", Date: " . $row['date'] . ", Title: " . $row['title'] . "\n";
    }
    
    echo "6. Testing JSON output...\n";
    echo "JSON: " . json_encode($events) . "\n";
} else {
    echo "ERROR: Could not fetch events\n";
}

$conn->close();
echo "=== Debug Complete ===\n";
?>
