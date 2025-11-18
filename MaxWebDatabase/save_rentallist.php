<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php#edit-rental-list?error=forbidden');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rentallist'])) {
    $rentallist = $_POST['rentallist'];

    try {
        // Save to database
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error);
        }

        // Clear existing rental list data
        $conn->query("TRUNCATE TABLE rentallist");

        // Insert new rental list data
        $stmt = $conn->prepare("INSERT INTO rentallist (number, desc1, image, desc2, deposit, day, weekend, week, month) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssss", $number, $desc1, $image, $desc2, $deposit, $day, $weekend, $week, $month);

        foreach ($rentallist as $rental) {
            $number = $rental['number'];
            $desc1 = $rental['desc1'];
            // Keep just the filename, the frontend will handle the path
            $image = $rental['image'];
            $desc2 = $rental['desc2'];
            $deposit = $rental['deposit'];
            $day = $rental['day'];
            $weekend = $rental['weekend'];
            $week = $rental['week'];
            $month = $rental['month'];
            $stmt->execute();
        }

        $stmt->close();
        $conn->close();

        // Also save to JSON file as backup
        $rentalDataFile = __DIR__ . '/data/rentallist.json';
        
        // Create data directory if not exists
        if (!file_exists(__DIR__ . '/data')) {
            mkdir(__DIR__ . '/data', 0755, true);
        }
        
        file_put_contents($rentalDataFile, json_encode($rentallist, JSON_PRETTY_PRINT));

        // Log the activity
        if (isset($pdo)) {
            logActivity($_SESSION['username'], 'Updated', 'Rental List', 'Rental list data updated successfully', $pdo);
        }

        header('Location: dashboard.php#edit-rental-list?success=rentallist_saved');
        exit;

    } catch (Exception $e) {
        error_log("Error saving rental list: " . $e->getMessage());
        header('Location: dashboard.php#edit-rental-list?error=save_failed');
        exit;
    }
}
?>
