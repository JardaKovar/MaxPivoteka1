<?php
require_once 'db_config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$result = $conn->query("SELECT * FROM events ORDER BY id ASC");
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($events);
?>
