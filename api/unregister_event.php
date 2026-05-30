<?php
require_once '../includes/db.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = "Bitte melde dich zuerst an.";
    redirect('../login.php');
}

if (!isset($_GET['registration_id']) || empty($_GET['registration_id'])) {
    $_SESSION['error'] = "Ungültige Anfrage.";
    redirect('../my_events.php');
}

$registration_id = (int)$_GET['registration_id'];
$user_id = $_SESSION['user_id'];

$database = new Database();
$db = $database->getConnection();

try {
    // Prüfen ob die Registrierung dem Benutzer gehört
    $checkQuery = "SELECT r.id, e.title, e.date 
                   FROM registrations r 
                   JOIN events e ON r.event_id = e.id 
                   WHERE r.id = :registration_id AND r.user_id = :user_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([
        ':registration_id' => $registration_id,
        ':user_id' => $user_id
    ]);
    $registration = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$registration) {
        $_SESSION['error'] = "Registrierung nicht gefunden.";
        redirect('../my_events.php');
    }
    
    // Prüfen ob Event schon vorbei ist
    if (strtotime($registration['date']) < time()) {
        $_SESSION['error'] = "Dieses Event ist bereits vorbei, eine Abmeldung ist nicht mehr möglich.";
        redirect('../my_events.php');
    }
    
    // Abmelden (Löschen der Registrierung)
    $deleteQuery = "DELETE FROM registrations WHERE id = :registration_id AND user_id = :user_id";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->execute([
        ':registration_id' => $registration_id,
        ':user_id' => $user_id
    ]);
    
    $_SESSION['success'] = "✓ Du wurdest erfolgreich von '" . htmlspecialchars($registration['title']) . "' abgemeldet.";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Ein Fehler ist aufgetreten. Bitte versuche es später erneut.";
}

redirect('../my_events.php');
?>
