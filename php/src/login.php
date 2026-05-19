<?php
require_once 'includes/db.php';
require_once 'includes/auth_handler.php';

// Wenn bereits eingeloggt, direkt zur index.php weiterleiten
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

// Verarbeite Login-Anfrage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authHandler = new AuthHandler();
    $result = $authHandler->handleLogin($_POST['username'], $_POST['password']);
    
    if ($result['success']) {
        redirect('index.php');
    } else {
        $error = $result['error'];
    }
}

// CSS
include 'css/login_form.html';
?>
