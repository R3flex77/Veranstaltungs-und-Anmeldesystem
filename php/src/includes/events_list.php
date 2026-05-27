<?php
if (!isset($events_by_month) || !isset($months) || !isset($weekdays)) {
    return;
}
?>
<div id="eventsContainer">
    <?php if (count($events_by_month) === 0): ?>
        <div class="no-events">
            <div class="icon">📅</div>
            <h3>Keine bevorstehenden Events</h3>
            <p>Schau später wieder vorbei für tolle Events!</p>
            <a href="dashboard.php" class="btn-book" style="display: inline-block; width: auto; padding: 12px 30px;">✨ EVENT ERSTELLEN</a>
        </div>
    <?php else: ?>
        <?php foreach ($events_by_month as $year => $months_in_year): ?>
            <div class="year-group" data-year="<?php echo htmlspecialchars($year); ?>">
                <?php foreach ($months_in_year as $month_num => $month_events): ?>
                    <div class="month-group">
                        <h2 class="month-header"><?php echo htmlspecialchars($months[$month_num]) . ' ' . htmlspecialchars($year); ?></h2>
                        <div class="events-grid">
                            <?php foreach ($month_events as $event): ?>
                                <?php
                                    $available = $event['capacity'] - $event['registered_count'];
                                    $capacityPercentage = $event['capacity'] > 0 ? ($event['registered_count'] / $event['capacity']) * 100 : 0;
                                    $isRegistered = $event['user_registered'] > 0;
                                    $weekday_en = date('l', strtotime($event['date']));
                                    $weekday_de = $weekdays[$weekday_en] ?? $weekday_en;

                                    if ($available <= 0) {
                                        $spots_class = 'spots-ausgebucht';
                                        $spots_text = '🔴 AUSGEBUCHT';
                                    } elseif ($available <= $event['capacity'] * 0.2) {
                                        $spots_class = 'spots-wenig';
                                        $spots_text = '⚠️ NUR NOCH ' . $available;
                                    } else {
                                        $spots_class = 'spots-verfuegbar';
                                        $spots_text = '🟢 ' . $available . ' FREI';
                                    }

                                    $event_image = !empty($event['image']) ? $event['image'] : 'img/festival-default.jpg';
                                ?>
                                <div class="event-card" data-title="<?php echo strtolower(htmlspecialchars($event['title'])); ?>" data-year="<?php echo htmlspecialchars(date('Y', strtotime($event['date']))); ?>">
                                    <div class="card-image">
                                        <img src="<?php echo htmlspecialchars($event_image); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                                        <div class="card-overlay"></div>
                                        <div class="status-badge status-upcoming">🔥 BEVORSTEHEND</div>
                                        <div class="capacity-badge <?php echo $spots_class; ?>"><?php echo $spots_text; ?></div>
                                        <div class="date-badge"><?php echo htmlspecialchars(date('d.m.Y', strtotime($event['date']))); ?></div>
                                    </div>
                                    <div class="card-content">
                                        <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
                                        <div class="event-date">📅 <?php echo htmlspecialchars($weekday_de . ', ' . date('d.m.Y - H:i', strtotime($event['date']))); ?></div>
                                        <div class="event-location">📍 <?php echo htmlspecialchars($event['location']); ?></div>
                                        <div class="organizer-info">🎪 Veranstaltet von <?php echo htmlspecialchars($event['organizer_name']); ?></div>
                                        <div class="capacity-info">
                                            <span>🎟️ <?php echo htmlspecialchars($event['registered_count']); ?> / <?php echo htmlspecialchars($event['capacity']); ?> angemeldet</span>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?php echo htmlspecialchars($capacityPercentage); ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="event-buttons">
                                            <?php if ($isRegistered): ?>
                                                <button class="btn-book angemeldet" disabled>BEREITS ANGEMELDET</button>
                                            <?php elseif (!isOrganizer() && $available > 0): ?>
                                                <a href="api/register_event.php?event_id=<?php echo htmlspecialchars($event['id']); ?>" class="btn-book" onclick="return confirm('Möchtest du dich für \'<?php echo addslashes(htmlspecialchars($event['title'])); ?>\' anmelden?')">🎫 ANMELDEN</a>
                                            <?php elseif (!isOrganizer() && $available <= 0): ?>
                                                <button class="btn-book deaktiviert" disabled>AUSGEBUCHT</button>
                                            <?php else: ?>
                                                <a href="dashboard.php" class="btn-book">EVENT VERWALTEN</a>
                                            <?php endif; ?>
                                            
  
                                            <a href="event_details.php?id=<?php echo htmlspecialchars($event['id']); ?>" class="btn-details">
                                                📖 DETAILS
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
