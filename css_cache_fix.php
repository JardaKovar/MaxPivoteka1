<?php
/**
 * CSS Cache Fix Script for Modal Content Issues in XAMPP
 * This script provides multiple solutions for CSS caching problems
 */

// Function to get CSS file modification time
function getCSSModTime($cssFile) {
    if (file_exists($cssFile)) {
        return filemtime($cssFile);
    }
    return time();
}

// Function to generate cache-busting parameter
function getCacheBuster($cssFile) {
    return getCSSModTime($cssFile);
}

// Check if dashboard.css exists and get its info
$dashboardCSS = __DIR__ . '/css/dashboard.css';
$cssExists = file_exists($dashboardCSS);
$cssModTime = $cssExists ? date('Y-m-d H:i:s', filemtime($dashboardCSS)) : 'File not found';
$cacheBuster = getCacheBuster($dashboardCSS);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Cache Fix - XAMPP Modal Content</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .solution-box {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .code-block {
            background: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 10px 0;
        }
        .btn {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>🔧 CSS Cache Fix for XAMPP Modal Content</h1>
    
    <div class="solution-box <?php echo $cssExists ? 'success' : 'warning'; ?>">
        <h3>📊 Current Status</h3>
        <table>
            <tr>
                <th>Item</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>CSS File</td>
                <td><?php echo $cssExists ? '✅ Found' : '❌ Missing'; ?></td>
                <td><?php echo $dashboardCSS; ?></td>
            </tr>
            <tr>
                <td>Last Modified</td>
                <td><?php echo $cssModTime; ?></td>
                <td>Used for cache busting</td>
            </tr>
            <tr>
                <td>Cache Buster</td>
                <td><?php echo $cacheBuster; ?></td>
                <td>Timestamp parameter</td>
            </tr>
            <tr>
                <td>Dashboard.php Fix</td>
                <td>✅ Applied</td>
                <td>Added ?v=<?php echo time(); ?> parameter</td>
            </tr>
        </table>
    </div>

    <div class="solution-box">
        <h3>🎯 Solution 1: Cache-Busting Parameter (APPLIED)</h3>
        <p>I've already updated your <code>dashboard.php</code> file with this solution:</p>
        <div class="code-block">
&lt;link rel="stylesheet" href="css/dashboard.css?v=&lt;?= time() ?&gt;"&gt;
        </div>
        <p><strong>How it works:</strong> Adds a timestamp parameter to force browsers to reload the CSS file every time.</p>
    </div>

    <div class="solution-box">
        <h3>🔄 Solution 2: File Modification Time Cache Busting</h3>
        <p>Alternative approach using file modification time (more efficient):</p>
        <div class="code-block">
&lt;link rel="stylesheet" href="css/dashboard.css?v=&lt;?= filemtime('css/dashboard.css') ?&gt;"&gt;
        </div>
        <p><strong>Benefit:</strong> Only updates when CSS file actually changes, better for performance.</p>
    </div>

    <div class="solution-box">
        <h3>🌐 Solution 3: HTTP Headers (Server-Level)</h3>
        <p>Add these headers to prevent CSS caching in your PHP files:</p>
        <div class="code-block">
&lt;?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?&gt;
        </div>
    </div>

    <div class="solution-box">
        <h3>🔧 Solution 4: .htaccess Method</h3>
        <p>Create/update <code>.htaccess</code> file in your CSS directory:</p>
        <div class="code-block">
&lt;FilesMatch "\.(css|js)$"&gt;
    ExpiresActive On
    ExpiresDefault "access plus 1 seconds"
&lt;/FilesMatch&gt;
        </div>
    </div>

    <div class="solution-box warning">
        <h3>⚠️ XAMPP Specific Issues</h3>
        <ul>
            <li><strong>Apache Cache:</strong> Restart Apache in XAMPP Control Panel</li>
            <li><strong>PHP OPcache:</strong> Clear PHP cache if enabled</li>
            <li><strong>Browser Cache:</strong> Use Ctrl+Shift+R for hard refresh</li>
            <li><strong>File Permissions:</strong> Ensure CSS files are readable</li>
        </ul>
    </div>

    <div class="solution-box">
        <h3>🧪 Testing Instructions</h3>
        <ol>
            <li><strong>Open your dashboard:</strong> <a href="dashboard.php" target="_blank" class="btn">Open Dashboard</a></li>
            <li><strong>Test CSS loading:</strong> <a href="test_css_cache.html" target="_blank" class="btn">Run CSS Test</a></li>
            <li><strong>Check browser dev tools:</strong> F12 → Network tab → look for CSS file with timestamp</li>
            <li><strong>Test modal:</strong> Click "Activity Log History" button in dashboard</li>
        </ol>
    </div>

    <div class="solution-box">
        <h3>🔍 Troubleshooting Steps</h3>
        <div class="code-block">
1. Clear browser cache: Ctrl+Shift+Delete
2. Hard refresh: Ctrl+Shift+R or Ctrl+F5
3. Restart XAMPP Apache service
4. Check file permissions on CSS files
5. Verify CSS file path is correct
6. Check browser console for errors (F12)
        </div>
    </div>

    <div class="solution-box success">
        <h3>✅ Expected Results</h3>
        <p>After applying the fix, you should see:</p>
        <ul>
            <li>CSS file loads with timestamp parameter (e.g., <code>dashboard.css?v=1703123456</code>)</li>
            <li>Modal opens with updated styling</li>
            <li>No cached CSS issues in XAMPP</li>
            <li>Changes to CSS file reflect immediately</li>
        </ul>
    </div>

    <script>
        // Auto-refresh every 30 seconds to show updated timestamps
        setTimeout(function() {
            location.reload();
        }, 30000);

        // Add click handlers for testing
        document.addEventListener('DOMContentLoaded', function() {
            console.log('CSS Cache Fix loaded');
            console.log('Current timestamp:', <?php echo time(); ?>);
            console.log('CSS file timestamp:', <?php echo $cacheBuster; ?>);
        });
    </script>
</body>
</html>
