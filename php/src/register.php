<?php
require_once 'includes/db.php';
require_once 'includes/registration_handler.php';

// Wenn bereits eingeloggt, direkt zur index.php weiterleiten
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$message = '';

// Verarbeite Registrierungs-Anfrage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registrationHandler = new RegistrationHandler();
    $result = $registrationHandler->handleRegistration(
        $_POST['username'],
        $_POST['password'],
        $_POST['role'] ?? 'user'
    );
    
    if ($result['success']) {
        $message = $result['message'];
    } else {
        $error = $result['error'];
    }
}

// CSS
include 'css/register_form.html';
?>
