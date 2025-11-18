<?php
require_once 'db_config.php';

echo "<h1>Testing Events Save Functionality</h1>\n";

// Test database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo "<p style='color: red;'>Database connection failed: " . $conn->connect_error . "</p>\n";
    exit;
}

echo "<p style='color: green;'>Database connection successful!</p>\n";

// Test data
$testEvents = [
    ['date' => '24.12.', 'title' => 'Vánoční pivní speciály - TEST UPDATE', 'description' => 'Test description 1'],
    ['date' => '31.12.', 'title' => 'Silvestrovská ochutnávka - TEST', 'description' => 'Test description 2'],
    ['date' => '15.1.', 'title' => 'Degustace řemeslných piv - TEST', 'description' => 'Test description 3']
];

// Clear existing events data
$result = $conn->query("TRUNCATE TABLE events");
if ($result) {
    echo "<p style='color: green;'>Events table cleared successfully!</p>\n";
} else {
    echo "<p style='color: red;'>Error clearing events table: " . $conn->error . "</p>\n";
}

// Insert new events data
$stmt = $conn->prepare("INSERT INTO events (date, title, description) VALUES (?, ?, ?)");
if (!$stmt) {
    echo "<p style='color: red;'>Prepare failed: " . $conn->error . "</p>\n";
    exit;
}

$stmt->bind_param("sss", $date, $title, $description);

$insertCount = 0;
foreach ($testEvents as $event) {
    $date = $event['date'];
    $title = $event['title'];
    $description = $event['description'];
    
    if (!empty($title) || !empty($description)) {
        if ($stmt->execute()) {
            $insertCount++;
            echo "<p style='color: green;'>Inserted event: {$title}</p>\n";
        } else {
            echo "<p style='color: red;'>Error inserting event: " . $stmt->error . "</p>\n";
        }
    }
}

$stmt->close();
$conn->close();

echo "<p><strong>Total events inserted: {$insertCount}</strong></p>\n";

// Test reading the data back
echo "<h2>Reading Events Back from Database:</h2>\n";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
$result = $conn->query("SELECT * FROM events");
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
    echo "<p>ID: {$row['id']}, Date: {$row['date']}, Title: {$row['title']}, Description: {$row['description']}</p>\n";
}
$conn->close();

echo "<h2>JSON Output (what get_events.php should return):</h2>\n";
echo "<pre>" . json_encode($events, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>\n";
?>
