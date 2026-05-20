<?php

// Wird ebenfalls für Authentifzierung verwendet - Je nach eingeloggtem Benutzer wird der Zugriff festgelegt

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isOrganizer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'organizer';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// Datenbank Zugriff über Root
class Database {
    private $host = "mysql"; 
    private $db_name = "event_system";
    private $port = 3306;
    public $conn;

    public function getConnection() {
        $this->conn = null;
        $username = "root";
        $password = getenv('MYSQL_ROOT_PASSWORD') ?: 'root123';
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8",
                $username,
                $password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }
        return $this->conn;
    }
}
?>
