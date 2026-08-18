<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
require_once __DIR__ . '/db_config.php';

$taplist = [];

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM taplist ORDER BY number ASC");
        $dbData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($dbData)) $taplist = $dbData;
    } catch (Throwable $e) {}
}

if (empty($taplist)) {
    $tapFile = __DIR__ . '/data/taplist.json';
    if (file_exists($tapFile)) {
        $taplist = json_decode(file_get_contents($tapFile), true) ?: [];
    }
}

echo json_encode($taplist);
exit;
?>