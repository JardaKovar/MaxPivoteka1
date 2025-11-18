<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $stmt = $pdo->prepare("SELECT image_filename FROM price_list WHERE id = 1 LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $latestImage = $row ? $row['image_filename'] : null;
    echo json_encode(['latestImage' => $latestImage]);
} catch (Exception $e) {
    echo json_encode(['latestImage' => null, 'error' => $e->getMessage()]);
}
?>
