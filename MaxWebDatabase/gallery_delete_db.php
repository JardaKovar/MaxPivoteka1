<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: no_access.php');
    exit;
}

// Database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to log gallery operations
function logGalleryOperation($pdo, $operation_type, $old_filename = null, $new_filename = null, $file_path = null, $details = null, $status = 'success', $error = null) {
    $stmt = $pdo->prepare("INSERT INTO gallery_operations (operation_type, old_filename, new_filename, file_path, operation_details, user_id, ip_address, user_agent, status, error_message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $operation_type,
        $old_filename,
        $new_filename,
        $file_path,
        $details,
        $_SESSION['username'] ?? 'admin',
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        $status,
        $error
    ]);
}

$deleteSuccess = false;
$deleteErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_images'])) {
    if (is_array($_POST['delete_images']) && !empty($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $imageId) {
            // Get image info from database
            $stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE id = ? AND status = 'active'");
            $stmt->execute([$imageId]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($image) {
                $filePath = __DIR__ . '/' . $image['file_path'];
                $xamppPath = 'd:/XAMPP/htdocs/' . $image['file_path'];
                
                $fileDeleted = false;
                $xamppDeleted = false;
                
                // Delete physical files
                if (file_exists($filePath)) {
                    $fileDeleted = unlink($filePath);
                } else {
                    $fileDeleted = true; // File doesn't exist, consider it deleted
                }
                
                if (file_exists($xamppPath)) {
                    $xamppDeleted = unlink($xamppPath);
                } else {
                    $xamppDeleted = true; // File doesn't exist, consider it deleted
                }
                
                if ($fileDeleted && $xamppDeleted) {
                    // Mark as deleted in database
                    $stmt = $pdo->prepare("UPDATE gallery_images SET status = 'deleted', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->execute([$imageId]);
                    
                    // Log operation
                    logGalleryOperation($pdo, 'delete', $image['filename'], null, $image['file_path'], "Deleted: " . $image['original_name']);
                    
                    $deleteSuccess = true;
                } else {
                    $deleteErrors[] = "Failed to delete: " . $image['original_name'];
                    logGalleryOperation($pdo, 'delete', $image['filename'], null, $image['file_path'], "Failed to delete: " . $image['original_name'], 'failed', 'File deletion failed');
                }
            } else {
                $deleteErrors[] = "Image not found in database";
                logGalleryOperation($pdo, 'delete', null, null, null, "Image not found in database", 'failed', 'Image not found');
            }
        }
    }
}

// Redirect back to dashboard with status
if ($deleteSuccess && empty($deleteErrors)) {
    header('Location: dashboard.php#gallery-management?success=delete');
} elseif (!empty($deleteErrors)) {
    $errorMsg = implode(', ', $deleteErrors);
    header('Location: dashboard.php#gallery-management?error=' . urlencode($errorMsg));
} else {
    header('Location: dashboard.php#gallery-management');
}
exit;
?>
