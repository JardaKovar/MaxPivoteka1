<?php
header('Content-Type: application/json');

$galleryDir = __DIR__ . '/images/gallery/';
$images = [];

if (is_dir($galleryDir)) {
    $files = scandir($galleryDir);
    
    if ($files !== false) {
        // Remove . and .. entries
        $files = array_filter($files, function($file) {
            return $file !== '.' && $file !== '..';
        });
        
        // Filter for image files only
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $imageExtensions)) {
                $images[] = $file;
            }
        }
        
        // Sort images naturally
        sort($images);
    }
}

echo json_encode([
    'success' => true,
    'images' => $images,
    'count' => count($images)
]);
?>
