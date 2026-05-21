<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/footer.php';
require_once __DIR__ . '/includes/my_events_service.php';
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
>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> EVENT-SYSTEM. Alle Rechte vorbehalten.</p>
    </footer>

    <script>
        setTimeout(function() {
            let notifications = document.querySelectorAll('.notification');
            notifications.forEach(function(n) {
                n.remove();
            });
        }, 4000);
    </script>
</body>
</html>
