<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php#cenik-management?error=forbidden');
    exit;
}

$uploadDir = __DIR__ . '/images/cenik/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['cenik_files'])) {
    $files = $_FILES['cenik_files'];
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

    // Update the database with the latest uploaded image filename (use the last uploaded file)
    if (!empty($uploadedFiles)) {
        $latestImage = end($uploadedFiles);
        try {
            $stmt = $pdo->prepare("UPDATE price_list SET image_filename = ? WHERE id = 1");
            $stmt->execute([$latestImage]);
        } catch (Exception $e) {
            $errors[] = "Failed to update database with latest image: " . $e->getMessage();
        }
    }

    // Log the activity
    $username = $_SESSION['username'] ?? 'Unknown';
    if (!empty($uploadedFiles)) {
        $details = "Uploaded price list images: " . implode(', ', $uploadedFiles) . " by user $username";
        logActivity($username, 'Uploaded price list images', 'Price List', $details, $pdo);

        // Prepare current state details string for current_activity_logs
        $currentStateDetails = "Current price list image: " . end($uploadedFiles);

        // Upsert current state into current_activity_logs table
        try {
            $stmt = $pdo->prepare("SELECT id FROM current_activity_logs WHERE section = 'Price List' AND username = ?");
            $stmt->execute([$username]);
            $row = $stmt->fetch();

            if ($row) {
                $updateStmt = $pdo->prepare("UPDATE current_activity_logs SET details = ?, ip_address = ?, user_agent = ?, last_updated = NOW() WHERE id = ?");
                $updateStmt->execute([$currentStateDetails, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '', $row['id']]);
            } else {
                $insertStmt = $pdo->prepare("INSERT INTO current_activity_logs (username, section, details, ip_address, user_agent) VALUES (?, 'Price List', ?, ?, ?)");
                $insertStmt->execute([$username, $currentStateDetails, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
            }
        } catch (Exception $e) {
            // Log error but do not block user
            error_log("Failed to update current_activity_logs for Price List: " . $e->getMessage());
        }
    }

    if (empty($errors)) {
        header('Location: dashboard.php#cenik-management?success=cenik_uploaded');
        exit;
    } else {
        header('Location: dashboard.php#cenik-management?error=upload_failed');
        exit;
    }
} else {
    header('Location: dashboard.php#cenik-management?error=invalid_request');
    exit;
}
