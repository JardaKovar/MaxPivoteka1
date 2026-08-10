<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataDir = __DIR__ . '/data';
    if (!file_exists($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    $dataFile = $dataDir . '/popup.json';
    
    // Load existing settings
    $currentSettings = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
    
    $active = isset($_POST['active']) && ($_POST['active'] === '1' || $_POST['active'] === 'on');
    $title = trim($_POST['title'] ?? '');
    $text = trim($_POST['text'] ?? '');
    $start_datetime = trim($_POST['start_datetime'] ?? '');
    $end_datetime = trim($_POST['end_datetime'] ?? '');
    $image = $currentSettings['image'] ?? '';
    
    // Check image removal
    if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
        if (!empty($image) && file_exists(__DIR__ . '/' . $image)) {
            @unlink(__DIR__ . '/' . $image);
        }
        $image = '';
    }
    
    // Handle image upload
    if (isset($_FILES['popup_image']) && $_FILES['popup_image']['error'] === UPLOAD_ERR_OK) {
        $uploadsDir = __DIR__ . '/uploads';
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
        
        $tmpName = $_FILES['popup_image']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['popup_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif'])) {
            $filename = 'popup_banner_' . time() . '.' . $ext;
            $destination = $uploadsDir . '/' . $filename;
            
            if (move_uploaded_file($tmpName, $destination)) {
                // Delete old image if exists
                if (!empty($image) && file_exists(__DIR__ . '/' . $image)) {
                    @unlink(__DIR__ . '/' . $image);
                }
                $image = 'uploads/' . $filename;
            }
        }
    }
    
    $popupData = [
        'active' => $active,
        'title' => $title,
        'text' => $text,
        'start_datetime' => $start_datetime,
        'end_datetime' => $end_datetime,
        'image' => $image,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($dataFile, json_encode($popupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    if (isset($pdo) && $pdo) {
        logActivity($_SESSION['username'] ?? 'User', 'Updated', 'Pop-up Oznámení', 'Aktualizováno pop-up okno', $pdo);
    }
    
    $referer = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
    if (strpos($referer, 'dashboard.html') !== false) {
        header('Location: dashboard.html#edit-popup?success=popup_saved');
    } else {
        header('Location: dashboard.php#edit-popup?success=popup_saved');
    }
    exit;
}
?>