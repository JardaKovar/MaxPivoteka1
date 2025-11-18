<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php#edit-rental-list?error=forbidden');
    exit;
}

$uploadDir = __DIR__ . '/images/rental/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['rental_files'])) {
    $files = $_FILES['rental_files'];
    $count = count($files['name']);
    $errors = [];
    $uploadedFiles = [];

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $files['tmp_name'][$i];
            $name = basename($files['name'][$i]);
            $targetFile = $uploadDir . $name;

            // Check if file is an image
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($imageFileType, $allowedTypes)) {
                $errors[] = "File $name is not a valid image type. Only JPG, JPEG, PNG & GIF files are allowed.";
                continue;
            }

            // Check file size (limit to 5MB)
            if ($files['size'][$i] > 5000000) {
                $errors[] = "File $name is too large. Maximum size is 5MB.";
                continue;
            }

            // Check if image file is a actual image or fake image
            $check = getimagesize($tmpName);
            if ($check === false) {
                $errors[] = "File $name is not a valid image.";
                continue;
            }

            if (move_uploaded_file($tmpName, $targetFile)) {
                $uploadedFiles[] = $name;
            } else {
                $errors[] = "Failed to upload file: $name";
            }
        } else {
            $errors[] = "Error uploading file: " . $files['name'][$i];
        }
    }

    // Log the activity
    $username = $_SESSION['username'] ?? 'Unknown';
    if (!empty($uploadedFiles)) {
        $details = "Uploaded rental images: " . implode(', ', $uploadedFiles) . " by user $username";
        logActivity($username, 'Uploaded rental images', 'Rental List', $details, $pdo);

        // Prepare current state details string for current_activity_logs
        $currentStateDetails = "Current rental images: " . implode(', ', $uploadedFiles);

        // Upsert current state into current_activity_logs table
        try {
            $stmt = $pdo->prepare("SELECT id FROM current_activity_logs WHERE section = 'Rental List' AND username = ?");
            $stmt->execute([$username]);
            $row = $stmt->fetch();

            if ($row) {
                $updateStmt = $pdo->prepare("UPDATE current_activity_logs SET details = ?, ip_address = ?, user_agent = ?, last_updated = NOW() WHERE id = ?");
                $updateStmt->execute([$currentStateDetails, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '', $row['id']]);
            } else {
                $insertStmt = $pdo->prepare("INSERT INTO current_activity_logs (username, section, details, ip_address, user_agent) VALUES (?, 'Rental List', ?, ?, ?)");
                $insertStmt->execute([$username, $currentStateDetails, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
            }
        } catch (Exception $e) {
            // Log error but do not block user
            error_log("Failed to update current_activity_logs for Rental List: " . $e->getMessage());
        }
    }

    if (empty($errors)) {
        header('Location: dashboard.php#edit-rental-list?success=rental_images_uploaded');
        exit;
    } else {
        $errorMsg = implode('; ', $errors);
        header('Location: dashboard.php#edit-rental-list?error=upload_failed&msg=' . urlencode($errorMsg));
        exit;
    }
} else {
    header('Location: dashboard.php#edit-rental-list?error=invalid_request');
    exit;
}
?>
