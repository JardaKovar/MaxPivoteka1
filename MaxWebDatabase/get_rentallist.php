<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    // Try to get rental list from database first
    if ($pdo) {
        $stmt = $pdo->query("SELECT * FROM rentallist ORDER BY number ASC");
        $rentalList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($rentalList)) {
            echo json_encode($rentalList);
            exit;
        }
    }
    
    // Fallback to JSON file if database is empty or unavailable
    $rentalDataFile = __DIR__ . '/data/rentallist.json';
    if (file_exists($rentalDataFile)) {
        $rentalData = json_decode(file_get_contents($rentalDataFile), true);
        if ($rentalData && is_array($rentalData)) {
            echo json_encode($rentalData);
            exit;
        }
    }
    
    // If no data available, return empty array
    echo json_encode([]);
    
} catch (Exception $e) {
    error_log("Error in get_rentallist.php: " . $e->getMessage());
    echo json_encode([]);
}
?>
