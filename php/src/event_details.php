<?php
require_once 'includes/db.php';
require_once 'includes/header.php';
require_once 'includes/footer.php';

// Prüfen, ob Benutzer eingeloggt ist
if (!isLoggedIn()) {
    redirect('login.php');
}

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Event-ID aus URL holen
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id <= 0) {
    redirect('index.php');
}

// Event Details abrufen
$query = "SELECT e.*, u.username as organizer_name 
          FROM events e
          JOIN users u ON e.organizer_id = u.id
          WHERE e.id = :id";
$stmt = $db->prepare($query);
$stmt->execute([':id' => $event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    redirect('index.php');
}

// Prüfen ob Benutzer bereits registriert ist
$check_reg_query = "SELECT id FROM registrations WHERE event_id = :event_id AND user_id = :user_id";
$check_reg_stmt = $db->prepare($check_reg_query);
$check_reg_stmt->execute([':event_id' => $event_id, ':user_id' => $user_id]);
$is_registered = $check_reg_stmt->rowCount() > 0;

// Aktuelle Anzahl der Registrierungen
$reg_query = "SELECT COUNT(*) as count FROM registrations WHERE event_id = :event_id";
$reg_stmt = $db->prepare($reg_query);
$reg_stmt->execute([':event_id' => $event_id]);
$registered_count = $reg_stmt->fetch(PDO::FETCH_ASSOC)['count'];

$available_spots = $event['capacity'] - $registered_count;
$is_upcoming = strtotime($event['date']) > time();
$is_full = $available_spots <= 0;
$can_register = $is_upcoming && !$is_full && !$is_registered && $event['organizer_id'] != $user_id;

// Registrierung verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'register' && $can_register) {
        try {
            $insert_query = "INSERT INTO registrations (event_id, user_id, registered_at) VALUES (:event_id, :user_id, NOW())";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->execute([':event_id' => $event_id, ':user_id' => $user_id]);
            
            // Aktualisiere Zähler
            $registered_count++;
            $available_spots = $event['capacity'] - $registered_count;
            $is_full = $available_spots <= 0;
            $can_register = false;
            $is_registered = true;
            
            $success = 'Sie wurden erfolgreich für dieses Event registriert!';
        } catch (PDOException $e) {
            $error = 'Fehler bei der Registrierung. Möglicherweise sind Sie bereits registriert.';
        }
    } 
    elseif ($_POST['action'] === 'unregister' && $is_registered && $is_upcoming) {
        try {
            $delete_query = "DELETE FROM registrations WHERE event_id = :event_id AND user_id = :user_id";
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->execute([':event_id' => $event_id, ':user_id' => $user_id]);
            
            // Aktualisiere Zähler
            $registered_count--;
            $available_spots = $event['capacity'] - $registered_count;
            $is_full = $available_spots <= 0;
            $can_register = !$is_full && $is_upcoming;
            $is_registered = false;
            
            $success = 'Sie wurden erfolgreich von diesem Event abgemeldet.';
        } catch (PDOException $e) {
            $error = 'Fehler bei der Abmeldung.';
        }
    }
}

// Hole alle registrierten Teilnehmer (für Veranstalter)
$participants = [];
if ($event['organizer_id'] == $user_id) {
    // Wähle `username` statt `email`, da andere Teile des Codes `username` erwarten
    $part_query = "SELECT u.id, u.username, r.registered_at 
                   FROM registrations r
                   JOIN users u ON r.user_id = u.id
                   WHERE r.event_id = :event_id
                   ORDER BY r.registered_at ASC";
    $part_stmt = $db->prepare($part_query);
    $part_stmt->execute([':event_id' => $event_id]);
    $participants = $part_stmt->fetchAll(PDO::FETCH_ASSOC);
}


renderHeader($event['title'] . ' - Event-Details', 'my_events', '/css/event_details.css');
?>


<section class="hero-details <?php echo !empty($event['image']) && file_exists($event['image']) ? 'has-image' : ''; ?>">
    <div class="hero-content">
        <span class="hero-badge <?php echo $is_upcoming ? 'badge-upcoming' : 'badge-past'; ?>">
            <?php echo $is_upcoming ? '🟢 BEVORSTEHEND' : '⚪ BEENDET'; ?>
        </span>
        <h1><?php echo htmlspecialchars($event['title']); ?></h1>
        <div class="hero-meta">
            <span><i class="fas fa-calendar-alt"></i> <?php echo date('d.m.Y', strtotime($event['date'])); ?></span>
            <span><i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($event['date'])); ?> Uhr</span>
            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></span>
            <span><i class="fas fa-user"></i> Veranstalter: <?php echo htmlspecialchars($event['organizer_name']); ?></span>
        </div>
    </div>
</section>

