<?php
header('Content-Type: application/json');

$rentalDir = __DIR__ . '/images/rental/';
$images = [];

if (is_dir($rentalDir)) {
    $files = array_diff(scandir($rentalDir), ['.', '..']);
    
    foreach ($files as $file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $images[] = $file;
        }
    }
    
    // Sort images by name
    sort($images);
}

echo json_encode([
    'success' => true,
    'images' => $images,
    'count' => count($images)
]);
?>
