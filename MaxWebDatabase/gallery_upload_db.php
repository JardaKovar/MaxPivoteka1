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

$uploadSuccess = false;
$uploadErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gallery_files'])) {
    $uploadDir = __DIR__ . '/images/gallery/';
    $xamppUploadDir = 'd:/XAMPP/htdocs/images/gallery/';
    
    // Create directories if they don't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    if (!is_dir($xamppUploadDir)) {
        mkdir($xamppUploadDir, 0755, true);
    }
    
    if (!empty($_FILES['gallery_files']['name'][0])) {
        foreach ($_FILES['gallery_files']['name'] as $key => $filename) {
            if ($_FILES['gallery_files']['error'][$key] === UPLOAD_ERR_OK) {
                $originalName = $filename;
                $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    // Generate unique filename
                    $newFilename = 'galerie_' . time() . '_' . $key . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $newFilename;
                    $xamppPath = $xamppUploadDir . $newFilename;
                    
                    if (move_uploaded_file($_FILES['gallery_files']['tmp_name'][$key], $uploadPath)) {
                        // Copy to XAMPP directory
                        if (file_exists($xamppPath)) {
                            unlink($xamppPath);
                        }
                        copy($uploadPath, $xamppPath);
                        
                        // Insert into database
                        $stmt = $pdo->prepare("INSERT INTO gallery_images (filename, original_name, file_path, file_size, mime_type, status) VALUES (?, ?, ?, ?, ?, 'active')");
                        $stmt->execute([
                            $newFilename,
                            $originalName,
                            'images/gallery/' . $newFilename,
                            filesize($uploadPath),
                            $_FILES['gallery_files']['type'][$key]
                        ]);
                        
                        // Log operation
                        logGalleryOperation($pdo, 'upload', null, $newFilename, 'images/gallery/' . $newFilename, "Uploaded: $originalName → $newFilename");
                        
                        $uploadSuccess = true;
                    } else {
                        $uploadErrors[] = "Failed to upload: $originalName";
                        logGalleryOperation($pdo, 'upload', null, $newFilename, null, "Failed to upload: $originalName", 'failed', 'File move failed');
                    }
                } else {
                    $uploadErrors[] = "Invalid file type: $originalName";
                    logGalleryOperation($pdo, 'upload', null, $filename, null, "Invalid file type: $originalName", 'failed', 'Invalid file extension');
                }
            }
        }
    }
}

// Redirect back to dashboard with status
if ($uploadSuccess && empty($uploadErrors)) {
    header('Location: dashboard.php#gallery-management?success=upload');
} elseif (!empty($uploadErrors)) {
    $errorMsg = implode(', ', $uploadErrors);
    header('Location: dashboard.php#gallery-management?error=' . urlencode($errorMsg));
} else {
    header('Location: dashboard.php#gallery-management');
}
exit;
?>
