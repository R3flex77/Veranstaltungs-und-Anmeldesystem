<?php

require_once __DIR__ . '/db.php';

class RegistrationHandler {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
// Verarbeitung der Registrierung
    public function handleRegistration($username, $password, $role = 'user') {
        // Validierung
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'error' => 'Benutzername und Passwort sind erforderlich',
                'message' => null
            ];
        }
        
        if (strlen($password) < 6) {
            return [
                'success' => false,
                'error' => 'Das Passwort muss mindestens 6 Zeichen lang sein',
                'message' => null
            ];
        }
        
        if (!in_array($role, ['user', 'organizer'])) {
            $role = 'user';
        }
        
        // Passwort hashen
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // In Datenbank speichern
        try {
            $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashed_password,
                ':role' => $role
            ]);
            
            return [
                'success' => true,
                'error' => null,
                'message' => 'Registrierung erfolgreich! Sie können sich jetzt Anmelden.'
            ];
        } catch (PDOException $e) {
            // Duplicate entry error
            if ($e->getCode() == 23000) {
                return [
                    'success' => false,
                    'error' => 'Dieser Benutzername ist bereits vergeben.',
                    'message' => null
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Fehler bei der Registrierung: ' . $e->getMessage(),
                'message' => null
            ];
        }
    }
}
?>
