<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php#gallery-management?error=forbidden');
    exit;
}

$uploadDir = __DIR__ . '/images/gallery/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['gallery_files'])) {
    $files = $_FILES['gallery_files'];
    $count = count($files['name']);
    $errors = [];
    $uploadedFiles = [];

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $files['tmp_name'][$i];
            $name = basename($files['name'][$i]);
            $targetFile = $uploadDir . $name;

            if (move_uploaded_file($tmpName, $targetFile)) {
                $uploadedFiles[] = $name;
            } else {
                $errors[] = "Failed to upload file: $name";
            }
        } else {
            $errors[] = "Error uploading file: " . $files['name'][$i];
        }
    }

    // Log the activity (mirroring Price List Management logging)
    $username = $_SESSION['username'] ?? 'Unknown';
    if (!empty($uploadedFiles)) {
        $details = "Uploaded gallery images: " . implode(', ', $uploadedFiles) . " by user $username";
        logActivity($username, 'Uploaded gallery images', 'Gallery', $details, $pdo);

        // Prepare current state details string for current_activity_logs (mirroring Price List Management)
        $currentStateDetails = "Current gallery images: " . implode(', ', $uploadedFiles);

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
        header('Location: dashboard.php#gallery-management?success=gallery_uploaded');
        exit;
    } else {
        header('Location: dashboard.php#gallery-management?error=upload_failed');
        exit;
    }
} else {
    header('Location: dashboard.php#gallery-management?error=invalid_request');
    exit;
}
