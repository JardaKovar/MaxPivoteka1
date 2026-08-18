<?php
ob_start();
session_start();
require_once __DIR__ . '/db_config.php';

header('Content-Type: application/json; charset=utf-8');
ob_clean();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Neplatná metoda']);
    exit;
}

$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$rental_item = trim($_POST['rental_item'] ?? '');
$rental_date_from = trim($_POST['rental_date_from'] ?? '');
$rental_date_to = trim($_POST['rental_date_to'] ?? '');
$additional_info = trim($_POST['additional_info'] ?? '');

$errors = [];
if (empty($first_name)) $errors[] = 'Jméno je povinné';
if (empty($last_name)) $errors[] = 'Příjmení je povinné';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Platný email je povinný';
if (empty($rental_item)) $errors[] = 'Výběr předmětu k půjčení je povinný';
if (empty($rental_date_from)) $errors[] = 'Datum od je povinné';
if (empty($rental_date_to)) $errors[] = 'Datum do je povinné';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// 1. Save to JSON backup in data/reservations.json
$resDir = __DIR__ . '/data';
if (!file_exists($resDir)) @mkdir($resDir, 0755, true);
$resFile = $resDir . '/reservations.json';
$existingReservations = file_exists($resFile) ? json_decode(file_get_contents($resFile), true) : [];
if (!is_array($existingReservations)) $existingReservations = [];

$newReservation = [
    'id' => time() . rand(100, 999),
    'first_name' => $first_name,
    'last_name' => $last_name,
    'email' => $email,
    'phone' => $phone,
    'rental_item' => $rental_item,
    'rental_date_from' => $rental_date_from,
    'rental_date_to' => $rental_date_to,
    'additional_info' => $additional_info,
    'status' => 'pending',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

array_unshift($existingReservations, $newReservation);
@file_put_contents($resFile, json_encode($existingReservations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 2. Save to database if connected
if ($pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO reservations (first_name, last_name, email, phone, rental_item, rental_date_from, rental_date_to, additional_info, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$first_name, $last_name, $email, $phone, $rental_item, $rental_date_from, $rental_date_to, $additional_info]);
    } catch (Throwable $e) {
        error_log("Reservation DB insert error: " . $e->getMessage());
    }
}

logActivity('Návštěvník', 'Nová poptávka', 'Rezervace', "Poptávka na $rental_item od $first_name $last_name ($email)", $pdo);

echo json_encode([
    'success' => true,
    'message' => 'Vaše poptávka byla úspěšně odeslána. Brzy se Vám ozveme!'
]);
exit;
?>