<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: dashboard.php?error=forbidden#cenik-management');
    exit;
}

$dataDir = __DIR__ . '/data';
$uploadsDir = __DIR__ . '/uploads';

if (!file_exists($dataDir)) mkdir($dataDir, 0755, true);
if (!file_exists($uploadsDir)) mkdir($uploadsDir, 0755, true);

$cenikDataFile = $dataDir . '/cenik.json';
$cenikList = file_exists($cenikDataFile) ? json_decode(file_get_contents($cenikDataFile), true) : [];

if (!is_array($cenikList)) {
    $cenikList = [];
}

$action = $_POST['action'] ?? 'save_all';

if (isset($_POST['action_delete'])) {
    $action = 'delete';
    $_POST['delete_id'] = $_POST['action_delete'];
} elseif (isset($_POST['action_add'])) {
    $action = 'add';
}

if ($action === 'add') {
    // Add new Ceník item
    $title = trim($_POST['new_title'] ?? '');
    if (empty($title)) {
        $title = 'Ceník ' . (count($cenikList) + 1);
    }

    $pdfPath = '';

    if (isset($_FILES['new_pdf']) && $_FILES['new_pdf']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['new_pdf']['tmp_name'];
        $fileName = $_FILES['new_pdf']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExt === 'pdf') {
            $newFileName = 'cenik_' . time() . '_' . rand(100, 999) . '.pdf';
            $destPath = $uploadsDir . '/' . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $pdfPath = 'uploads/' . $newFileName;
            }
        }
    }

    $newItem = [
        'id' => uniqid(),
        'title' => $title,
        'pdf' => $pdfPath,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $cenikList[] = $newItem;
    file_put_contents($cenikDataFile, json_encode(array_values($cenikList), JSON_PRETTY_PRINT));

    if (isset($pdo)) {
        logActivity($_SESSION['username'] ?? 'Admin', 'Added', 'Ceník', "Přidán nový ceník: $title", $pdo);
    }

    header('Location: dashboard.php?success=added#cenik-management');
    exit;

} elseif ($action === 'delete') {
    $deleteId = $_POST['delete_id'] ?? '';
    $updatedList = [];
    $deletedTitle = '';

    foreach ($cenikList as $item) {
        if ($item['id'] === $deleteId) {
            $deletedTitle = $item['title'];
            if (!empty($item['pdf']) && file_exists(__DIR__ . '/' . $item['pdf'])) {
                @unlink(__DIR__ . '/' . $item['pdf']);
            }
        } else {
            $updatedList[] = $item;
        }
    }

    file_put_contents($cenikDataFile, json_encode(array_values($updatedList), JSON_PRETTY_PRINT));

    if (isset($pdo)) {
        logActivity($_SESSION['username'] ?? 'Admin', 'Deleted', 'Ceník', "Smazán ceník: $deletedTitle", $pdo);
    }

    header('Location: dashboard.php?success=deleted#cenik-management');
    exit;

} else {
    // Update existing items
    $titles = $_POST['title'] ?? [];

    foreach ($cenikList as $index => &$item) {
        $itemId = $item['id'];
        if (isset($titles[$itemId])) {
            $item['title'] = trim($titles[$itemId]);
        }

        // Check if a replacement PDF was uploaded for this item
        if (isset($_FILES['pdf_' . $itemId]) && $_FILES['pdf_' . $itemId]['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['pdf_' . $itemId]['tmp_name'];
            $fileName = $_FILES['pdf_' . $itemId]['name'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if ($fileExt === 'pdf') {
                $newFileName = 'cenik_' . time() . '_' . rand(100, 999) . '.pdf';
                $destPath = $uploadsDir . '/' . $newFileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    if (!empty($item['pdf']) && file_exists(__DIR__ . '/' . $item['pdf'])) {
                        @unlink(__DIR__ . '/' . $item['pdf']);
                    }
                    $item['pdf'] = 'uploads/' . $newFileName;
                }
            }
        }
    }

    file_put_contents($cenikDataFile, json_encode(array_values($cenikList), JSON_PRETTY_PRINT));

    if (isset($pdo)) {
        logActivity($_SESSION['username'] ?? 'Admin', 'Updated', 'Ceník', "Ceníky byly upraveny", $pdo);
    }

    header('Location: dashboard.php?success=saved#cenik-management');
    exit;
}
?>
