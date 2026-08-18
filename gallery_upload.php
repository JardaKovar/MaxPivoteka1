<?php
session_start();
require_once __DIR__ . '/db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php?error=forbidden#gallery-management');
    exit;
}

$uploadDir = __DIR__ . '/images/gallery/';
if (!file_exists($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['gallery_files'])) {
    $files = $_FILES['gallery_files'];
    $count = is_array($files['name']) ? count($files['name']) : 0;
    $uploadedFiles = [];

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $files['tmp_name'][$i];
            $origName = basename($files['name'][$i]);
            // Clean filename
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($origName, PATHINFO_FILENAME)) . '.' . $ext;
            $targetFile = $uploadDir . $cleanName;

            if (move_uploaded_file($tmpName, $targetFile)) {
                @chmod($targetFile, 0644);
                $uploadedFiles[] = $cleanName;
            }
        }
    }

    if (!empty($uploadedFiles)) {
        logActivity($_SESSION['username'] ?? 'Admin', 'Nahrány fotky do galerie', 'Galerie', 'Nahráno fotek: ' . count($uploadedFiles), $pdo);
        header('Location: dashboard.php?success=gallery_uploaded#gallery-management');
        exit;
    } else {
        header('Location: dashboard.php?error=upload_failed#gallery-management');
        exit;
    }
} else {
    header('Location: dashboard.php#gallery-management');
    exit;
}
?>