<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php#edit-tap-list?error=forbidden');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['taplist'])) {
    $taplist = $_POST['taplist'];

    try {
        // Save to database
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error);
        }

        // Clear existing tap list data
        $conn->query("TRUNCATE TABLE taplist");

        // Insert new tap list data
        $stmt = $conn->prepare("INSERT INTO taplist (number, brewery, beer, alc, epm, price_05l) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $number, $brewery, $beer, $alc, $epm, $price_05l);

        foreach ($taplist as $tap) {
            $number = $tap['number'];
            $brewery = $tap['brewery'];
            $beer = $tap['beer'];
            $alc = $tap['alc'];
            $epm = $tap['epm'];
            $price_05l = $tap['price_05l'];
            $stmt->execute();
        }

        $stmt->close();
        $conn->close();

        // Also save to JSON file as backup
        $tapDataFile = __DIR__ . '/data/taplist.json';
        
        // Create data directory if not exists
        if (!file_exists(__DIR__ . '/data')) {
            mkdir(__DIR__ . '/data', 0755, true);
        }
        
        file_put_contents($tapDataFile, json_encode($taplist, JSON_PRETTY_PRINT));

        // Log the activity
        if (isset($pdo)) {
            logActivity($_SESSION['username'], 'Updated', 'Tap List', 'Tap list data updated successfully', $pdo);
        }

        header('Location: dashboard.php#edit-tap-list?success=taplist_saved');
        exit;

    } catch (Exception $e) {
        error_log("Error saving tap list: " . $e->getMessage());
        header('Location: dashboard.php#edit-tap-list?error=save_failed');
        exit;
    }
}
?>
