<?php
require_once '../includes/db.php';


// Prüfen ob eingeloggt
if (!isLoggedIn()) {
    $_SESSION['error'] = "Bitte melde dich zuerst an.";
    redirect('../login.php');
}

// Prüfen ob event_id übergeben wurde
if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {
    $_SESSION['error'] = "Kein Event ausgewählt.";
    redirect('../index.php');
}

$event_id = (int)$_GET['event_id'];
$user_id = $_SESSION['user_id'];

$database = new Database();
$db = $database->getConnection();

try {
    // Prüfen ob Benutzer bereits registriert ist
    $checkQuery = "SELECT id FROM registrations WHERE event_id = :event_id AND user_id = :user_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([':event_id' => $event_id, ':user_id' => $user_id]);
    
    if ($checkStmt->fetch()) {
        $_SESSION['error'] = "Du bist bereits für dieses Event registriert!";
        redirect("../index.php");
    }
    
    // Event-Daten abrufen und Kapazität prüfen
    $eventQuery = "SELECT e.*, 
                   (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as registered_count
                   FROM events e 
                   WHERE e.id = :event_id AND e.date > NOW()";
    $eventStmt = $db->prepare($eventQuery);
    $eventStmt->execute([':event_id' => $event_id]);
    $event = $eventStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        $_SESSION['error'] = "Event nicht gefunden oder bereits vorbei.";
        redirect("../index.php");
    }
    
    $available = $event['capacity'] - $event['registered_count'];
    
    if ($available <= 0) {
        $_SESSION['error'] = "Dieses Event ist bereits ausgebucht!";
        redirect("../index.php");
    }
    
    // Buchung durchführen
    $insertQuery = "INSERT INTO registrations (event_id, user_id, registered_at) 
                    VALUES (:event_id, :user_id, NOW())";
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->execute([
        ':event_id' => $event_id,
        ':user_id' => $user_id
    ]);
    
    $_SESSION['success'] = "✓ Erfolgreich für '" . htmlspecialchars($event['title']) . "' gebucht!";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Ein Fehler ist aufgetreten. Bitte versuche es später erneut.";
}

redirect("../index.php");
?>
