<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/footer.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

renderHeader('Event-System', 'index');
?>

<section class="hero">
    <div class="hero-content">
        <h1>ENTDECKE GROSSARTIGE EVENTS</h1>
        <p>Finde die besten Festivals, Partys und Veranstaltungen in deiner Nähe</p>
    </div>
</section>

<div class="container">
    <div class="section-title">
        <span>📅</span>
        <span>Events</span>
    </div>
    <p style="text-align: center; padding: 50px;">Event-Liste wird bald angezeigt...</p>
</div>

<?php renderFooter(); ?>
