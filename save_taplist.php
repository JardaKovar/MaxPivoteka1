<?php
session_start();
require_once __DIR__ . '/db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php?error=forbidden#edit-tap-list');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['taplist'])) {
    $taplist = $_POST['taplist'];
    
    // Clean & format tap list
    $cleanTaplist = [];
    foreach ($taplist as $index => $tap) {
        $cleanTaplist[] = [
            'number' => (int)($tap['number'] ?? ($index + 1)),
            'brewery' => trim($tap['brewery'] ?? ''),
            'beer' => trim($tap['beer'] ?? ''),
            'alc' => trim($tap['alc'] ?? ''),
            'epm' => trim($tap['epm'] ?? ''),
            'price_05l' => trim($tap['price_05l'] ?? '')
        ];
    }

    // 1. Save to JSON file as primary fast store
    $dataDir = __DIR__ . '/data';
    if (!file_exists($dataDir)) @mkdir($dataDir, 0777, true);
    @file_put_contents($dataDir . '/taplist.json', json_encode($cleanTaplist, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // 2. Sync to Database if connected
    if ($pdo) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS taplist (
                id INT AUTO_INCREMENT PRIMARY KEY,
                number INT,
                brewery VARCHAR(255),
                beer VARCHAR(255),
                alc VARCHAR(50),
                epm VARCHAR(50),
                price_05l VARCHAR(50)
            )");
            $pdo->exec("TRUNCATE TABLE taplist");
            $stmt = $pdo->prepare("INSERT INTO taplist (number, brewery, beer, alc, epm, price_05l) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($cleanTaplist as $tap) {
                $stmt->execute([$tap['number'], $tap['brewery'], $tap['beer'], $tap['alc'], $tap['epm'], $tap['price_05l']]);
            }
        } catch (Throwable $e) {
            error_log("Taplist DB sync error: " . $e->getMessage());
        }
    }

    logActivity($_SESSION['username'] ?? 'Admin', 'Uloženo pivo na čepu', 'Právě na čepu', 'Aktualizován seznam piv na čepu', $pdo);

    header('Location: dashboard.php?success=taplist_saved#edit-tap-list');
    exit;
} else {
    header('Location: dashboard.php#edit-tap-list');
    exit;
}
?>