<?php
function renderMyEventsPage(array $my_events, array $weekdays): void {
    renderHeader('Meine Events - EVENT-SYSTEM', 'my_events', 'css/my_events.css');
    ?>
    <main class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="notification success">✓ <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="notification error">⚠ <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="page-header">
            <h1>Meine Events</h1>
            <p>Hier siehst du alle Veranstaltungen, für die du dich angemeldet hast.</p>
        </div>

        <?php if (count($my_events) > 0): ?>
            <div class="events-grid">
                <?php foreach ($my_events as $event):
                    $isPast = strtotime($event['date']) < time();
                    $weekday_en = date('l', strtotime($event['date']));
                    $weekday_de = $weekdays[$weekday_en] ?? $weekday_en;
                    $event_image = !empty($event['image']) ? $event['image'] : 'img/festival-default.jpg';
                ?>
                    <div class="event-card">
                        <div class="card-image">
                            <img src="<?php echo htmlspecialchars($event_image); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                            <div class="card-overlay"></div>
                            <div class="status-badge <?php echo $isPast ? 'status-past' : 'status-upcoming'; ?>">
                                <?php echo $isPast ? '📅 ABGELAUFEN' : '🔥 BEVORSTEHEND'; ?>
                            </div>
                            <div class="date-badge">
                                <?php echo date('d.m.Y', strtotime($event['date'])); ?>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
                            <div class="event-date">
                                📅 <?php echo $weekday_de . ', ' . date('d.m.Y - H:i', strtotime($event['date'])); ?>
                            </div>
                            <div class="event-location">
                                📍 <?php echo htmlspecialchars($event['location']); ?>
                            </div>
                            <div class="booking-info">
                                🎟️ Gebucht am: <?php echo date('d.m.Y', strtotime($event['registered_at'])); ?>
                            </div>
                            <?php if (!$isPast): ?>
                                <a href="api/unregister_event.php?registration_id=<?php echo urlencode($event['registration_id']); ?>&event_id=<?php echo urlencode($event['id']); ?>" 
                                   class="btn-unregister" 
                                   onclick="return confirm(<?php echo json_encode('Möchtest du dich wirklich von ' . $event['title'] . ' abmelden?'); ?>)">
                                    ❌ VOM EVENT ABMELDEN
                                </a>
                            <?php else: ?>
                                <button class="btn-unregister disabled" disabled>
                                    ⏰ EVENT BEREITS VORBEI
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-events">
                <div class="icon">🎟️</div>
                <h3>Keine Event-Buchungen</h3>
                <p>Du hast dich noch für kein Event angemeldet.</p>
                <a href="index.php" class="btn-browse">🔥 EVENTS ENTDECKEN</a>
            </div>
        <?php endif; ?>
    </main>
    <?php
    renderFooter();
}
