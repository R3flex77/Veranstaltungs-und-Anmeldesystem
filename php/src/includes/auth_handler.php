<?php

require_once __DIR__ . '/db.php';

class AuthHandler {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
// Verarbeitung Login Formular
    public function handleLogin($username, $password) {
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'error' => 'Benutzername und Passwort sind erforderlich'
            ];
        }
        
        $user = $this->getUserByUsername($username);
        
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Falscher Benutzername oder Passwort'
            ];
        }
        
        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'error' => 'Falscher Benutzername oder Passwort'
            ];
        }
        
        // Erfolgreich Login
        $this->setUserSession($user);
        return ['success' => true];
    }
    
// Holt Benutzer aus Datenbank anhand des Benutzernamens
    private function getUserByUsername($username) {
        $query = "SELECT id, username, password, role FROM users WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
// Setzt die Session-Variablen für den eingeloggten Benutzer
    private function setUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    }
}
?>
