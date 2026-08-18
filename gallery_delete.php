<?php
session_start();
require_once __DIR__ . '/db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php?error=forbidden#gallery-management');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_gallery_images'])) {
    $deleteImages = (array)$_POST['delete_gallery_images'];
    $galleryDir = __DIR__ . '/images/gallery/';
    $deletedCount = 0;

    foreach ($deleteImages as $image) {
        $cleanImage = basename($image);
        $filePath = $galleryDir . $cleanImage;
        if (file_exists($filePath)) {
            if (@unlink($filePath)) {
                $deletedCount++;
            }
        }
    }

    logActivity($_SESSION['username'] ?? 'Admin', 'Smazány fotky z galerie', 'Galerie', "Smazáno $deletedCount fotek", $pdo);
    header('Location: dashboard.php?success=gallery_deleted#gallery-management');
    exit;
} else {
    header('Location: dashboard.php#gallery-management');
    exit;
}
?>