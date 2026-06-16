<?php
// app/models/User.php
require_once __DIR__ . '/../config/db.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail($email) {
        $db = Database::getInstance();
        
        // Fetch directly from users table and ignore the old roles table completely
        $stmt = $db->prepare("SELECT *, role AS role_name FROM users WHERE email = :email AND deleted_at IS NULL");
        $stmt->execute(['email' => $email]);
        
        return $stmt->fetch();
    }

    public function updateLastLogin($userId) {
        $query = "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>