<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php#cenik-management?error=forbidden');
    exit;
}

$uploadDir = __DIR__ . '/images/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cenik_file']) && $_FILES['cenik_file']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['cenik_file']['tmp_name'];
    $name = basename($_FILES['cenik_file']['name']);
    $targetFile = $uploadDir . $name;

    if (move_uploaded_file($tmpName, $targetFile)) {
        // Update database using stored procedure
        try {
            $stmt = $pdo->prepare("CALL upsert_price_list_image(?)");
            $stmt->execute([$name]);
        } catch (PDOException $e) {
            error_log("Failed to update price list in database: " . $e->getMessage());
        }

        // Log the activity
        $username = $_SESSION['username'] ?? 'Unknown';
        $details = "Uploaded price list image: $name by user $username";
        logActivity($username, 'Uploaded price list image', 'Price List', $details, $pdo);

        // Update local JSON file with new price list image filename (keep as backup)
        $jsonFile = __DIR__ . '/data/cenik_latest.json';
        $jsonData = json_decode(file_get_contents($jsonFile), true);
        $jsonData['latestImage'] = $name;
        file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));

        header('Location: dashboard.php#cenik-management?success=cenik_uploaded');
        exit;
    } else {
        header('Location: dashboard.php#cenik-management?error=upload_failed');
        exit;
    }
} else {
    header('Location: dashboard.php#cenik-management?error=invalid_request');
    exit;
}
