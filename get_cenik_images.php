<?php
header('Content-Type: application/json');

try {
    $cenikDir = __DIR__ . '/images/cenik/';
    $images = [];
    
    if (is_dir($cenikDir)) {
        $files = array_diff(scandir($cenikDir), ['.', '..']);
        // Filter only image files
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filteredImages = [];
        
        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $imageExtensions)) {
                $filteredImages[] = $file;
            }
        }
        
        $images = array_values($filteredImages);
    }
    
    echo json_encode([
        'success' => true,
        'images' => $images,
        'count' => count($images)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'images' => [], 
        'error' => $e->getMessage()
    ]);
}
?>
