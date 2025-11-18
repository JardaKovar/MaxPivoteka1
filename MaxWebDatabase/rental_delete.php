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

    foreach ($imagesToDelete as $image) {
        $imagePath = $rentalDir . basename($image); // basename for security
        
        if (file_exists($imagePath)) {
            if (unlink($imagePath)) {
                $deletedFiles[] = $image;
            } else {
                $errors[] = "Failed to delete: $image";
            }
        } else {
            $errors[] = "File not found: $image";
        }
    }

    // Log the activity
    $username = $_SESSION['username'] ?? 'Unknown';
    if (!empty($deletedFiles)) {
        $details = "Deleted rental images: " . implode(', ', $deletedFiles) . " by user $username";
        logActivity($username, 'Deleted rental images', 'Rental List', $details, $pdo);

        // Update current state in current_activity_logs
        try {
            // Get remaining images
            $remainingImages = [];
            if (is_dir($rentalDir)) {
                $remainingImages = array_diff(scandir($rentalDir), ['.', '..']);
                $remainingImages = array_filter($remainingImages, function($file) {
                    return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                });
            }

            $currentStateDetails = "Current rental images: " . (empty($remainingImages) ? 'None' : implode(', ', $remainingImages));

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
            error_log("Failed to update current_activity_logs for Rental List: " . $e->getMessage());
        }
    }

    if (empty($errors)) {
        header('Location: dashboard.php#edit-rental-list?success=rental_images_deleted');
        exit;
    } else {
        $errorMsg = implode('; ', $errors);
        header('Location: dashboard.php#edit-rental-list?error=delete_failed&msg=' . urlencode($errorMsg));
        exit;
    }
} else {
    header('Location: dashboard.php#edit-rental-list?error=invalid_request');
    exit;
}
?>
