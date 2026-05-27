<?php

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Event löschen
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $event_id = $_GET['delete'];
    
    // Hole Bildpfad vor dem Löschen
    $img_query = "SELECT image FROM events WHERE id = :id AND organizer_id = :organizer_id";
    $img_stmt = $db->prepare($img_query);
    $img_stmt->execute([':id' => $event_id, ':organizer_id' => $user_id]);
    $event_img = $img_stmt->fetch(PDO::FETCH_ASSOC);
    
    try {
        // Prüfen ob der Benutzer der Besitzer des Events ist
        $check_query = "SELECT id FROM events WHERE id = :id AND organizer_id = :organizer_id";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([':id' => $event_id, ':organizer_id' => $user_id]);
        
        if ($check_stmt->rowCount() > 0) {
            // Lösche Bilddatei wenn vorhanden
            if ($event_img && !empty($event_img['image']) && file_exists($event_img['image'])) {
                unlink($event_img['image']);
            }
            
            $delete_query = "DELETE FROM events WHERE id = :id";
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->execute([':id' => $event_id]);
            $success = 'Event erfolgreich gelöscht!';
        } else {
            $error = 'Sie haben keine Berechtigung, dieses Event zu löschen.';
        }
    } catch (PDOException $e) {
        $error = 'Fehler beim Löschen des Events.';
    }
}

// Meine Events abrufen (vergangene und zukünftige)
$query = "SELECT e.*, 
          (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as registered_count
          FROM events e
          WHERE e.organizer_id = :organizer_id
          ORDER BY e.date ASC";

$stmt = $db->prepare($query);
$stmt->execute([':organizer_id' => $user_id]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$weekdays = [
    'Monday' => 'Montag', 'Tuesday' => 'Dienstag', 'Wednesday' => 'Mittwoch',
    'Thursday' => 'Donnerstag', 'Friday' => 'Freitag', 'Saturday' => 'Samstag', 'Sunday' => 'Sonntag'
];

// Statistik berechnen
$total_events = count($events);
$upcoming_events = 0;
$past_events = 0;
$total_participants = 0;

foreach ($events as $event) {
    if (strtotime($event['date']) > time()) {
        $upcoming_events++;
    } else {
        $past_events++;
    }
    $total_participants += $event['registered_count'];
}
?>

<section class="hero-small">
    <div class="hero-content">
        <h1>VERANSTALTER DASHBOARD</h1>
        <p>Übersicht und Verwaltung deiner Events</p>
    </div>
</section>

<div class="container">
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Statistik Karten -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-value"><?php echo $total_events; ?></div>
            <div class="stat-label">GESAMT EVENTS</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-value"><?php echo $upcoming_events; ?></div>
            <div class="stat-label">BEVORSTEHEND</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-history"></i></div>
            <div class="stat-value"><?php echo $past_events; ?></div>
            <div class="stat-label">VERGANGEN</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-value"><?php echo $total_participants; ?></div>
            <div class="stat-label">TEILNEHMER</div>
        </div>
    </div>


    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-list-ul"></i> MEINE EVENTS
        </h2>
        <a href="create_event.php" class="create-btn"><i class="fas fa-plus-circle"></i> NEUES EVENT ERSTELLEN</a>
    </div>

    <?php if (count($events) > 0): ?>
        <div class="events-grid">
            <?php foreach ($events as $event): 
                $isPast = strtotime($event['date']) < time();
                $weekday_en = date('l', strtotime($event['date']));
                $weekday_de = $weekdays[$weekday_en] ?? $weekday_en;
                $event_image = !empty($event['image']) ? $event['image'] : 'img/festival-default.jpg';
                $available = $event['capacity'] - $event['registered_count'];
                
                $spots_text = '';
                $spots_icon = '';
                if ($available <= 0) {
                    $spots_text = 'AUSGEBUCHT';
                    $spots_icon = '<i class="fas fa-ban"></i>';
                } elseif ($available <= $event['capacity'] * 0.2) {
                    $spots_text = 'NUR NOCH ' . $available . ' PLÄTZE';
                    $spots_icon = '<i class="fas fa-exclamation-triangle"></i>';
                } else {
                    $spots_text = $available . ' PLÄTZE FREI';
                    $spots_icon = '<i class="fas fa-check-circle"></i>';
                }
            ?>
                <div class="event-card">
                    <div class="card-image">
                        <img src="<?php echo $event_image; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                        <div class="card-overlay"></div>
                        <div class="status-badge <?php echo $isPast ? 'status-past' : 'status-upcoming'; ?>">
                            <?php echo $isPast ? '<i class="fas fa-calendar-times"></i> ABGELAUFEN' : '<i class="fas fa-fire"></i> BEVORSTEHEND'; ?>
                        </div>
                        <div class="capacity-badge">
                            <?php echo $spots_icon . ' ' . $spots_text; ?>
                        </div>
                        <div class="date-badge">
                            <i class="fas fa-calendar-day"></i> <?php echo date('d.m.Y', strtotime($event['date'])); ?>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
                        <div class="event-date">
                            <i class="fas fa-calendar-alt"></i> <?php echo $weekday_de . ', ' . date('d.m.Y - H:i', strtotime($event['date'])); ?>
                        </div>
                        <div class="event-location">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?>
                        </div>
                        <div class="event-stats">
                            <span><i class="fas fa-users"></i> <?php echo $event['registered_count']; ?> / <?php echo $event['capacity']; ?> Teilnehmer</span>
                            <span><?php echo round(($event['registered_count'] / $event['capacity']) * 100); ?>%</span>
                        </div>
                        <?php if (!$isPast): ?>
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn-edit-event">
                                <i class="fas fa-edit"></i> EVENT BEARBEITEN
                            </a>
                            <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn-edit-event">
                                <i class="fas fa-info-circle"></i> EVENT DETAILS
                            </a>
                            <button onclick="showDeleteModal(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars($event['title']); ?>')" class="btn-delete-event">
                                <i class="fas fa-trash-alt"></i> EVENT LÖSCHEN
                            </button>
                        <?php else: ?>
                            <a href="event_details.php?id=<?php echo $event['id']; ?>" class="btn-edit-event">
                                <i class="fas fa-info-circle"></i> EVENT DETAILS
                            </a>
                            <div class="btn-disabled">
                                <i class="fas fa-hourglass-end"></i> EVENT VORBEI
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-events">
            <div class="icon"><i class="fas fa-calendar-times"></i></div>
            <h3>Keine Events erstellt</h3>
            <p>Du hast noch keine Veranstaltungen erstellt.</p>
            <a href="create_event.php" class="btn-browse"><i class="fas fa-plus-circle"></i> JETZT DEIN ERSTES EVENT ERSTELLEN</a>
        </div>
    <?php endif; ?>
</div>


<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3><i class="fas fa-exclamation-triangle"></i> EVENT LÖSCHEN</h3>
        <p id="deleteMessage">Möchten Sie dieses Event wirklich löschen?</p>
        <p style="font-size: 12px; color: #ff6b6b;"><i class="fas fa-ban"></i> Diese Aktion kann nicht rückgängig gemacht werden!</p>
        <div class="modal-buttons">
            <button onclick="confirmDelete()" class="modal-btn modal-confirm"><i class="fas fa-check-circle"></i> JA, LÖSCHEN</button>
            <button onclick="hideDeleteModal()" class="modal-btn modal-cancel"><i class="fas fa-times-circle"></i> ABBRECHEN</button>
        </div>
    </div>
</div>

<?php
?>
