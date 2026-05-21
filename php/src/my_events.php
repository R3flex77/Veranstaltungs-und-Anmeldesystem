<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/footer.php';
require_once __DIR__ . '/includes/my_events_function.php';
require_once __DIR__ . '/includes/my_events_page.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['user_id'];

$my_events = getMyEventsForUser($db, $user_id);
$weekdays = getGermanWeekdays();

renderMyEventsPage($my_events, $weekdays);
