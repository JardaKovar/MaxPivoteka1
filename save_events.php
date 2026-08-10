<?php
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['events'])) {
    $events = $_POST['events'];

    // Save to database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        header('Location: dashboard.php#edit-events?error=db_connection_failed');
        exit;
    }

    // Clear existing events data
    $conn->query("TRUNCATE TABLE events");

    // Insert new events data
    $stmt = $conn->prepare("INSERT INTO events (date, title, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $date, $title, $description);

    foreach ($events as $event) {
        $date = $event['date'];
        $title = $event['title'];
        $description = $event['description'];
        
        // Only insert events that have at least a title or description
        if (!empty($title) || !empty($description)) {
            $stmt->execute();
        }
    }

    $stmt->close();
    $conn->close();

    // Also save to JSON file for backward compatibility
    $eventsDataFile = __DIR__ . '/data/events.json';
    $filteredEvents = array_filter($events, function($event) {
        return !empty($event['title']) || !empty($event['description']);
    });
    
    file_put_contents($eventsDataFile, json_encode(array_values($filteredEvents), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Log the activity
    if ($pdo) {
        logActivity($_SESSION['username'] ?? 'Unknown', 'Updated', 'Events', 'Events data updated', $pdo);
    }

    header('Location: dashboard.php#edit-events?success=events_saved');
    exit;
}
?>
