<?php
require_once 'includes/create_event_logic.php';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event erstellen - Event-System</title>
    <link rel="stylesheet" href="/css/create_event.css">
</head>
<body>

    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <h1>🎪 EVENT-SYSTEM</h1>
                <div class="nav-links">
                    <a href="index.php">STARTSEITE</a>
                    <a href="my_events.php">MEINE EVENTS</a>
                    <?php if (isOrganizer()): ?>
                        <a href="create_event.php" style="color: #4ecdc4;">EVENT ERSTELLEN</a>
                        <a href="dashboard.php">DASHBOARD</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="user-menu">
                <a href="logout.php" class="logout-btn">ABMELDEN</a>
            </div>
        </div>
    </header>

    <section class="hero-small">
        <div class="hero-content">
            <h1>✨ NEUES EVENT ERSTELLEN</h1>
            <p>Teile deine Veranstaltung mit der Welt</p>
        </div>
    </section>

    <div class="container">
        <div class="form-container">

            <?php if ($success && !isset($_POST['title'])): ?>
                <?php include 'includes/create_event_success.php'; ?>
            <?php else: ?>

                <?php include 'includes/create_event_form.php'; ?>
            <?php endif; ?>
        </div>
    </div>


    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>🎪 EVENT-SYSTEM</h4>
                <p>Deine erste Adresse für die besten Festivals, Partys und Veranstaltungen weltweit.</p>
            </div>
            <div class="footer-section">
                <h4>SCHNELLLINKS</h4>
                <p>Über uns<br>Kontakt<br>AGB<br>Datenschutz</p>
            </div>
            <div class="footer-section">
                <h4>FOLGE UNS</h4>
                <p>Instagram<br>Facebook<br>Twitter<br>TikTok</p>
            </div>
            <div class="footer-section">
                <h4>NEWSLETTER</h4>
                <p>Abonniere unseren Newsletter für exklusive Updates und Frühbucher-Tickets!</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> EVENT-SYSTEM. Alle Rechte vorbehalten.</p>
        </div>
    </footer>

    <script src="js/create_event.js"></script>
</body>
</html>
