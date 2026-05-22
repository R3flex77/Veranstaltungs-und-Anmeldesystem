<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Prüfen, ob Benutzer eingeloggt ist
if (!isLoggedIn()) {
    redirect('login.php');
}

// Prüfen, ob Benutzer ein Veranstalter ist
if (!isOrganizer()) {
    redirect('index.php');
}

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Event-ID aus URL holen
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id <= 0) {
    redirect('dashboard.php');
}

// Event abrufen und prüfen ob es dem Benutzer gehört
$query = "SELECT * FROM events WHERE id = :id AND organizer_id = :organizer_id";
$stmt = $db->prepare($query);
$stmt->execute([':id' => $event_id, ':organizer_id' => $user_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    redirect('dashboard.php');
}

// Prüfen ob Event bereits vergangen ist
$is_past = strtotime($event['date']) <= time();
if ($is_past) {
    redirect('dashboard.php?error=Vergangene%20Events%20k%C3%B6nnen%20nicht%20bearbeitet%20werden.');
}

// Bild-Upload Einstellungen
$target_dir = "uploads/events/";
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
$max_file_size = 5 * 1024 * 1024; // 5MB

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = trim($_POST['location']);
    $capacity = (int)$_POST['capacity'];
    $image_path = $event['image'];
    $delete_image = isset($_POST['delete_image']) ? true : false;
    
    if ($delete_image && !empty($event['image']) && file_exists($event['image'])) {
        unlink($event['image']);
        $image_path = '';
    }
    
    if (empty($title)) {
        $error = 'Bitte geben Sie einen Titel ein.';
    } elseif (empty($date)) {
        $error = 'Bitte wählen Sie ein Datum aus.';
    } elseif (empty($time)) {
        $error = 'Bitte wählen Sie eine Uhrzeit aus.';
    } elseif (empty($location)) {
        $error = 'Bitte geben Sie einen Ort ein.';
    } elseif ($capacity < 1) {
        $error = 'Die Kapazität muss mindestens 1 betragen.';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && !$delete_image) {
            $file = $_FILES['image'];
            $file_type = mime_content_type($file['tmp_name']);
            $file_size = $file['size'];
            
            if (!in_array($file_type, $allowed_types)) {
                $error = 'Nur JPG, PNG und WEBP Bilder sind erlaubt.';
            } elseif ($file_size > $max_file_size) {
                $error = 'Das Bild ist zu groß. Maximal 5MB sind erlaubt.';
            } else {
                if (!empty($event['image']) && file_exists($event['image'])) {
                    unlink($event['image']);
                }
                
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $file_extension;
                $image_path = $target_dir . $filename;
                
                if (!move_uploaded_file($file['tmp_name'], $image_path)) {
                    $error = 'Fehler beim Hochladen des Bildes.';
                }
            }
        }
        
        if (empty($error)) {
            $datetime = $date . ' ' . $time;
            
            if (strtotime($datetime) <= time()) {
                $error = 'Das Event-Datum muss in der Zukunft liegen.';
            } else {
                if ($capacity < $event['capacity']) {
                    $reg_query = "SELECT COUNT(*) as count FROM registrations WHERE event_id = :event_id";
                    $reg_stmt = $db->prepare($reg_query);
                    $reg_stmt->execute([':event_id' => $event_id]);
                    $registered_count = $reg_stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    
                    if ($capacity < $registered_count) {
                        $error = "Die neue Kapazität ($capacity) ist kleiner als die Anzahl bereits registrierter Teilnehmer ($registered_count). Bitte erhöhen Sie die Kapazität oder kontaktieren Sie die Teilnehmer.";
                    }
                }
                
                if (empty($error)) {
                    try {
                        $update_query = "UPDATE events \
                                        SET title = :title, \
                                            description = :description, \
                                            date = :date, \
                                            location = :location, \
                                            capacity = :capacity, \
                                            image = :image\
                                        WHERE id = :id AND organizer_id = :organizer_id";
                        $update_stmt = $db->prepare($update_query);
                        $update_stmt->execute([
                            ':title' => $title,
                            ':description' => $description,
                            ':date' => $datetime,
                            ':location' => $location,
                            ':capacity' => $capacity,
                            ':image' => $image_path,
                            ':id' => $event_id,
                            ':organizer_id' => $user_id
                        ]);
                        
                        $success = 'Event erfolgreich aktualisiert!';
                        
                        $event['title'] = $title;
                        $event['description'] = $description;
                        $event['date'] = $datetime;
                        $event['location'] = $location;
                        $event['capacity'] = $capacity;
                        $event['image'] = $image_path;
                    } catch (PDOException $e) {
                        $error = 'Fehler beim Aktualisieren des Events: ' . $e->getMessage();
                        if ($image_path !== $event['image'] && !empty($image_path) && file_exists($image_path)) {
                            unlink($image_path);
                        }
                    }
                }
            }
        }
    }
}

$event_date = date('Y-m-d', strtotime($event['date']));
$event_time = date('H:i', strtotime($event['date']));
$min_date = date('Y-m-d');
$current_time = date('H:i');

renderHeader('Event bearbeiten - ' . htmlspecialchars($event['title']), '', '/css/edit_event.css');
require_once 'includes/edit_event_page.php';
require_once 'includes/footer.php';
renderFooter();

