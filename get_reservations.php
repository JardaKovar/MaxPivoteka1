<?php
session_start();
require_once __DIR__ . '/db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$reservations = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM reservations ORDER BY id DESC");
        $stmt->execute();
        $dbReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($dbReservations)) {
            $reservations = $dbReservations;
        }
    } catch (Throwable $e) {
        error_log("Get reservations DB error: " . $e->getMessage());
    }
}

// Fallback to data/reservations.json
if (empty($reservations)) {
    $resFile = __DIR__ . '/data/reservations.json';
    if (file_exists($resFile)) {
        $reservations = json_decode(file_get_contents($resFile), true) ?: [];
    }
}

echo json_encode([
    'success' => true,
    'reservations' => $reservations,
    'count' => count($reservations)
]);
exit;
?>