<div class="container">
    <div class="back-button">
        <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i> Zurück</a>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">❌ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="details-grid">
        <div class="main-content">
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-align-left"></i> Beschreibung</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($event['description'])): ?>
                        <div class="description-text">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #888;">Keine Beschreibung verfügbar.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($event['organizer_id'] == $user_id): ?>
                <div class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-users"></i> Teilnehmerliste</h2>
                    </div>
                    <div class="card-body">
                        <?php if (count($participants) === 0): ?>
                            <p style="color: #888; text-align: center; padding: 30px;">
                                <i class="fas fa-user-slash" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                                Noch keine Teilnehmer registriert.
                            </p>
                        <?php else: ?>
                            <div class="participants-table-wrapper">
                                <table class="participants-table">
                                    <thead>
                                        <tr>
                                            <th>BENUTZERNAME</th>
                                            <th>REGISTRIERT AM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($participants as $participant): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($participant['username']); ?></td>
                                                <td><?php echo date('d.m.Y H:i', strtotime($participant['registered_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="info-sidebar">
            <div class="info-card">
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="info-content">
                        <div class="info-label">Datum & Uhrzeit</div>
                        <div class="info-value"><?php echo date('d.m.Y', strtotime($event['date'])); ?></div>
                        <div class="info-value" style="font-size: 14px; color: #aaa;"><?php echo date('H:i', strtotime($event['date'])); ?> Uhr</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-content">
                        <div class="info-label">Veranstaltungsort</div>
                        <div class="info-value"><?php echo htmlspecialchars($event['location']); ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-user-circle"></i></div>
                    <div class="info-content">
                        <div class="info-label">Veranstalter</div>
                        <div class="info-value"><?php echo htmlspecialchars($event['organizer_name']); ?></div>
                    </div>
                </div>
                
                <div class="capacity-bar-container">
                    <div class="capacity-stats">
                        <span><i class="fas fa-users"></i> Teilnehmer</span>
                        <span><?php echo $registered_count; ?> / <?php echo $event['capacity']; ?></span>
                    </div>
                    <div class="capacity-bar">
                        <div class="capacity-fill" style="width: <?php echo ($registered_count / $event['capacity']) * 100; ?>%"></div>
                    </div>
                    <?php if ($available_spots > 0 && $available_spots <= ($event['capacity'] * 0.2)): ?>
                        <div class="spots-warning">
                            <i class="fas fa-exclamation-triangle"></i> Nur noch <?php echo $available_spots; ?> Plätze verfügbar!
                        </div>
                    <?php elseif ($available_spots == 0): ?>
                        <div class="spots-warning" style="color: #dc3545;">
                            <i class="fas fa-ban"></i> Event ausgebucht!
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="action-buttons">
                    <?php if ($event['organizer_id'] == $user_id): ?>
                        <?php if ($is_upcoming): ?>
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> Event bearbeiten
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($is_upcoming): ?>
                            <?php if ($is_registered): ?>
                                <button onclick="showUnregisterModal()" class="btn-unregister">
                                    <i class="fas fa-times-circle"></i> Abmelden
                                </button>
                            <?php elseif ($available_spots > 0): ?>
                                <form method="POST" action="" style="width: 100%;">
                                    <input type="hidden" name="action" value="register">
                                    <button type="submit" class="btn-register">
                                        <i class="fas fa-ticket-alt"></i> Jetzt anmelden
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn-disabled" disabled>
                                    <i class="fas fa-ban"></i> Ausgebucht
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn-disabled" disabled>
                                <i class="fas fa-hourglass-end"></i> Event bereits beendet
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="unregisterModal" class="modal">
    <div class="modal-content">
        <h3>⚠️ VOM EVENT ABMELDEN</h3>
        <p>Möchten Sie sich wirklich von diesem Event abmelden?</p>
        <p style="font-size: 12px; color: #ffc107;">Ihr Platz wird dann sofort für andere Teilnehmer freigegeben.</p>
        <div class="modal-buttons">
            <form method="POST" action="" style="display: inline;">
                <input type="hidden" name="action" value="unregister">
                <button type="submit" class="modal-btn modal-confirm">JA, ABMELDEN</button>
            </form>
            <button onclick="hideUnregisterModal()" class="modal-btn modal-cancel">ABBRECHEN</button>
        </div>
    </div>
</div>

<script>
    function showUnregisterModal() {
        document.getElementById('unregisterModal').classList.add('active');
    }
    
    function hideUnregisterModal() {
        document.getElementById('unregisterModal').classList.remove('active');
    }
    
    window.onclick = function(event) {
        const modal = document.getElementById('unregisterModal');
        if (event.target === modal) {
            hideUnregisterModal();
        }
    }
</script>

<?php
renderFooter();
?>
