<?php
session_start();
require_once __DIR__ . '/db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php?error=forbidden#edit-rental-list');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rentallist'])) {
    $rentallist = $_POST['rentallist'];
    
    $cleanRental = [];
    foreach ($rentallist as $index => $rental) {
        $cleanRental[] = [
            'number' => (int)($rental['number'] ?? ($index + 1)),
            'desc1' => trim($rental['desc1'] ?? ''),
            'image' => trim($rental['image'] ?? ''),
            'desc2' => trim($rental['desc2'] ?? ''),
            'deposit' => trim($rental['deposit'] ?? ''),
            'day' => trim($rental['day'] ?? ''),
            'weekend' => trim($rental['weekend'] ?? ''),
            'week' => trim($rental['week'] ?? ''),
            'month' => trim($rental['month'] ?? '')
        ];
    }

    // 1. Save to JSON
    $dataDir = __DIR__ . '/data';
    if (!file_exists($dataDir)) @mkdir($dataDir, 0777, true);
    @file_put_contents($dataDir . '/rentallist.json', json_encode($cleanRental, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // 2. Sync to DB if connected
    if ($pdo) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS rentallist (
                id INT AUTO_INCREMENT PRIMARY KEY,
                number INT,
                desc1 VARCHAR(255),
                image VARCHAR(255),
                desc2 VARCHAR(255),
                deposit VARCHAR(50),
                day VARCHAR(50),
                weekend VARCHAR(50),
                week VARCHAR(50),
                month VARCHAR(50)
            )");
            $pdo->exec("TRUNCATE TABLE rentallist");
            $stmt = $pdo->prepare("INSERT INTO rentallist (number, desc1, image, desc2, deposit, day, weekend, week, month) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($cleanRental as $r) {
                $stmt->execute([$r['number'], $r['desc1'], $r['image'], $r['desc2'], $r['deposit'], $r['day'], $r['weekend'], $r['week'], $r['month']]);
            }
        } catch (Throwable $e) {
            error_log("Rental list DB sync error: " . $e->getMessage());
        }
    }

    logActivity($_SESSION['username'] ?? 'Admin', 'Uložena půjčovna', 'Půjčovna', 'Aktualizován ceník půjčovny', $pdo);

    header('Location: dashboard.php?success=rentallist_saved#edit-rental-list');
    exit;
} else {
    header('Location: dashboard.php#edit-rental-list');
    exit;
}
?>