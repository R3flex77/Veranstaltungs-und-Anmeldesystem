<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Prüfen, ob Benutzer eingeloggt ist
if (!isLoggedIn()) {
    redirect('login.php');
}

// Prüfen, ob Benutzer ein Veranstalter ist
if (!isOrganizer()) {
    redirect('index.php');
}


renderHeader('Dashboard - Event-System', 'dashboard', '/css/dashboard.css');
?>

<?php
require_once 'includes/dashboard_page.php';
?>

<?php
require_once 'includes/footer.php';
renderFooter();
?>

<script src="js/dashboard.js"></script>
