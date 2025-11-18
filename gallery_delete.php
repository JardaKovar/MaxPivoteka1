<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php#gallery-management?error=forbidden');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_gallery_images'])) {
    $deleteImages = $_POST['delete_gallery_images'];
    $galleryDir = __DIR__ . '/images/gallery/';
    $errors = [];

    foreach ($deleteImages as $image) {
        $filePath = $galleryDir . basename($image);
        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                $errors[] = "Failed to delete: " . basename($image);
            }
        }
    }

    // Log the activity (mirroring Price List Management logging)
    $username = $_SESSION['username'] ?? 'Unknown';
    if (!empty($deleteImages)) {
        $details = "Deleted gallery images: " . implode(', ', $deleteImages) . " by user $username";
        logActivity($username, 'Deleted gallery images', 'Gallery', $details, $pdo);

        // Prepare current state details string for current_activity_logs (mirroring Price List Management)
        $currentStateDetails = "Deleted gallery images: " . implode(', ', $deleteImages);

        // Upsert current state into current_activity_logs table
        try {
            $stmt = $pdo->prepare("SELECT id FROM current_activity_logs WHERE section = 'Gallery' AND username = ?");
            $stmt->execute([$username]);
            $row = $stmt->fetch();

            if ($row) {
                $updateStmt = $pdo->prepare("UPDATE current_activity_logs SET details = ?, ip_address = ?, user_agent = ?, last_updated = NOW() WHERE id = ?");
                $updateStmt->execute([$currentStateDetails, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '', $row['id']]);
            } else {
                $insertStmt = $pdo->prepare("INSERT INTO current_activity_logs (username, section, details, ip_address, user_agent) VALUES (?, 'Gallery', ?, ?, ?)");
                $insertStmt->execute([$username, $currentStateDetails, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
            }
        } catch (Exception $e) {
            // Log error but do not block user
            error_log("Failed to update current_activity_logs for Gallery: " . $e->getMessage());
        }
    }

    if (empty($errors)) {
        header('Location: dashboard.php#gallery-management?success=gallery_deleted');
        exit;
    } else {
        header('Location: dashboard.php#gallery-management?error=delete_failed');
        exit;
    }
} else {
    header('Location: dashboard.php#gallery-management?error=invalid_request');
    exit;
}
