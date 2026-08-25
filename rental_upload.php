<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php#edit-rental-list?error=forbidden');
    exit;
}

$uploadDir = __DIR__ . '/images/rental/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['rental_files'])) {
    $files = $_FILES['rental_files'];
    $count = is_array($files['name']) ? count($files['name']) : 0;
    $errors = [];
    $uploadedFiles = [];

    for ($i = 0; $i < $count; $i++) {
        if (!empty($files['name'][$i]) && $files['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $files['tmp_name'][$i];
            $originalName = basename($files['name'][$i]);
            
            // Clean filename
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (!in_array($extension, $allowedTypes)) {
                $errors[] = "Soubor $originalName má nepodporovaný formát. Povoleno: JPG, PNG, GIF, WEBP.";
                continue;
            }

            if ($files['size'][$i] > 10 * 1024 * 1024) {
                $errors[] = "Soubor $originalName je příliš velký. Max 10MB.";
                continue;
            }

            $targetFile = $uploadDir . $originalName;
            if (move_uploaded_file($tmpName, $targetFile)) {
                chmod($targetFile, 0644);
                $uploadedFiles[] = $originalName;
            } else {
                $errors[] = "Nepodařilo se uložit soubor: $originalName";
            }
        }
    }

    // Failsafe activity log
    $username = $_SESSION['username'] ?? 'Admin';
    if (!empty($uploadedFiles)) {
        $details = "Nahrány obrázky půjčovny: " . implode(', ', $uploadedFiles) . " uživatelem $username";
        if (function_exists('logActivity')) {
            try {
                logActivity($username, 'Nahrány obrázky půjčovny', 'Půjčovna', $details, $pdo ?? null);
            } catch (Throwable $t) {}
        }
    }

    if (empty($errors)) {
        header('Location: dashboard.php?success=rental_images_uploaded#edit-rental-list');
        exit;
    } else {
        $errorMsg = implode('; ', $errors);
        header('Location: dashboard.php?error=upload_failed&msg=' . urlencode($errorMsg) . '#edit-rental-list');
        exit;
    }
} else {
    header('Location: dashboard.php?error=invalid_request#edit-rental-list');
    exit;
}
