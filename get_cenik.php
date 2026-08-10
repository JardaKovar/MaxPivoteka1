<?php
header('Content-Type: application/json');

$cenikDataFile = __DIR__ . '/data/cenik.json';

if (file_exists($cenikDataFile)) {
    $data = json_decode(file_get_contents($cenikDataFile), true);
    if (isset($data['title']) && !isset($data[0])) {
        $data = [[
            'id' => '1',
            'title' => $data['title'],
            'pdf' => $data['pdf'] ?? 'uploads/cenik.pdf'
        ]];
    }
    echo json_encode($data ?: []);
} else {
    echo json_encode([
        [
            'id' => '1',
            'title' => 'Ceník Srpen',
            'pdf' => 'uploads/cenik.pdf'
        ]
    ]);
}
?>
