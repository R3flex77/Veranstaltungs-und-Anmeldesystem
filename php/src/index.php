<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/event_helper.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/footer.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['user_id'];
$events = getUpcomingEvents($db, $user_id);
$months = getGermanMonths();
$weekdays = getGermanWeekdays();
$events_by_month = groupEventsByYearAndMonth($events);


renderHeader('Event-System - Festival Kalender', 'index');
?>

<section class="hero">
    <div class="hero-content">
        <h1>ENTDECKE GROSSARTIGE EVENTS</h1>
        <p>Finde die besten Festivals, Partys und Veranstaltungen in deiner Nähe</p>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Events durchsuchen..." onkeyup="filterEvents()">
            <button type="button">🔍 SUCHE</button>
        </div>
    </div>
</section>

<div class="container">
    <div class="year-selector">
        <button class="year-btn aktiv" data-year="2026">2026</button>
        <button class="year-btn" data-year="2025">2025</button>
        <button class="year-btn" data-year="2024">2024</button>
    </div>

    <div class="section-title">
        <span class="trending-icon">🔥</span>
        <span>ANGESAGTE EVENTS</span>
    </div>

    <?php include __DIR__ . '/includes/events_list.php'; ?>
</div>

<?php renderFooter(); ?>
