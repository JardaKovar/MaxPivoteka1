<?php
require_once 'db_config.php';

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    $result = $conn->query("SELECT * FROM taplist ORDER BY number ASC");
    $taplist = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $taplist[] = $row;
        }
    }

    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($taplist);

} catch (Exception $e) {
    // If database fails, try to read from JSON file as fallback
    $tapDataFile = __DIR__ . '/data/taplist.json';
    
    if (file_exists($tapDataFile)) {
        $taplist = json_decode(file_get_contents($tapDataFile), true);
        header('Content-Type: application/json');
        echo json_encode($taplist ?: []);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Could not load tap list data']);
    }
    
    error_log("Error loading tap list: " . $e->getMessage());
}
?>
