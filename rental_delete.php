<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php#edit-rental-list?error=forbidden');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_rental_images'])) {
    $imagesToDelete = $_POST['delete_rental_images'];
    $rentalDir = __DIR__ . '/images/rental/';
    $deletedFiles = [];
    $errors = [];

    if (is_array($imagesToDelete)) {
        foreach ($imagesToDelete as $image) {
            $imagePath = $rentalDir . basename($image);
            if (file_exists($imagePath)) {
                if (unlink($imagePath)) {
                    $deletedFiles[] = $image;
                } else {
                    $errors[] = "Nepodařilo se smazat: $image";
                }
            }
        }
    }

    $username = $_SESSION['username'] ?? 'Admin';
    if (!empty($deletedFiles)) {
        $details = "Smazány obrázky půjčovny: " . implode(', ', $deletedFiles) . " uživatelem $username";
        if (function_exists('logActivity')) {
            try {
                logActivity($username, 'Smazány obrázky půjčovny', 'Půjčovna', $details, $pdo ?? null);
            } catch (Throwable $t) {}
        }
    }

    if (empty($errors)) {
        header('Location: dashboard.php?success=rental_images_deleted#edit-rental-list');
        exit;
    } else {
        $errorMsg = implode('; ', $errors);
        header('Location: dashboard.php?error=delete_failed&msg=' . urlencode($errorMsg) . '#edit-rental-list');
        exit;
    }
} else {
    header('Location: dashboard.php?error=invalid_request#edit-rental-list');
    exit;
}
