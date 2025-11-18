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

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    $uploadDir = __DIR__ . '/images/gallery/';
    $xamppUploadDir = 'd:/XAMPP/htdocs/images/gallery/';
    
    // Create directories if they don't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    if (!is_dir($xamppUploadDir)) {
        mkdir($xamppUploadDir, 0755, true);
    }
    
    if (isset($_FILES['gallery_files']) && !empty($_FILES['gallery_files']['name'][0])) {
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
                        logGalleryOperation($pdo, 'upload', null, $newFilename, null, "Failed to upload: $originalName", 'failed', 'File move failed');
                    }
                } else {
                    logGalleryOperation($pdo, 'upload', null, $filename, null, "Invalid file type: $originalName", 'failed', 'Invalid file extension');
                }
            }
        }
    }
    
    header('Location: gallery_manager.php?uploaded=1');
    exit;
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $imageId) {
            // Get image info from database
            $stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE id = ? AND status = 'active'");
            $stmt->execute([$imageId]);
            $image = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($image) {
                $filePath = __DIR__ . '/' . $image['file_path'];
                $xamppPath = 'd:/XAMPP/htdocs/' . $image['file_path'];
                
                // Delete physical files
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                if (file_exists($xamppPath)) {
                    unlink($xamppPath);
                }
                
                // Mark as deleted in database
                $stmt = $pdo->prepare("UPDATE gallery_images SET status = 'deleted', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$imageId]);
                
                // Log operation
                logGalleryOperation($pdo, 'delete', $image['filename'], null, $image['file_path'], "Deleted: " . $image['original_name']);
            }
        }
    }
    
    header('Location: gallery_manager.php?deleted=1');
    exit;
}

// Get all active gallery images
$stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE status = 'active' ORDER BY upload_date DESC");
$stmt->execute();
$galleryImages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent operations
$stmt = $pdo->prepare("SELECT * FROM gallery_operations ORDER BY operation_date DESC LIMIT 20");
$stmt->execute();
$recentOperations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Manager - MAX PIVOTÉKA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #dc3545 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #dc3545;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2.5rem;
        }
        
        .section {
            margin-bottom: 3rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 4px solid #dc3545;
        }
        
        .section h2 {
            color: #495057;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .upload-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .file-input {
            padding: 1rem;
            border: 2px dashed #dc3545;
            border-radius: 10px;
            background: rgba(220, 53, 69, 0.05);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-input:hover {
            background: rgba(220, 53, 69, 0.1);
            transform: translateY(-2px);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #dc3545 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.3);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .gallery-item {
            position: relative;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .gallery-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .gallery-item-info {
            padding: 1rem;
        }
        
        .gallery-item-name {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .gallery-item-date {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .checkbox-container {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 5px;
            padding: 5px;
        }
        
        .operations-log {
            max-height: 400px;
            overflow-y: auto;
            background: white;
            border-radius: 10px;
            padding: 1rem;
        }
        
        .operation-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border-bottom: 1px solid #e9ecef;
            transition: background 0.2s ease;
        }
        
        .operation-item:hover {
            background: #f8f9fa;
        }
        
        .operation-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.8rem;
        }
        
        .operation-upload { background: #28a745; }
        .operation-delete { background: #dc3545; }
        .operation-change { background: #ffc107; color: #212529; }
        
        .operation-details {
            flex: 1;
        }
        
        .operation-type {
            font-weight: 600;
            color: #495057;
        }
        
        .operation-description {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .operation-time {
            font-size: 0.8rem;
            color: #adb5bd;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #155724;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #dc3545;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Gallery Manager</h1>
        
        <?php if (isset($_GET['uploaded'])): ?>
            <div class="alert alert-success">✅ Images uploaded successfully!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">🗑️ Images deleted successfully!</div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($galleryImages) ?></div>
                <div class="stat-label">Total Images</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($recentOperations, fn($op) => $op['operation_type'] === 'upload' && $op['status'] === 'success')) ?></div>
                <div class="stat-label">Recent Uploads</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($recentOperations, fn($op) => $op['operation_type'] === 'delete' && $op['status'] === 'success')) ?></div>
                <div class="stat-label">Recent Deletions</div>
            </div>
        </div>
        
        <!-- Upload Section -->
        <div class="section">
            <h2>📤 Upload New Images</h2>
            <form method="post" enctype="multipart/form-data" class="upload-form">
                <input type="hidden" name="action" value="upload">
                <input type="file" name="gallery_files[]" multiple accept="image/*" class="file-input" required>
                <button type="submit" class="btn">Upload Images</button>
            </form>
        </div>
        
        <!-- Gallery Management -->
        <div class="section">
            <h2>🖼️ Manage Gallery Images (<?= count($galleryImages) ?> images)</h2>
            
            <?php if (empty($galleryImages)): ?>
                <p style="text-align: center; color: #6c757d; padding: 2rem;">No images found. Upload some images to get started!</p>
            <?php else: ?>
                <form method="post">
                    <input type="hidden" name="action" value="delete">
                    <div class="gallery-grid">
                        <?php foreach ($galleryImages as $image): ?>
                            <div class="gallery-item">
                                <div class="checkbox-container">
                                    <input type="checkbox" name="delete_images[]" value="<?= $image['id'] ?>">
                                </div>
                                <img src="<?= htmlspecialchars($image['file_path']) ?>" alt="<?= htmlspecialchars($image['original_name']) ?>" loading="lazy">
                                <div class="gallery-item-info">
                                    <div class="gallery-item-name"><?= htmlspecialchars($image['original_name']) ?></div>
                                    <div class="gallery-item-date"><?= date('d.m.Y H:i', strtotime($image['upload_date'])) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-danger" style="margin-top: 1rem;" onclick="return confirm('Are you sure you want to delete selected images?')">Delete Selected</button>
                </form>
            <?php endif; ?>
        </div>
        
        <!-- Operations Log -->
        <div class="section">
            <h2>📋 Recent Operations Log</h2>
            <div class="operations-log">
                <?php if (empty($recentOperations)): ?>
                    <p style="text-align: center; color: #6c757d; padding: 2rem;">No operations recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($recentOperations as $operation): ?>
                        <div class="operation-item">
                            <div class="operation-icon operation-<?= $operation['operation_type'] ?>">
                                <?php
                                switch($operation['operation_type']) {
                                    case 'upload': echo '↑'; break;
                                    case 'delete': echo '×'; break;
                                    case 'change': echo '↔'; break;
                                    default: echo '•'; break;
                                }
                                ?>
                            </div>
                            <div class="operation-details">
                                <div class="operation-type"><?= ucfirst($operation['operation_type']) ?></div>
                                <div class="operation-description"><?= htmlspecialchars($operation['operation_details'] ?? 'No details') ?></div>
                                <div class="operation-time"><?= date('d.m.Y H:i:s', strtotime($operation['operation_date'])) ?> by <?= htmlspecialchars($operation['user_id']) ?></div>
                            </div>
                            <div style="color: <?= $operation['status'] === 'success' ? '#28a745' : '#dc3545' ?>; font-weight: bold;">
                                <?= $operation['status'] === 'success' ? '✓' : '✗' ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="dashboard.php" class="btn">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
