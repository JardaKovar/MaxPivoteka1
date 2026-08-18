<?php
session_start();
require_once __DIR__ . '/db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['id'])) {
        echo json_encode(['success' => false, 'error' => 'Missing ID']);
        exit;
    }
    
    $reservationId = $input['id'];
    
    // Delete from data/reservations.json
    $resFile = __DIR__ . '/data/reservations.json';
    if (file_exists($resFile)) {
        $reservations = json_decode(file_get_contents($resFile), true) ?: [];
        $reservations = array_values(array_filter($reservations, function($r) use ($reservationId) {
            return (string)$r['id'] !== (string)$reservationId;
        }));
        file_put_contents($resFile, json_encode($reservations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    // Delete from database if connected
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
            $stmt->execute([$reservationId]);
        } catch (Throwable $e) {}
    }
    
    logActivity($_SESSION['username'] ?? 'User', 'Smazání rezervace', 'Rezervace', "Smazána rezervace #{$reservationId}", $pdo);
    
    echo json_encode(['success' => true, 'message' => 'Reservation deleted']);
    exit;
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
?>