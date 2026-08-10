<?php
header('Content-Type: application/json; charset=utf-8');

$dataFile = __DIR__ . '/data/popup.json';

if (file_exists($dataFile)) {
    $data = json_decode(file_get_contents($dataFile), true);
    echo json_encode($data ?: [
        'active' => false,
        'title' => '',
        'text' => '',
        'start_datetime' => '',
        'end_datetime' => '',
        'image' => ''
    ]);
} else {
    echo json_encode([
        'active' => false,
        'title' => '',
        'text' => '',
        'start_datetime' => '',
        'end_datetime' => '',
        'image' => ''
    ]);
}
?>