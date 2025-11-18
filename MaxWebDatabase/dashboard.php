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
    <link rel="stylesheet" href="css/dashboard.css?v=<?= time() ?>">
    <meta name="theme-color" content="#dc3545">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="MaxDashboard">
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading-screen" style="display:none;">
        <div class="loading-content">
            <img src="images/logo2.png" alt="MAX PIVOTÉKA" class="loading-logo">
            <div class="loading-spinner"></div>
        </div>
    </div>

    <header>
        <div class="header-left">
            <h1>MaxDashboard - Edit Website Content</h1>
        </div>
        <div class="header-right">
            <div class="user-info">
                <div class="user-details">
                    <span class="username"><?= htmlspecialchars($username) ?></span>
                    <span class="login-time">Logged in: <?= $loginTimeFormatted ?></span>
                </div>
                <div class="user-actions">
                    <a href="index.php" target="_blank" class="btn-secondary">View Website</a>
                    <?php if ($username !== 'MaxZ'): ?>
                    <button id="activity-log-btn" class="btn-secondary" style="margin-left: 10px;">Activity Log History</button>
                    <?php endif; ?>
                    <a href="login.php?logout=1" class="btn-logout" style="margin-left: 10px;">Logout</a>
                </div>








            </div>
        </div>
    </header>

    <main>
        <section id="edit-tap-list">
            <h2>Edit "Právě na čepu" (Just on Tap)</h2>
            <form id="tap-form" method="post" action="save_taplist.php">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Number</th>
                                <th>Brewery</th>
                                <th>Beer</th>
                                <th>Alc. %</th>
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
                <button type="submit">Save Tap List</button>
            </form>
        </section>

        <section id="edit-rental-list">
            <h2>Edit "Půjčovna" (Rental List)</h2>
            
            <!-- Rental Image Management -->
            <div style="margin-bottom: 2rem; padding: 1.5rem; border: 1px solid #333; border-radius: 8px;">
                <h3>Rental Image Management</h3>
                
                <!-- Upload Form -->
                <form id="rental-upload-form" method="post" action="rental_upload.php" enctype="multipart/form-data" style="margin-bottom: 1.5rem;">
                    <label for="rental-files">Upload Rental Images (multiple):</label>
                    <input type="file" id="rental-files" name="rental_files[]" multiple accept="image/*">
                    <button type="submit">Upload Images</button>
                </form>
                
                <!-- Delete Form -->
                <form id="rental-delete-form" method="post" action="rental_delete.php">
                    <h4>Available Rental Images</h4>
                    <div class="rental-images" style="display: flex; flex-wrap: wrap; gap: 1rem; margin: 1rem 0;">
                        <?php
                        $rentalDir = __DIR__ . '/images/rental/';
                        $rentalImages = [];
                        if (is_dir($rentalDir)) {
                            $rentalImages = array_diff(scandir($rentalDir), ['.', '..']);
                        }
                        foreach ($rentalImages as $img):
                            if (in_array(strtolower(pathinfo($img, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])):
                        ?>
                        <label style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="delete_rental_images[]" value="<?= htmlspecialchars($img) ?>">
                            <img src="images/rental/<?= htmlspecialchars($img) ?>" alt="Rental Image" style="max-height: 100px; max-width: 100px; object-fit: cover; border-radius: 4px;">
                            <span style="font-size: 0.8rem; text-align: center; word-break: break-all;"><?= htmlspecialchars($img) ?></span>
                        </label>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                    <?php if (!empty($rentalImages)): ?>
                    <button type="submit" onclick="return confirm('Are you sure you want to delete the selected images?')">Delete Selected</button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Rental List Form -->
            <form id="rental-form" method="post" action="save_rentallist.php">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Number</th>
                                <th>Description 1</th>
                                <th>Image</th>
                                <th>Description 2</th>
                                <th>Deposit</th>
                                <th>Day</th>
                                <th>Weekend</th>
                                <th>Week</th>
                                <th>Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < 5; $i++): ?>
                            <tr>
                                <td><input type="number" name="rentallist[<?= $i ?>][number]" value="<?= htmlspecialchars($rentalList[$i]['number'] ?? $i + 1) ?>"></td>
                                <td><input type="text" name="rentallist[<?= $i ?>][desc1]" value="<?= htmlspecialchars($rentalList[$i]['desc1'] ?? '') ?>"></td>
                                <td>
                                    <select name="rentallist[<?= $i ?>][image]" style="width: 100%; padding: 0.5rem;">
                                        <option value="">Select Image</option>
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
                <button type="submit">Save Rental List</button>
            </form>
        </section>

        <section id="edit-events">
            <h2>Edit "Akce" (Events)</h2>
            <?php
            // Load events data from database
            $events = [];
            if ($pdo) {
                try {
                    $stmt = $pdo->query("SELECT * FROM events ORDER BY id ASC");
                    $events = $stmt->fetchAll();
                } catch (PDOException $e) {
                    error_log("Failed to load events: " . $e->getMessage());
                }
            }
            
            // Ensure we have at least 3 empty slots for editing
            while (count($events) < 3) {
                $events[] = ['date' => '', 'title' => '', 'description' => ''];
            }
            ?>
            <form id="events-form" method="post" action="save_events.php">
                <div class="events-grid">
                    <?php for ($i = 0; $i < 3; $i++): ?>
                    <div class="event-edit-card">
                        <h3>Event <?= $i + 1 ?></h3>
                        <label>Date:</label>
                        <input type="text" name="events[<?= $i ?>][date]" placeholder="24.12." maxlength="10" value="<?= htmlspecialchars($events[$i]['date'] ?? '') ?>">
                        
                        <label>Title:</label>
                        <input type="text" name="events[<?= $i ?>][title]" placeholder="Event Title" maxlength="100" value="<?= htmlspecialchars($events[$i]['title'] ?? '') ?>">
                        
                        <label>Description:</label>
                        <textarea name="events[<?= $i ?>][description]" rows="4" placeholder="Event description..." maxlength="500"><?= htmlspecialchars($events[$i]['description'] ?? '') ?></textarea>
                    </div>
                    <?php endfor; ?>
                </div>
                <button type="submit">Save Events</button>
            </form>
        </section>

        <section id="edit-gallery">
            <h2>Edit Gallery</h2>
            <form id="gallery-upload-form" method="post" action="gallery_upload.php" enctype="multipart/form-data">
                <label for="gallery-files">Upload Images (multiple):</label>
                <input type="file" id="gallery-files" name="gallery_files[]" multiple accept="image/*">
                <button type="submit">Upload Images</button>
            </form>
            
            <form id="gallery-delete-form" method="post" action="gallery_delete.php">
                <h3>Delete Images</h3>
                <div class="gallery-images">
                    <?php
                    $galleryDir = __DIR__ . '/images/gallery/';
                    $galleryImages = [];
                    if (is_dir($galleryDir)) {
                        $galleryImages = array_diff(scandir($galleryDir), ['.', '..']);
                    }
                    foreach ($galleryImages as $img):
                        if (in_array(strtolower(pathinfo($img, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])):
                    ?>
                    <label>
                        <input type="checkbox" name="delete_gallery_images[]" value="<?= htmlspecialchars($img) ?>">
                        <img src="images/gallery/<?= htmlspecialchars($img) ?>" alt="Gallery Image" style="max-height: 100px; margin: 5px;">
                    </label>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <button type="submit">Delete Selected</button>
            </form>
        </section>



        <section id="cenik-management">
            <h2>Price List Management</h2>
            <form id="cenik-upload-form" method="post" action="cenik_upload.php" enctype="multipart/form-data">
                <label for="cenik-files">Upload Images (multiple):</label>
                <input type="file" id="cenik-files" name="cenik_files[]" multiple accept="image/*">
                <button type="submit">Upload</button>
            </form>
            <form id="cenik-delete-form" method="post" action="cenik_delete.php">
                <h3>Delete Images</h3>
                <div class="cenik-images">
                    <?php
                    $cenikDir = __DIR__ . '/images/cenik/';
                    $cenikImages = [];
                    if (is_dir($cenikDir)) {
                        $cenikImages = array_diff(scandir($cenikDir), ['.', '..']);
                    }
                    foreach ($cenikImages as $img):
                        if (in_array(strtolower(pathinfo($img, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])):
                    ?>
                    <label>
                        <input type="checkbox" name="delete_cenik_images[]" value="<?= htmlspecialchars($img) ?>">
                        <img src="images/cenik/<?= htmlspecialchars($img) ?>" alt="Price List Image" style="max-height: 100px; margin: 5px;">
                    </label>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                <button type="submit">Delete Selected</button>
            </form>
        </section>

        <!-- Reservation Management Section -->
        <section id="reservation-management">
            <h2>Správa rezervací</h2>
            <div class="reservation-stats">
                <div class="stat-card">
                    <h3>Celkem rezervací</h3>
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
    </main>

    <!-- Activity Log Modal -->
    <?php if ($username !== 'MaxZ'): ?>
    <div id="activity-log-modal" class="modal">
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
        function getActivityIcon(action, section) {
            if (action.toLowerCase().includes('login') || action.toLowerCase().includes('logged in')) {
                return { icon: '🔓', class: 'login' };
            } else if (action.toLowerCase().includes('logout') || action.toLowerCase().includes('logged out')) {
                return { icon: '🔒', class: 'logout' };
            } else if (section === 'Authentication') {
                return { icon: '🔐', class: 'login' };
            } else {
                return { icon: '📝', class: 'change' };
            }
        }

        // Function to format timestamp
        function formatTimestamp(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleString('cs-CZ', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        // Load activity logs function with enhanced display
        function loadActivityLogs(type = 'sessions') {
            // Get the correct content area based on type
            let targetContent;
            switch(type) {
                case 'sessions':
                    targetContent = logsContentSessions;
                    break;
                case 'changes':
                    targetContent = logsContentChanges;
                    break;
                default:
                    targetContent = logsContentSessions;
            }

            targetContent.innerHTML = '<p style="text-align: center; color: #666;">Loading activity logs...</p>';
            
            const params = new URLSearchParams({
                type: type,
                limit: 50
            });

            fetch(`get_activity_logs.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.logs && data.logs.length > 0) {
                        let logsHtml = '';
                        data.logs.forEach(log => {
                            const activityInfo = getActivityIcon(log.action, log.section);
                            const formattedTime = formatTimestamp(log.timestamp);
                            
                            logsHtml += `
                                <div class="activity-item ${activityInfo.class}">
                                    <div class="activity-icon ${activityInfo.class}">
                                        ${activityInfo.icon}
                                    </div>
                                    <div class="activity-details">
                                        <div class="activity-action">${log.action}</div>
                                        <div class="activity-description">
                                            ${log.section ? `Section: ${log.section}` : ''}
                                            ${log.details ? `<br>${log.details}` : ''}
                                        </div>
                                        <div class="activity-meta">
                                            <div class="activity-time">
                                                🕒 ${formattedTime}
                                            </div>
                                            <div class="activity-ip">
                                                👤 ${log.username}
                                            </div>
                                            ${log.ip_address ? `<div class="activity-ip">🌐 ${log.ip_address}</div>` : ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        targetContent.innerHTML = logsHtml;
                    } else {
                        targetContent.innerHTML = '<p style="text-align: center; color: #666;">No activity logs found.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading logs:', error);
                    targetContent.innerHTML = '<p style="text-align: center; color: #dc3545;">Error loading activity logs.</p>';
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
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen) {
                    loadingScreen.style.display = 'flex';
                }
            });
        });

        // Clear input fields on focus to prevent missing first character issue
        document.querySelectorAll('input[name^="taplist"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.value = '';
            });
        });

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

        // Load reservations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Load reservations after a short delay to ensure page is fully loaded
            setTimeout(() => {
                loadReservations();
            }, 1000);
        });

    </script>
</html>
