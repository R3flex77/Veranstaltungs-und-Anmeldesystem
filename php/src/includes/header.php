<?php
function renderHeader(string $pageTitle = 'Event-System', string $activePage = 'index', string $pageCss = '') {
    ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <?php if ($pageCss !== ''): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($pageCss); ?>">
    <?php endif; ?>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <h1>🎪 EVENT-SYSTEM</h1>
                <div class="nav-links">
                    <a href="index.php" class="<?php echo $activePage === 'index' ? 'active' : ''; ?>">STARTSEITE</a>
                    <a href="my_events.php" class="<?php echo $activePage === 'my_events' ? 'active' : ''; ?>">MEINE EVENTS</a>
                    <?php if (isOrganizer()): ?>
                        <a href="create_event.php" class="<?php echo $activePage === 'create_event' ? 'active' : ''; ?>">EVENT ERSTELLEN</a>
                        <a href="dashboard.php" class="<?php echo $activePage === 'dashboard' ? 'active' : ''; ?>">DASHBOARD</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="user-menu">
                <a href="logout.php" class="logout-btn">ABMELDEN <span class="username">(<?php echo htmlspecialchars($_SESSION['username']); ?>)</span></a>
            </div>
        </div>
    </header>
    <?php
}
?>
