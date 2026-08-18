<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
require_once __DIR__ . '/db_config.php';

$events = [];

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM events ORDER BY id ASC");
        $dbData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($dbData)) $events = $dbData;
    } catch (Throwable $e) {}
}

if (empty($events)) {
    $eventsFile = __DIR__ . '/data/events.json';
    if (file_exists($eventsFile)) {
        $events = json_decode(file_get_contents($eventsFile), true) ?: [];
    }
}

echo json_encode($events);
exit;
?>