<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: no_access.php');
    exit;
}

// Check if user has diary access
$diary_access = $_SESSION['diary_access'] ?? false;
$username = $_SESSION['username'] ?? 'User';

// Paths for data storage
$tapDataFile = __DIR__ . '/data/taplist.json';
$rentalDataFile = __DIR__ . '/data/rentallist.json';

// Create data directory if not exists
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// Load tap list data
$tapList = file_exists($tapDataFile) ? json_decode(file_get_contents($tapDataFile), true) : [];
// Load rental list data
$rentalList = file_exists($rentalDataFile) ? json_decode(file_get_contents($rentalDataFile), true) : [];

// Get user info
$username = $_SESSION['username'] ?? 'User';
$loginTime = $_SESSION['login_time'] ?? time();
$loginTimeFormatted = date('H:i', $loginTime);

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, maximum-scale=5.0, minimum-scale=1.0" />
    <title>MaxDashboard - MAX PIVOTÉKA</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css?v=<?= time() ?>">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MaxDashboard">

    <style id="activity-log-custom-styles">
        /* Activity Log Modal Cards Styling */
        #activity-log-modal .modal-content {
            max-width: 680px;
            width: 90%;
            background: #111827 !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6) !important;
            border-radius: 16px !important;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 420px;
            overflow-y: auto;
            padding: 6px;
            margin: 1rem 0;
        }

        .activity-list::-webkit-scrollbar {
            width: 6px;
        }
        .activity-list::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.8);
            border-radius: 4px;
        }
        .activity-list::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 4px;
        }
        .activity-list::-webkit-scrollbar-thumb:hover {
            background: #ef4444;
        }

        .activity-item {
            background: #1e293b !important;
            border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 14px !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25) !important;
        }

        .activity-item:hover {
            background: #273549 !important;
            border-color: rgba(255, 255, 255, 0.25) !important;
            transform: translateY(-1px);
        }

        .activity-item.login {
            border-left: 4px solid #22c55e !important;
        }

        .activity-item.logout {
            border-left: 4px solid #ef4444 !important;
        }

        .activity-item.change {
            border-left: 4px solid #3b82f6 !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 8px !important;
        }

        .activity-left-col {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .activity-right-col {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #94a3b8;
            font-size: 0.85rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .activity-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .activity-badge.login {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.4);
        }

        .activity-badge.logout {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        .activity-badge.change {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.4);
        }

        .activity-user {
            color: #f8fafc;
            font-size: 0.92rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .activity-user i {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .activity-time {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #cbd5e1;
            font-weight: 500;
        }

        .activity-ip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #94a3b8;
        }

        .activity-desc-box {
            background: rgba(15, 23, 42, 0.5);
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            color: #e2e8f0;
            font-size: 0.88rem;
            line-height: 1.4;
        }

        .activity-change-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
    </style>

</head>
<body>
    <!-- Loading Screen -->
    
    </div>

    <header>
        <div class="header-left">
            <div class="brand-badge"><i class="fa-solid fa-beer-mug-empty"></i> MAX PIVOTÉKA</div>
            <h1>MaxDashboard <span>Administrace</span></h1>
        </div>
        <div class="header-right">
            <div class="user-info">
                <div class="user-details">
                    <span class="username"><i class="fa-solid fa-user-shield"></i> <?= htmlspecialchars($username) ?></span>
                    <span class="login-time"><i class="fa-regular fa-clock"></i> Přihlášeno: <?= $loginTimeFormatted ?></span>
                </div>
                <div class="user-actions">
                    <a href="index.php" target="_blank" class="btn-secondary"><i class="fa-solid fa-arrow-up-right-from-square"></i> Zobrazit Web</a>
                    <?php if ($username !== 'MaxZ'): ?>
                    <button id="activity-log-btn" class="btn-secondary"><i class="fa-solid fa-clock-rotate-left"></i> Historie aktivity</button>
                    <?php endif; ?>
                    <a href="login.php?logout=1" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Odhlásit se</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section id="edit-tap-list">
            <h2><i class="fa-solid fa-beer-mug-empty"></i> Úprava "Právě na čepu"</h2>
            <form id="tap-form" method="post" action="save_taplist.php">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Číslo</th>
                                <th>Pivovar</th>
                                <th>Pivo</th>
                                <th>Alk. %</th>
                                <th>EPM</th>
                                <th>0,5l (Kč)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 10; $i++): ?>
                            <tr>
                                <td><input type="number" name="taplist[<?= $i ?>][number]" value="<?= htmlspecialchars($tapList[$i]['number'] ?? $i + 1) ?>"></td>
                                <td><input type="text" name="taplist[<?= $i ?>][brewery]" value="<?= htmlspecialchars($tapList[$i]['brewery'] ?? '') ?>"></td>
                                <td><input type="text" name="taplist[<?= $i ?>][beer]" value="<?= htmlspecialchars($tapList[$i]['beer'] ?? '') ?>"></td>
                                <td><input type="text" name="taplist[<?= $i ?>][alc]" value="<?= htmlspecialchars($tapList[$i]['alc'] ?? '') ?>"></td>
                                <td><input type="text" name="taplist[<?= $i ?>][epm]" value="<?= htmlspecialchars($tapList[$i]['epm'] ?? '') ?>"></td>
                                <td><input type="text" name="taplist[<?= $i ?>][price_05l]" value="<?= htmlspecialchars($tapList[$i]['price_05l'] ?? '') ?>"></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Uložit čepovaný lístek</button>
            </form>
        </section>

        <section id="edit-rental-list">
            <h2><i class="fa-solid fa-boxes-packing"></i> Úprava "Půjčovna"</h2>
            
            <!-- Rental Image Management -->
            <div class="sub-card">
                <h3><i class="fa-solid fa-images"></i> Správa obrázků půjčovny</h3>
                
                <!-- Upload Form -->
                <form id="rental-upload-form" method="post" action="rental_upload.php" enctype="multipart/form-data" class="upload-box">
                    <label for="rental-files"><i class="fa-solid fa-cloud-arrow-up"></i> Nahrát obrázky půjčovny (více najednou):</label>
                    <div class="upload-controls">
                        <input type="file" id="rental-files" name="rental_files[]" multiple accept="image/*">
                        <button type="submit" class="btn-secondary"><i class="fa-solid fa-upload"></i> Nahrát obrázky</button>
                    </div>
                </form>
                
                <!-- Delete Form -->
                <form id="rental-delete-form" method="post" action="rental_delete.php">
                    <h4>Dostupné obrázky</h4>
                    <div class="rental-images">
                        <?php
                        $rentalDir = __DIR__ . '/images/rental/';
                        $rentalImages = [];
                        if (is_dir($rentalDir)) {
                            $rentalImages = array_diff(scandir($rentalDir), ['.', '..']);
                        }
                        foreach ($rentalImages as $img):
                            if (in_array(strtolower(pathinfo($img, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])):
                        ?>
                        <label class="image-select-card">
                            <input type="checkbox" name="delete_rental_images[]" value="<?= htmlspecialchars($img) ?>">
                            <img src="images/rental/<?= htmlspecialchars($img) ?>" alt="Obrázek půjčovny">
                            <span><?= htmlspecialchars($img) ?></span>
                        </label>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                    <?php if (!empty($rentalImages)): ?>
                    <button type="submit" class="btn-danger" onclick="return confirm('Opravdu chcete smazat vybrané obrázky?')"><i class="fa-solid fa-trash"></i> Smazat vybrané</button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Rental List Form -->
            <form id="rental-form" method="post" action="save_rentallist.php">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Číslo</th>
                                <th>Název 1</th>
                                <th>Obrázek</th>
                                <th>Detail 2</th>
                                <th>Kauce</th>
                                <th>Den</th>
                                <th>Víkend</th>
                                <th>Týden</th>
                                <th>Měsíc</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 9; $i++): ?>
                            <tr>
                                <td><input type="number" name="rentallist[<?= $i ?>][number]" value="<?= htmlspecialchars($rentalList[$i]['number'] ?? $i + 1) ?>"></td>
                                <td><input type="text" name="rentallist[<?= $i ?>][desc1]" value="<?= htmlspecialchars($rentalList[$i]['desc1'] ?? '') ?>"></td>
                                <td>
                                    <select name="rentallist[<?= $i ?>][image]">
                                        <option value="">Vybrat obrázek</option>
                                        <?php
                                        $currentImage = $rentalList[$i]['image'] ?? '';
                                        foreach ($rentalImages as $img):
                                            if (in_array(strtolower(pathinfo($img, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])):
                                                $selected = ($currentImage === $img) ? 'selected' : '';
                                        ?>
                                        <option value="<?= htmlspecialchars($img) ?>" <?= $selected ?>><?= htmlspecialchars($img) ?></option>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td><input type="text" name="rentallist[<?= $i ?>][desc2]" value="<?= htmlspecialchars($rentalList[$i]['desc2'] ?? '') ?>"></td>
                                <td><input type="text" name="rentallist[<?= $i ?>][deposit]" value="<?= htmlspecialchars($rentalList[$i]['deposit'] ?? '') ?>"></td>
                                <td><input type="text" name="rentallist[<?= $i ?>][day]" value="<?= htmlspecialchars($rentalList[$i]['day'] ?? '') ?>"></td>
                                <td><input type="text" name="rentallist[<?= $i ?>][weekend]" value="<?= htmlspecialchars($rentalList[$i]['weekend'] ?? '') ?>"></td>
                                <td><input type="text" name="rentallist[<?= $i ?>][week]" value="<?= htmlspecialchars($rentalList[$i]['week'] ?? '') ?>"></td>
                                <td><input type="text" name="rentallist[<?= $i ?>][month]" value="<?= htmlspecialchars($rentalList[$i]['month'] ?? '') ?>"></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Uložit ceník půjčovny</button>
            </form>
        </section>

        <section id="edit-events">
            <h2><i class="fa-solid fa-calendar-days"></i> Úprava "Akce"</h2>
            <?php
            // Load events data
            $events = [];
            if ($pdo) {
                try {
                    $stmt = $pdo->query("SELECT * FROM events ORDER BY id ASC");
                    $events = $stmt->fetchAll();
                } catch (PDOException $e) {
                    error_log("Failed to load events: " . $e->getMessage());
                }
            }
            if (empty($events)) {
                $eventsDataFile = __DIR__ . '/data/events.json';
                if (file_exists($eventsDataFile)) {
                    $events = json_decode(file_get_contents($eventsDataFile), true) ?: [];
                }
            }
            // Filter out empty items
            $events = array_values(array_filter($events, function($e) {
                return !empty($e['title']) || !empty($e['description']) || !empty($e['date']);
            }));
            ?>
            <form id="events-form" method="post" action="save_events.php" style="margin-bottom: 2rem;">
                <div class="events-grid" id="events-grid-container" style="margin-bottom: 1.5rem;">
                    <?php foreach ($events as $i => $ev): ?>
                    <div class="event-edit-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem;">
                            <h3><i class="fa-solid fa-calendar-days"></i> Akce #<?= $i + 1 ?></h3>
                            <button type="button" onclick="removeEventCard(this)" class="btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                                <i class="fa-solid fa-trash"></i> Smazat akci
                            </button>
                        </div>

                        <label>Datum:</label>
                        <input type="text" name="events[<?= $i ?>][date]" placeholder="např. 24.12." maxlength="10" value="<?= htmlspecialchars($ev['date'] ?? '') ?>">
                        
                        <label style="margin-top: 0.8rem;">Název akce:</label>
                        <input type="text" name="events[<?= $i ?>][title]" placeholder="Název události" maxlength="100" value="<?= htmlspecialchars($ev['title'] ?? '') ?>">
                        
                        <label style="margin-top: 0.8rem;">Popis:</label>
                        <textarea name="events[<?= $i ?>][description]" rows="4" placeholder="Popis akce..." maxlength="500"><?= htmlspecialchars($ev['description'] ?? '') ?></textarea>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Uložit akce</button>
                    <button type="button" onclick="addEventCard()" class="btn-primary"><i class="fa-solid fa-plus"></i> Přidat další akci</button>
                </div>
            </form>
        </section>

        <section id="edit-gallery">
            <h2><i class="fa-solid fa-photo-film"></i> Úprava Galerie</h2>
            <form id="gallery-upload-form" method="post" action="gallery_upload.php" enctype="multipart/form-data" class="upload-box">
                <label for="gallery-files"><i class="fa-solid fa-cloud-arrow-up"></i> Nahrát fotky (více najednou):</label>
                <div class="upload-controls">
                    <input type="file" id="gallery-files" name="gallery_files[]" multiple accept="image/*">
                    <button type="submit" class="btn-secondary"><i class="fa-solid fa-upload"></i> Nahrát fotky</button>
                </div>
            </form>
            
            <form id="gallery-delete-form" method="post" action="gallery_delete.php">
                <h3>Smazat fotky z galerie</h3>
                <div class="gallery-images">
                    <?php
                    $galleryDir = __DIR__ . '/images/gallery/';
                    $galleryImages = [];
                    if (is_dir($galleryDir)) {
                        $files = array_diff(scandir($galleryDir), ['.', '..']);
                        foreach ($files as $f) {
                            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                                $galleryImages[] = $f;
                            }
                        }
                        sort($galleryImages);
                    }
                    if (!empty($galleryImages)):
                        foreach ($galleryImages as $img):
                    ?>
                    <label class="image-select-card">
                        <input type="checkbox" name="delete_gallery_images[]" value="<?= htmlspecialchars($img) ?>">
                        <img src="images/gallery/<?= htmlspecialchars($img) ?>" alt="Galerie Obrázek">
                    </label>
                    <?php 
                        endforeach;
                    else:
                    ?>
                    <p style="color: #94a3b8; font-size: 0.95rem; grid-column: 1 / -1; padding: 1rem 0;">V galerii zatím nejsou žádné nahrané fotky. Nahrajte je výše.</p>
                    <?php endif; ?>
                </div>
                <?php if (!empty($galleryImages)): ?>
                <button type="submit" class="btn-danger" onclick="return confirm('Opravdu chcete smazat vybrané obrázky?')"><i class="fa-solid fa-trash"></i> Smazat vybrané</button>
                <?php endif; ?>
            </form>
        </section>

        <section id="cenik-management">
            <h2><i class="fa-solid fa-file-pdf"></i> Správa Ceníků</h2>
            <?php
            $cenikDataFile = __DIR__ . '/data/cenik.json';
            $cenikList = file_exists($cenikDataFile) ? json_decode(file_get_contents($cenikDataFile), true) : [];
            if (isset($cenikList['title']) && !isset($cenikList[0])) {
                $cenikList = [['id' => '1', 'title' => $cenikList['title'], 'pdf' => $cenikList['pdf'] ?? 'uploads/cenik.pdf']];
            }
            if (empty($cenikList)) {
                $cenikList = [['id' => '1', 'title' => 'Ceník Srpen', 'pdf' => 'uploads/cenik.pdf']];
            }
            ?>
            
            <form id="save-all-cenik-form" method="post" action="save_cenik.php" enctype="multipart/form-data" style="margin-bottom: 2rem;">
                <input type="hidden" name="action" value="save_all">
                
                <div class="events-grid" style="margin-bottom: 1.5rem;">
                    <?php foreach ($cenikList as $index => $item): 
                        $itemId = htmlspecialchars($item['id']);
                        $itemTitle = htmlspecialchars($item['title'] ?? '');
                        $itemPdf = htmlspecialchars($item['pdf'] ?? '');
                    ?>
                        <div class="event-edit-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem;">
                                <h3><i class="fa-solid fa-beer-mug-empty"></i> Ceník <?= $index + 1 ?></h3>
                                <?php if (count($cenikList) > 1): ?>
                                <button type="submit" form="delete-form-<?= $itemId ?>" onclick="return confirm('Opravdu chcete tento ceník smazat?')" class="btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>

                            <label>Název ceníku (vepsaný v sudu):</label>
                            <input type="text" name="title[<?= $itemId ?>]" value="<?= $itemTitle ?>" placeholder="např. Ceník Srpen" required>

                            <label style="margin-top: 0.8rem;">Nahrát PDF ceník:</label>
                            <input type="file" name="pdf_<?= $itemId ?>" accept=".pdf" style="font-size: 0.85rem; color: #ccc; width: 100%;">
                            <?php if (!empty($itemPdf) && file_exists(__DIR__ . '/' . $itemPdf)): ?>
                                <p style="margin-top: 8px; color: #4ade80; font-size: 0.85rem; word-break: break-all;">
                                    <i class="fa-solid fa-circle-check"></i> Uloženo: <a href="<?= $itemPdf ?>" target="_blank" style="color: #60a5fa; text-decoration: underline;"><?= basename($itemPdf) ?></a>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Uložit ceníky</button>
                    <button type="submit" form="add-new-form" class="btn-secondary"><i class="fa-solid fa-plus"></i> Přidat další ceník</button>
                </div>
            </form>

            <?php foreach ($cenikList as $item): ?>
                <form id="delete-form-<?= htmlspecialchars($item['id']) ?>" method="post" action="save_cenik.php" style="display:none;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="delete_id" value="<?= htmlspecialchars($item['id']) ?>">
                </form>
            <?php endforeach; ?>

            <form id="add-new-form" method="post" action="save_cenik.php" style="display:none;">
                <input type="hidden" name="action" value="add">
            </form>
        </section>

        <!-- Reservation Management Section -->
        <section id="reservation-management">
            <h2>Správa poptávek</h2>
            <div class="reservation-stats">
                <div class="stat-card">
                    <h3>Celkem poptávek</h3>
                    <span id="total-reservations">0</span>
                </div>
                <div class="stat-card">
                    <h3>Čekající na potvrzení</h3>
                    <span id="pending-reservations">0</span>
                </div>
                <div class="stat-card">
                    <h3>Potvrzené</h3>
                    <span id="confirmed-reservations">0</span>
                </div>
                <div class="stat-card">
                    <h3>Zrušené</h3>
                    <span id="cancelled-reservations">0</span>
                </div>
            </div>
            
            <div class="reservation-controls">
                <button onclick="loadReservations()" class="btn-secondary">Obnovit seznam</button>
                <select id="status-filter" onchange="filterReservations()">
                    <option value="">Všechny stavy</option>
                    <option value="pending">Čekající</option>
                    <option value="confirmed">Potvrzené</option>
                    <option value="cancelled">Zrušené</option>
                </select>
            </div>

            <div class="reservations-container">
                <div id="reservations-loading" style="display: none; text-align: center; padding: 2rem;">
                    <p>Načítání rezervací...</p>
                </div>
                <div id="reservations-list">
                    <!-- Reservations will be loaded here -->
                </div>
            </div>
        </section>

        <?php if ($diary_access): ?>
    
        <?php else: ?>

        <?php endif; ?>
    <section id="edit-popup">
            <h2><i class="fa-solid fa-bullhorn"></i> Správa Pop-up Oznámení (Vyskakovací okno na webu)</h2>
            <?php
            $popupDataFile = __DIR__ . '/data/popup.json';
            $popupSettings = file_exists($popupDataFile) ? json_decode(file_get_contents($popupDataFile), true) : [];
            $pActive = !empty($popupSettings['active']);
            $pTitle = htmlspecialchars($popupSettings['title'] ?? '');
            $pText = htmlspecialchars($popupSettings['text'] ?? '');
            $pStart = htmlspecialchars($popupSettings['start_datetime'] ?? '');
            $pEnd = htmlspecialchars($popupSettings['end_datetime'] ?? '');
            $pImage = htmlspecialchars($popupSettings['image'] ?? '');
            ?>
            <form id="popup-form" method="post" action="save_popup.php" enctype="multipart/form-data">
                <div class="event-edit-card" style="margin-bottom: 1.5rem;">
                    
                    <label class="popup-toggle-wrapper" for="popup_active_php" style="margin-bottom: 1.25rem;">
                        <span class="toggle-switch">
                            <input type="checkbox" id="popup_active_php" name="active" value="1" <?= $pActive ? 'checked' : '' ?> onchange="updatePopupToggleLabel(this)">
                            <span class="toggle-slider"></span>
                        </span>
                        <span class="toggle-label-text" id="popup_toggle_label_php"><?= $pActive ? 'Vypnout' : 'Zapnout' ?> pop-up vyskakovací okno na webu</span>
                    </label>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label>Nadpis oznámení:</label>
                        <input type="text" name="title" value="<?= $pTitle ?>" placeholder="např. Mimořádná otevírací doba / Důležité upozornění" style="width: 100%;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label>Text oznámení:</label>
                        <textarea name="text" rows="4" placeholder="Napište zprávu pro návštěvníky webu..." style="width: 100%;"><?= $pText ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group">
                            <label><i class="fa-solid fa-clock"></i> Datum a čas spuštění (Platí OD):</label>
                            <input type="datetime-local" name="start_datetime" value="<?= $pStart ?>" style="width: 100%;">
                            <small style="color: #94a3b8; font-size: 0.8rem; margin-top: 4px; display: block;">Ponechte prázdné pro okamžité spuštění</small>
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-hourglass-end"></i> Datum a čas ukončení (Platí DO):</label>
                            <input type="datetime-local" name="end_datetime" value="<?= $pEnd ?>" style="width: 100%;">
                            <small style="color: #94a3b8; font-size: 0.8rem; margin-top: 4px; display: block;">Ponechte prázdné bez časového omezení</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-image"></i> Volitelná fotka / banner k oznámení (nepovinné):</label>
                        <input type="file" name="popup_image" accept="image/*" style="font-size: 0.85rem; color: #ccc;">
                        <?php if (!empty($pImage) && file_exists(__DIR__ . '/' . $pImage)): ?>
                            <div style="margin-top: 0.8rem; display: flex; align-items: center; gap: 1rem; background: rgba(0,0,0,0.3); padding: 0.8rem; border-radius: 8px;">
                                <img src="<?= $pImage ?>" alt="Pop-up obrázek" style="max-height: 80px; border-radius: 6px; border: 1px solid #475569;">
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; color: #ef4444; cursor: pointer; font-size: 0.9rem;">
                                    <input type="checkbox" name="remove_image" value="1"> Smazat současnou fotku
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Uložit pop-up oznámení</button>
            </form>
        </section>
</main>

    <!-- Activity Log Modal -->
    <?php if ($username !== 'MaxZ'): ?>
    <div id="activity-log-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Activity Log History</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="log-tabs">
                    <button id="log-type-sessions" class="tab-btn active">Login/Logout</button>
                    <button id="log-type-changes" class="tab-btn">Changes</button>
                </div>
                
                <!-- Login/Logout Tab Content -->
                <div id="tab-sessions" class="tab-content active">
                    <div class="log-section">
                        <h3>Login/Logout History</h3>
                        <div id="logs-content-sessions" class="activity-list">
                            <p>Loading login/logout logs...</p>
                        </div>
                        <div style="margin-top: 1rem; display: flex; gap: 1rem; justify-content: center;">
                            <button onclick="loadActivityLogs('sessions')" class="btn-secondary">Refresh Logs</button>
                            <button onclick="clearActivityLogs()" class="btn-logout">Clear History</button>
                        </div>
                    </div>
                </div>
                
                <!-- Changes Tab Content -->
                <div id="tab-changes" class="tab-content">
                    <div class="log-section">
                        <h3>Content Changes History</h3>
                        <div id="logs-content-changes" class="activity-list">
                            <p>Loading content changes logs...</p>
                        </div>
                        <div style="margin-top: 1rem; display: flex; gap: 1rem; justify-content: center;">
                            <button onclick="loadActivityLogs('changes')" class="btn-secondary">Refresh Logs</button>
                            <button onclick="clearActivityLogs()" class="btn-logout">Clear History</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Get modal elements (only if they exist)
        const modal = document.getElementById('activity-log-modal');
        const logBtn = document.getElementById('activity-log-btn');
        const closeBtn = document.querySelector('.modal-close');
        const logTypeSessions = document.getElementById('log-type-sessions');
        const logTypeChanges = document.getElementById('log-type-changes');

        // Get tab content elements (only if they exist)
        const tabSessions = document.getElementById('tab-sessions');
        const tabChanges = document.getElementById('tab-changes');

        // Get content areas for each tab (only if they exist)
        const logsContentSessions = document.getElementById('logs-content-sessions');
        const logsContentChanges = document.getElementById('logs-content-changes');

        // Only set up activity log functionality if elements exist
        if (logBtn && modal) {
            // Open modal when button clicked
            logBtn.onclick = function() {
                modal.style.display = 'flex';
                showTab('sessions');
                loadActivityLogs('sessions');
            }

            // Close modal when close button clicked
            if (closeBtn) {
                closeBtn.onclick = function() {
                    modal.style.display = 'none';
                }
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        }

        // Function to show specific tab
        function showTab(tabType) {
            // Hide all tab contents
            tabSessions.classList.remove('active');
            tabChanges.classList.remove('active');

            // Remove active class from all tab buttons
            logTypeSessions.classList.remove('active');
            logTypeChanges.classList.remove('active');

            // Show selected tab content and activate button
            switch(tabType) {
                case 'sessions':
                    tabSessions.classList.add('active');
                    logTypeSessions.classList.add('active');
                    break;
                case 'changes':
                    tabChanges.classList.add('active');
                    logTypeChanges.classList.add('active');
                    break;
            }
        }

        // Function to get activity icon and class based on action
        function getActivityType(action, section) {
            action = (action || '').toLowerCase();
            section = (section || '').toLowerCase();
            if (action.includes('login') || action.includes('logged in') || section.includes('auth')) {
                return { type: 'login', label: 'LOG IN', icon: 'fa-solid fa-arrow-right-to-bracket' };
            } else if (action.includes('logout') || action.includes('logged out')) {
                return { type: 'logout', label: 'LOG OUT', icon: 'fa-solid fa-arrow-right-from-bracket' };
            } else {
                return { type: 'change', label: action.toUpperCase() || 'ZMĚNA', icon: 'fa-solid fa-pen-to-square' };
            }
        }

        function formatTimestamp(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            if (isNaN(date.getTime())) return timestamp;
            return date.toLocaleString('cs-CZ', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        function loadActivityLogs(type = 'sessions') {
            let targetContent = (type === 'sessions') ? logsContentSessions : logsContentChanges;
            if (!targetContent) return;

            targetContent.innerHTML = '<p style="text-align: center; color: #94a3b8; padding: 1.5rem;"><i class="fa-solid fa-spinner fa-spin"></i> Načítání historie aktivity...</p>';
            
            const params = new URLSearchParams({ type: type, limit: 50 });

            fetch(`get_activity_logs.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.logs && data.logs.length > 0) {
                        let logsHtml = '';
                        data.logs.forEach(log => {
                            const act = getActivityType(log.action, log.section);
                            const formattedTime = formatTimestamp(log.timestamp);
                            
                            if (type === 'sessions') {
                                logsHtml += `
                                    <div class="activity-item ${act.type}">
                                        <div class="activity-left-col">
                                            <span class="activity-badge ${act.type}"><i class="${act.icon}"></i> ${act.label}</span>
                                            <span class="activity-user"><i class="fa-solid fa-user"></i> ${log.username || 'Uživatel'}</span>
                                        </div>
                                        <div class="activity-right-col">
                                            <span class="activity-time"><i class="fa-regular fa-clock"></i> ${formattedTime}</span>
                                            ${log.ip_address ? `<span class="activity-ip"><i class="fa-solid fa-globe"></i> ${log.ip_address}</span>` : ''}
                                        </div>
                                    </div>
                                `;
                            } else {
                                logsHtml += `
                                    <div class="activity-item change">
                                        <div class="activity-change-header">
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <span class="activity-badge change"><i class="${act.icon}"></i> ${act.label}</span>
                                                <span class="activity-user"><i class="fa-solid fa-user"></i> ${log.username || 'Uživatel'}</span>
                                            </div>
                                            <div class="activity-right-col">
                                                <span class="activity-time"><i class="fa-regular fa-clock"></i> ${formattedTime}</span>
                                                ${log.ip_address ? `<span class="activity-ip"><i class="fa-solid fa-globe"></i> ${log.ip_address}</span>` : ''}
                                            </div>
                                        </div>
                                        ${(log.section || log.details) ? `
                                            <div class="activity-desc-box">
                                                ${log.section ? `<strong style="color: #60a5fa;">${log.section}</strong>: ` : ''}
                                                ${log.details || ''}
                                            </div>
                                        ` : ''}
                                    </div>
                                `;
                            }
                        });
                        targetContent.innerHTML = logsHtml;
                    } else {
                        targetContent.innerHTML = '<p style="text-align: center; color: #94a3b8; padding: 2rem;"><i class="fa-regular fa-folder-open"></i> Žádné záznamy aktivit nebyly nalezeny.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading logs:', error);
                    targetContent.innerHTML = '<p style="text-align: center; color: #ef4444; padding: 2rem;"><i class="fa-solid fa-triangle-exclamation"></i> Chyba při načítání historie.</p>';
                });
        }

        // Handle log type buttons (only if they exist)
        if (logTypeSessions) {
            logTypeSessions.onclick = function() {
                showTab('sessions');
                loadActivityLogs('sessions');
            }
        }

        if (logTypeChanges) {
            logTypeChanges.onclick = function() {
                showTab('changes');
                loadActivityLogs('changes');
            }
        }

        // Clear activity logs function
        function clearActivityLogs() {
            if (confirm('Are you sure you want to clear all activity log history? This action cannot be undone.')) {
                fetch('clear_activity_logs.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Activity log history cleared successfully!');
                        // Refresh both tabs
                        loadActivityLogs('sessions');
                        loadActivityLogs('changes');
                    } else {
                        alert('Error clearing logs: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error clearing logs:', error);
                    alert('Error clearing logs. Please try again.');
                });
            }
        }

        // Hide loading screen after page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen) {
                    loadingScreen.style.display = 'none';
                }
            }, 500);
        });

        // Form submission handling
        // Diary functionality
        function addDiaryEntry() {
            const form = document.querySelector('.diary-entry-form');
            const entries = document.getElementById('diary-entries');
            form.style.display = 'block';
            entries.style.display = 'none';
            
            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('diary-date').value = today;
        }

        function cancelDiaryEntry() {
            const form = document.querySelector('.diary-entry-form');
            const entries = document.getElementById('diary-entries');
            form.style.display = 'none';
            entries.style.display = 'block';
            
            // Clear form
            document.getElementById('diary-form').reset();
        }

        function viewDiaryEntries() {
            const form = document.querySelector('.diary-entry-form');
            const entries = document.getElementById('diary-entries');
            form.style.display = 'none';
            entries.style.display = 'block';
            
            // Load diary entries (placeholder for now)
            entries.innerHTML = '<p>Diary entries will be loaded here...</p>';
        }

        // Reservation Management Functions
        let allReservations = [];
        let filteredReservations = [];

        // Load reservations from database
        function loadReservations() {
            const loadingDiv = document.getElementById('reservations-loading');
            const listDiv = document.getElementById('reservations-list');
            
            if (loadingDiv) loadingDiv.style.display = 'block';
            if (listDiv) listDiv.innerHTML = '';

            fetch('get_reservations.php')
                .then(response => response.json())
                .then(data => {
                    if (loadingDiv) loadingDiv.style.display = 'none';
                    
                    if (data.success) {
                        allReservations = data.reservations;
                        filteredReservations = [...allReservations];
                        updateReservationStats();
                        displayReservations(filteredReservations);
                    } else {
                        if (listDiv) {
                            listDiv.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 2rem;">Chyba při načítání rezervací: ' + (data.message || 'Neznámá chyba') + '</p>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading reservations:', error);
                    if (loadingDiv) loadingDiv.style.display = 'none';
                    if (listDiv) {
                        listDiv.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 2rem;">Chyba při načítání rezervací.</p>';
                    }
                });
        }

        // Update reservation statistics
        function updateReservationStats() {
            const totalElement = document.getElementById('total-reservations');
            const pendingElement = document.getElementById('pending-reservations');
            const confirmedElement = document.getElementById('confirmed-reservations');
            const cancelledElement = document.getElementById('cancelled-reservations');

            if (totalElement) totalElement.textContent = allReservations.length;
            if (pendingElement) pendingElement.textContent = allReservations.filter(r => r.status === 'pending').length;
            if (confirmedElement) confirmedElement.textContent = allReservations.filter(r => r.status === 'confirmed').length;
            if (cancelledElement) cancelledElement.textContent = allReservations.filter(r => r.status === 'cancelled').length;
        }

        // Display reservations
        function displayReservations(reservations) {
            const listDiv = document.getElementById('reservations-list');
            if (!listDiv) return;

            if (reservations.length === 0) {
                listDiv.innerHTML = '<p style="text-align: center; color: #666; padding: 2rem;">Žádné rezervace nenalezeny.</p>';
                return;
            }

            let html = '<div class="reservations-grid">';
            reservations.forEach(reservation => {
                const statusClass = reservation.status || 'pending';
                const statusText = {
                    'pending': 'Čeká na potvrzení',
                    'confirmed': 'Potvrzeno',
                    'cancelled': 'Zrušeno'
                }[statusClass] || 'Neznámý';

                html += `
                    <div class="reservation-card" data-id="${reservation.id}">
                        <div class="reservation-header">
                            <h3>${escapeHtml(reservation.first_name)} ${escapeHtml(reservation.last_name)}</h3>
                            <span class="reservation-status status-${statusClass}">
                                ${statusText}
                            </span>
                        </div>
                        
                        <div class="reservation-details">
                            <div class="detail-row">
                                <strong>Email:</strong> ${escapeHtml(reservation.email)}
                            </div>
                            ${reservation.phone ? `<div class="detail-row"><strong>Telefon:</strong> ${escapeHtml(reservation.phone)}</div>` : ''}
                            <div class="detail-row">
                                <strong>Předmět půjčení:</strong> ${escapeHtml(reservation.rental_item || 'N/A')}
                            </div>
                            <div class="detail-row">
                                <strong>Období:</strong> ${escapeHtml(reservation.rental_period || 'N/A')}
                            </div>
                            <div class="detail-row">
                                <strong>Od:</strong> ${formatDate(reservation.rental_date_from)}
                            </div>
                            <div class="detail-row">
                                <strong>Do:</strong> ${formatDate(reservation.rental_date_to)}
                            </div>
                            ${reservation.additional_info ? `<div class="detail-row"><strong>Dodatečné info:</strong> ${escapeHtml(reservation.additional_info)}</div>` : ''}
                            <div class="detail-row">
                                <strong>Podáno:</strong> ${formatDateTime(reservation.created_at)}
                            </div>
                        </div>
                        
                        <div class="reservation-actions">
                            ${reservation.status === 'pending' ? `
                                <button class="btn-action btn-confirm" onclick="updateReservationStatus(${reservation.id}, 'confirmed')">
                                    Potvrdit
                                </button>
                                <button class="btn-action btn-cancel" onclick="updateReservationStatus(${reservation.id}, 'cancelled')">
                                    Zrušit
                                </button>
                            ` : ''}
                            <button class="btn-action btn-delete" onclick="deleteReservation(${reservation.id})">
                                Smazat
                            </button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            listDiv.innerHTML = html;
        }

        // Filter reservations by status
        function filterReservations() {
            const statusFilter = document.getElementById('status-filter');
            if (!statusFilter) return;

            const selectedStatus = statusFilter.value;
            if (selectedStatus === '') {
                filteredReservations = [...allReservations];
            } else {
                filteredReservations = allReservations.filter(r => r.status === selectedStatus);
            }
            displayReservations(filteredReservations);
        }

        // Helper functions
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('cs-CZ');
        }

        function formatDateTime(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleString('cs-CZ');
        }

        function updateReservationStatus(reservationId, status) {
            const statusText = {
                'confirmed': 'potvrdit',
                'cancelled': 'zrušit'
            }[status] || status;

            if (!confirm(`Opravdu chcete ${statusText} tuto rezervaci?`)) {
                return;
            }

            fetch('update_reservation_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: reservationId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload reservations to get updated data
                    loadReservations();
                    alert(`Rezervace byla úspěšně ${status === 'confirmed' ? 'potvrzena' : 'zrušena'}!`);
                } else {
                    alert('Chyba při aktualizaci rezervace: ' + (data.error || 'Neznámá chyba'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Chyba při aktualizaci rezervace. Zkuste to prosím znovu.');
            });
        }

        function deleteReservation(reservationId) {
            if (!confirm('Opravdu chcete smazat tuto rezervaci? Tuto akci nelze vrátit zpět.')) {
                return;
            }

            fetch('delete_reservation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: reservationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload reservations to get updated data
                    loadReservations();
                    alert('Rezervace byla úspěšně smazána!');
                } else {
                    alert('Chyba při mazání rezervace: ' + (data.error || 'Neznámá chyba'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Chyba při mazání rezervace. Zkuste to prosím znovu.');
            });
        }

        // Load reservations when page loads & auto-scroll to active section anchor
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash) {
                const targetElement = document.querySelector(window.location.hash);
                if (targetElement) {
                    setTimeout(() => {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 150);
                }
            }

            // Load reservations after a short delay to ensure page is fully loaded
            setTimeout(() => {
                loadReservations();
            }, 1000);
        });

    
    function addEventCard() {
        const container = document.getElementById('events-grid-container');
        if (!container) return;

        const currentCards = container.querySelectorAll('.event-edit-card');
        const newIndex = currentCards.length;

        const newCard = document.createElement('div');
        newCard.className = 'event-edit-card';

        newCard.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.8rem;">
                <h3><i class="fa-solid fa-calendar-days"></i> Akce #${newIndex + 1}</h3>
                <button type="button" onclick="removeEventCard(this)" class="btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                    <i class="fa-solid fa-trash"></i> Smazat akci
                </button>
            </div>
            <label>Datum:</label>
            <input type="text" name="events[${newIndex}][date]" placeholder="např. 24.12." maxlength="10">
            
            <label style="margin-top: 0.8rem;">Název akce:</label>
            <input type="text" name="events[${newIndex}][title]" placeholder="Název události" maxlength="100">
            
            <label style="margin-top: 0.8rem;">Popis:</label>
            <textarea name="events[${newIndex}][description]" rows="4" placeholder="Popis akce..." maxlength="500"></textarea>
        `;

        container.appendChild(newCard);
    }

    function removeEventCard(btn) {
        const card = btn.closest('.event-edit-card');
        if (card) {
            card.remove();
            reindexEventCards();
        }
    }

    function reindexEventCards() {
        const container = document.getElementById('events-grid-container');
        if (!container) return;

        const cards = container.querySelectorAll('.event-edit-card');
        cards.forEach((card, index) => {
            card.setAttribute('data-index', index);
            const titleHeader = card.querySelector('.event-card-header h3');
            if (titleHeader) titleHeader.textContent = `Akce #${index + 1}`;

            const dateInput = card.querySelector('input[name*="[date]"]');
            const titleInput = card.querySelector('input[name*="[title]"]');
            const descInput = card.querySelector('textarea[name*="[description]"]');

            if (dateInput) dateInput.name = `events[${index}][date]`;
            if (titleInput) titleInput.name = `events[${index}][title]`;
            if (descInput) descInput.name = `events[${index}][description]`;
        });
    }


    function updatePopupToggleLabel(checkbox) {
        const labelText = checkbox.closest('.popup-toggle-wrapper').querySelector('.toggle-label-text');
        if (labelText) {
            labelText.textContent = (checkbox.checked ? 'Vypnout' : 'Zapnout') + ' pop-up vyskakovací okno na webu';
        }
    }

</script>
</html>
