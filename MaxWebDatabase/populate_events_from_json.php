<?php
require_once 'db_config.php';

// Read events from JSON file
$eventsDataFile = __DIR__ . '/data/events.json';
if (file_exists($eventsDataFile)) {
    $events = json_decode(file_get_contents($eventsDataFile), true);
    
    if ($events && is_array($events)) {
        // Connect to database
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Clear existing events
        $conn->query("TRUNCATE TABLE events");
        
        // Insert events from JSON
        $stmt = $conn->prepare("INSERT INTO events (date, title, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $date, $title, $description);
        
        foreach ($events as $event) {
            $date = $event['date'];
            $title = $event['title'];
            $description = $event['description'];
            $stmt->execute();
            echo "Inserted event: " . $title . "\n";
        }
        
        $stmt->close();
        $conn->close();
        
        echo "Successfully populated events table from JSON file.\n";
    } else {
        echo "No valid events found in JSON file.\n";
    }
} else {
    echo "Events JSON file not found.\n";
}
?>
