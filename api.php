<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Load data from JSON files
$tapDataFile = __DIR__ . '/data/taplist.json';
$rentalDataFile = __DIR__ . '/data/rentallist.json';
$eventsDataFile = __DIR__ . '/data/events.json';

$tapList = file_exists($tapDataFile) ? json_decode(file_get_contents($tapDataFile), true) : [
    ['number' => 1, 'brewery' => 'KAMENICE NAD LIPOU', 'beer' => 'KAMENICKÁ 10', 'alc' => '4,2', 'epm' => '10', 'ibu' => '28', 'ebc' => '11', 'price_05l' => '55', 'price_03l' => '39'],
    ['number' => 2, 'brewery' => 'MAXBEER BN', 'beer' => 'EXTRA HOŘKÝ LEŽÁK', 'alc' => '5', 'epm' => '12', 'ibu' => '52', 'ebc' => '', 'price_05l' => '59', 'price_03l' => '45'],
    ['number' => 3, 'brewery' => 'HUBERTUS KÁCOV', 'beer' => 'L.P. 1457 SVĚTLÝ LEŽÁK', 'alc' => '4,4', 'epm' => '11', 'ibu' => '', 'ebc' => '', 'price_05l' => '49', 'price_03l' => '39'],
    ['number' => 4, 'brewery' => 'FERDINAND BN', 'beer' => 'SVĚTLÝ LEŽÁK PREMIUM', 'alc' => '5', 'epm' => '12', 'ibu' => '', 'ebc' => '', 'price_05l' => '49', 'price_03l' => '39'],
    ['number' => 5, 'brewery' => 'PLZEŇSKÝ PRAZDROJ', 'beer' => 'PILSNER URQUELL', 'alc' => '4,4', 'epm' => '11', 'ibu' => '', 'ebc' => '', 'price_05l' => '59', 'price_03l' => '45']
];

$rentalList = file_exists($rentalDataFile) ? json_decode(file_get_contents($rentalDataFile), true) : [
    ['number' => 1, 'desc1' => 'GRILY', 'image' => 'grill.png', 'desc2' => 'PLYNOVÝ GRIL', 'deposit' => '2000', 'day' => '200', 'weekend' => '400', 'week' => '1000', 'month' => '2500'],
    ['number' => 2, 'desc1' => 'PÍPY', 'image' => 'pípa.png', 'desc2' => 'PŘENOSNÉ CHLAZENÍ', 'deposit' => '3000', 'day' => '300', 'weekend' => '500', 'week' => '1200', 'month' => '3000'],
    ['number' => 3, 'desc1' => 'PIV. SETY', 'image' => 'pivset (2).png', 'desc2' => 'STŮL + 2 LAVICE', 'deposit' => '500', 'day' => '50', 'weekend' => '100', 'week' => '200', 'month' => '500'],
    ['number' => 4, 'desc1' => 'BOMBY', 'image' => 'bomba.png', 'desc2' => 'VÝČEPNÍ ZAŘÍZENÍ', 'deposit' => '200', 'day' => '20', 'weekend' => '40', 'week' => '100', 'month' => '200']
];

$events = file_exists($eventsDataFile) ? json_decode(file_get_contents($eventsDataFile), true) : [
    ['date' => '24.12.', 'title' => 'Vánoční pivní speciály', 'description' => 'Přijďte ochutnat naše sváteční pivní speciály. V nabídce budou tmavé i světlé vánoční speciály od vybraných pivovarů.'],
    ['date' => '31.12.', 'title' => 'Silvestrovská ochutnávka', 'description' => 'Speciální silvestrovská nabídka piv. Připravili jsme pro vás výběr toho nejlepšího z našeho sortimentu.'],
    ['date' => '15.1.', 'title' => 'Degustace řemeslných piv', 'description' => 'Řízená degustace vybraných řemeslných piv z českých minipivovarů. Rezervace míst předem nutná.']
];

// Get gallery images
$galleryDir = __DIR__ . '/images/gallery/';
$galleryImages = [];
if (is_dir($galleryDir)) {
    $galleryImages = array_diff(scandir($galleryDir), ['.', '..']);
}

// If no gallery images, use default ones
if (empty($galleryImages)) {
    $defaultImages = ['galerie1.jpg', 'galerie2.jpg', 'galerie3.jpg', 'galerie4.jpg', 'galerie5.jpg', 'galerie6.jpg', 'galerie7.jpg', 'galerie8.jpg'];
    $galleryImages = array_filter($defaultImages, function($img) {
        return file_exists(__DIR__ . '/images/' . $img);
    });
}

// Return all data as JSON
echo json_encode([
    'tapList' => $tapList,
    'rentalList' => $rentalList,
    'galleryImages' => array_values($galleryImages),
    'events' => $events
]);
?>
