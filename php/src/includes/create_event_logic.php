<?php
require_once 'db.php';

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

$error = '';
$success = '';

// Bild-Upload Einstellungen
$target_dir = "uploads/events/";
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
$max_file_size = 5 * 1024 * 1024; // 5MB

// Erstellt Verzeichnis falls nicht vorhanden
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = trim($_POST['location']);
    $capacity = (int)$_POST['capacity'];
    $organizer_id = $_SESSION['user_id'];
    $image_path = '';
    
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
        // Bild-Upload verarbeiten
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $file_type = mime_content_type($file['tmp_name']);
            $file_size = $file['size'];
            
            // Prüfe Dateityp
            if (!in_array($file_type, $allowed_types)) {
                $error = 'Nur JPG, PNG und WEBP Bilder sind erlaubt.';
            }
            // Prüfe Dateigröße
            elseif ($file_size > $max_file_size) {
                $error = 'Das Bild ist zu groß. Maximal 5MB sind erlaubt.';
            }
            else {
                // Generiere eindeutigen Dateinamen
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $file_extension;
                $image_path = $target_dir . $filename;
                
                // Verschiebe hochgeladenes Bild
                if (!move_uploaded_file($file['tmp_name'], $image_path)) {
                    $error = 'Fehler beim Hochladen des Bildes.';
                }
            }
        }
        
        // Wenn kein Fehler beim Bild-Upload, erstelle Event
        if (empty($error)) {
            // Datum und Uhrzeit kombinieren
            $datetime = $date . ' ' . $time;
            
            // Prüfen ob Datum in der Zukunft liegt
            if (strtotime($datetime) <= time()) {
                $error = 'Das Event-Datum muss in der Zukunft liegen.';
            } else {
                try {
                    $query = "INSERT INTO events (title, description, date, location, capacity, organizer_id, image) 
                              VALUES (:title, :description, :date, :location, :capacity, :organizer_id, :image)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([
                        ':title' => $title,
                        ':description' => $description,
                        ':date' => $datetime,
                        ':location' => $location,
                        ':capacity' => $capacity,
                        ':organizer_id' => $organizer_id,
                        ':image' => $image_path
                    ]);
                    
                    $success = 'Event erfolgreich erstellt!';
                    
                    // Formular zurücksetzen
                    $_POST = array();
                } catch (PDOException $e) {
                    $error = 'Fehler beim Erstellen des Events: ' . $e->getMessage();
                    // Lösche Bild falls Event nicht erstellt werden konnte
                    if (!empty($image_path) && file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            }
        }
    }
}

// Aktuelles Datum
$min_date = date('Y-m-d');
$current_time = date('H:i');
