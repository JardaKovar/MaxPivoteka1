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
    if (!$input || !isset($input['id']) || !isset($input['status'])) {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit;
    }
    
    $reservationId = $input['id'];
    $status = $input['status'];
    
    // Update in data/reservations.json
    $resFile = __DIR__ . '/data/reservations.json';
    if (file_exists($resFile)) {
        $reservations = json_decode(file_get_contents($resFile), true) ?: [];
        foreach ($reservations as &$r) {
            if ((string)$r['id'] === (string)$reservationId) {
                $r['status'] = $status;
                $r['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        file_put_contents($resFile, json_encode($reservations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    // Update in database if connected
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE reservations SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$status, $reservationId]);
        } catch (Throwable $e) {}
    }
    
    logActivity($_SESSION['username'] ?? 'User', 'Změna stavu rezervace', 'Rezervace', "Rezervace #{$reservationId} změněna na {$status}", $pdo);
    
    echo json_encode(['success' => true, 'message' => 'Status updated']);
    exit;
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
?>