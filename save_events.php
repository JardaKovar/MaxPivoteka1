<?php
session_start();
require_once __DIR__ . '/db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php?error=forbidden#edit-events');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['events'])) {
    $events = $_POST['events'];
    
    $cleanEvents = [];
    foreach ($events as $event) {
        $date = trim($event['date'] ?? '');
        $title = trim($event['title'] ?? '');
        $description = trim($event['description'] ?? '');
        if (!empty($date) || !empty($title) || !empty($description)) {
            $cleanEvents[] = [
                'date' => $date,
                'title' => $title,
                'description' => $description
            ];
        }
    }

    // 1. Save to JSON
    $dataDir = __DIR__ . '/data';
    if (!file_exists($dataDir)) @mkdir($dataDir, 0777, true);
    @file_put_contents($dataDir . '/events.json', json_encode($cleanEvents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // 2. Sync to DB if connected
    if ($pdo) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                date VARCHAR(100),
                title VARCHAR(255),
                description TEXT
            )");
            $pdo->exec("TRUNCATE TABLE events");
            $stmt = $pdo->prepare("INSERT INTO events (date, title, description) VALUES (?, ?, ?)");
            foreach ($cleanEvents as $ev) {
                $stmt->execute([$ev['date'], $ev['title'], $ev['description']]);
            }
        } catch (Throwable $e) {
            error_log("Events DB sync error: " . $e->getMessage());
        }
    }

    logActivity($_SESSION['username'] ?? 'Admin', 'Uloženy akce', 'Akce', 'Aktualizován seznam plánovaných akcí (' . count($cleanEvents) . ' akcí)', $pdo);

    header('Location: dashboard.php?success=events_saved#edit-events');
    exit;
} else {
    header('Location: dashboard.php#edit-events');
    exit;
}
?>