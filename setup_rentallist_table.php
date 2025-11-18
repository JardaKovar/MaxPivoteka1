<?php
require_once 'db_config.php';

try {
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    // Create rentallist table if it doesn't exist
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS rentallist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        number INT NOT NULL,
        desc1 VARCHAR(255) DEFAULT '',
        image VARCHAR(255) DEFAULT '',
        desc2 VARCHAR(255) DEFAULT '',
        deposit VARCHAR(50) DEFAULT '',
        day VARCHAR(50) DEFAULT '',
        weekend VARCHAR(50) DEFAULT '',
        week VARCHAR(50) DEFAULT '',
        month VARCHAR(50) DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($createTableSQL);
    echo "✓ Rentallist table created/verified\n";

    // Clear existing data
    $pdo->exec("DELETE FROM rentallist");
    echo "✓ Cleared existing rental data\n";

    // Insert rental items data
    $rentalItems = [
        [1, 'GRILY', 'grill.png', 'PLYNOVÝ GRIL', '2000', '200', '400', '1000', '2500'],
        [2, 'PÍPY', 'pípa.png', '1 KOH - PŘENOSNÉ CHLAZENÍ', '2500', '250', '400', '1000', '2500'],
        [3, 'PÍPY', 'pípa.png', '2 KOH - PŘENOSNÉ CHLAZENÍ', '3500', '350', '550', '1400', '3500'],
        [4, 'PÍPY', 'pípa.png', '1 KOH - CHLAZENÍ', '3000', '300', '500', '1200', '3000'],
        [5, 'PÍPY', 'pípa.png', '2 KOH - CHLAZENÍ', '4000', '400', '650', '1600', '4000'],
        [6, 'VYCHLAZENÍ SUDU', 'pípa.png', 'CHLAZENÍ SUDU', '2000', '200', '350', '900', '2200'],
        [7, 'BOMBA', 'bomba.png', 'PIVNÍ BOMBA', '1500', '150', '300', '800', '2000'],
        [8, 'NUTCASE', 'pivset (2).png', 'PIVNÍ SET', '1000', '100', '200', '500', '1200'],
        [9, 'PEPÍK', '', 'SPECIÁLNÍ VYBAVENÍ', '500', '50', '100', '250', '600']
    ];

    $stmt = $pdo->prepare("INSERT INTO rentallist (number, desc1, image, desc2, deposit, day, weekend, week, month) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($rentalItems as $item) {
        $stmt->execute($item);
    }
    
    echo "✓ Inserted " . count($rentalItems) . " rental items\n";

    // Update JSON file as well for fallback
    $jsonData = [];
    foreach ($rentalItems as $item) {
        $jsonData[] = [
            'number' => (string)$item[0],
            'desc1' => $item[1],
            'image' => $item[2],
            'desc2' => $item[3],
            'deposit' => $item[4],
            'day' => $item[5],
            'weekend' => $item[6],
            'week' => $item[7],
            'month' => $item[8]
        ];
    }
    
    $jsonFile = __DIR__ . '/data/rentallist.json';
    if (!file_exists(dirname($jsonFile))) {
        mkdir(dirname($jsonFile), 0755, true);
    }
    file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "✓ Updated JSON fallback file\n";

    echo "\n🎉 Rental list setup completed successfully!\n";
    echo "All " . count($rentalItems) . " rental items are now available in both database and JSON fallback.\n";

} catch (Exception $e) {
    echo "❌ Error setting up rental list: " . $e->getMessage() . "\n";
    exit(1);
}
?>
