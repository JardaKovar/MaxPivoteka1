<?php
// Start output buffering to prevent header issues
ob_start();

// Start session before any output
session_start();

// Include database configuration
require_once 'db_config.php';

// Set content type header
header('Content-Type: application/json; charset=utf-8');

// Prevent any output before JSON response
ob_clean();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$rental_item = trim($_POST['rental_item'] ?? '');

$rental_date_from = trim($_POST['rental_date_from'] ?? '');
$rental_date_to = trim($_POST['rental_date_to'] ?? '');
$additional_info = trim($_POST['additional_info'] ?? '');

// Validation
$errors = [];

if (empty($first_name)) {
    $errors[] = 'Jméno je povinné';
}

if (empty($last_name)) {
    $errors[] = 'Příjmení je povinné';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Platný email je povinný';
}

if (empty($rental_item)) {
    $errors[] = 'Výběr předmětu k půjčení je povinný';
}



if (empty($rental_date_from)) {
    $errors[] = 'Datum od je povinné';
} else {
    $date_from = DateTime::createFromFormat('Y-m-d', $rental_date_from);
    if (!$date_from || $date_from->format('Y-m-d') !== $rental_date_from) {
        $errors[] = 'Neplatný formát data od';
    } elseif ($date_from < new DateTime('today')) {
        $errors[] = 'Datum od nemůže být v minulosti';
    }
}

if (empty($rental_date_to)) {
    $errors[] = 'Datum do je povinné';
} else {
    $date_to = DateTime::createFromFormat('Y-m-d', $rental_date_to);
    if (!$date_to || $date_to->format('Y-m-d') !== $rental_date_to) {
        $errors[] = 'Neplatný formát data do';
    } elseif ($date_to < new DateTime('today')) {
        $errors[] = 'Datum do nemůže být v minulosti';
    }
}

// Check if date_to is after date_from
if (!empty($rental_date_from) && !empty($rental_date_to)) {
    $date_from = DateTime::createFromFormat('Y-m-d', $rental_date_from);
    $date_to = DateTime::createFromFormat('Y-m-d', $rental_date_to);
    if ($date_from && $date_to && $date_to < $date_from) {
        $errors[] = 'Datum do musí být po datu od';
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

if (!isset($pdo) || !$pdo) {
    echo json_encode(['success' => false, 'message' => 'Databáze není dostupná. Zkuste to prosím později.']);
    exit;
}

try {
    // Insert reservation into database
    $stmt = $pdo->prepare("INSERT INTO reservations (first_name, last_name, email, phone, rental_item, rental_date_from, rental_date_to, additional_info, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$first_name, $last_name, $email, $phone, $rental_item, $rental_date_from, $rental_date_to, $additional_info]);
    
    $reservation_id = $pdo->lastInsertId();
    
    // Log the reservation
    logReservationActivity('System', 'New Reservation', $reservation_id, "From: $first_name $last_name ($email)", $pdo);
    
    // Send confirmation email to customer
    $customer_subject = 'Potvrzení rezervace půjčovny - MAX PIVOTÉKA';
    $customer_message = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .details { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
            .footer { background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>MAX PIVOTÉKA</h1>
            <h2>Potvrzení rezervace půjčovny</h2>
        </div>
        <div class='content'>
            <p>Vážený/á " . htmlspecialchars($first_name) . " " . htmlspecialchars($last_name) . ",</p>
            
            <p>Děkujeme za Vaši rezervaci půjčovny. Vaše žádost byla úspěšně odeslána a bude zpracována v nejbližší době.</p>
            
            <div class='details'>
                <h3>Detaily rezervace:</h3>
                <p><strong>Číslo rezervace:</strong> #" . $reservation_id . "</p>
                <p><strong>Jméno:</strong> " . htmlspecialchars($first_name . ' ' . $last_name) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                " . (!empty($phone) ? "<p><strong>Telefon:</strong> " . htmlspecialchars($phone) . "</p>" : "") . "
                <p><strong>Předmět půjčení:</strong> " . htmlspecialchars($rental_item) . "</p>
                <p><strong>Datum od:</strong> " . date('d.m.Y', strtotime($rental_date_from)) . "</p>
                <p><strong>Datum do:</strong> " . date('d.m.Y', strtotime($rental_date_to)) . "</p>
                " . (!empty($additional_info) ? "<p><strong>Dodatečné informace:</strong> " . nl2br(htmlspecialchars($additional_info)) . "</p>" : "") . "
            </div>
            
            <p>Vaše rezervace má aktuálně status <strong>ČEKÁ NA POTVRZENÍ</strong>. Budeme Vás kontaktovat v nejbližší době s dalšími informacemi.</p>
            
            <p>V případě jakýchkoli dotazů nás neváhejte kontaktovat:</p>
            <p>📞 +420 605 085 150 nebo +420 603 239 703<br>
            📧 odmax@seznam.cz</p>
        </div>
        <div class='footer'>
            <p>MAX PIVOTÉKA<br>
            Červené Vršky 2086, Benešov<br>
            Tento email byl odeslán automaticky, prosím neodpovídejte na něj.</p>
        </div>
    </body>
    </html>";
    
    $email_sent = sendReservationEmail($email, $customer_subject, $customer_message);
    
    // Send notification email to admin
    $admin_email = 'sebastianpokorny@seznam.cz';
    $admin_subject = 'Nová rezervace půjčovny - MAX PIVOTÉKA';
    $admin_message = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .details { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>Nová rezervace půjčovny</h1>
        </div>
        <div class='content'>
            <p>Byla přijata nová rezervace půjčovny:</p>
            
            <div class='details'>
                <p><strong>Číslo rezervace:</strong> #" . $reservation_id . "</p>
                <p><strong>Jméno:</strong> " . htmlspecialchars($first_name . ' ' . $last_name) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                " . (!empty($phone) ? "<p><strong>Telefon:</strong> " . htmlspecialchars($phone) . "</p>" : "") . "
                <p><strong>Předmět půjčení:</strong> " . htmlspecialchars($rental_item) . "</p>
                <p><strong>Datum od:</strong> " . date('d.m.Y', strtotime($rental_date_from)) . "</p>
                <p><strong>Datum do:</strong> " . date('d.m.Y', strtotime($rental_date_to)) . "</p>
                " . (!empty($additional_info) ? "<p><strong>Dodatečné informace:</strong> " . nl2br(htmlspecialchars($additional_info)) . "</p>" : "") . "
                <p><strong>Čas podání:</strong> " . date('d.m.Y H:i:s') . "</p>
            </div>
            
            <p>Přihlaste se do administrace pro zpracování rezervace.</p>
        </div>
    </body>
    </html>";
    
    sendReservationEmail($admin_email, $admin_subject, $admin_message);
    
    // Clean any output buffer before sending JSON
    ob_clean();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Rezervace byla úspěšně odeslána! Potvrzení bylo zasláno na Váš email.',
        'reservation_id' => $reservation_id
    ]);
    
} catch (PDOException $e) {
    error_log("Reservation submission error: " . $e->getMessage());
    
    // Clean any output buffer before sending JSON
    ob_clean();
    
    echo json_encode(['success' => false, 'message' => 'Chyba při ukládání rezervace. Zkuste to prosím později.']);
}

// End output buffering and send response
ob_end_flush();
?>
