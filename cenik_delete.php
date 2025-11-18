<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php#cenik-management?error=forbidden');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cenik_images'])) {
    $deleteImages = $_POST['delete_cenik_images'];
    $cenikDir = __DIR__ . '/images/cenik/';
    $errors = [];

    foreach ($deleteImages as $image) {
        $filePath = $cenikDir . basename($image);
        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                $errors[] = "Failed to delete: " . basename($image);
            }
        }
    }

    // Log the activity
    $username = $_SESSION['username'] ?? 'Unknown';
    if (!empty($deleteImages)) {
        $details = "Deleted price list images: " . implode(', ', $deleteImages) . " by user $username";
        logActivity($username, 'Deleted price list images', 'Price List', $details, $pdo);
    }

    if (empty($errors)) {
        header('Location: dashboard.php#cenik-management?success=cenik_deleted');
        exit;
    } else {
        header('Location: dashboard.php#cenik-management?error=delete_failed');
        exit;
    }
} else {
    header('Location: dashboard.php#cenik-management?error=invalid_request');
    exit;
}
