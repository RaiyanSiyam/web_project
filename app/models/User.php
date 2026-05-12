<?php
// app/models/User.php
require_once __DIR__ . '/../config/db.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail($email) {
        // Only fetch active users who haven't been soft-deleted
        $query = "SELECT u.*, r.role_name 
                  FROM users u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE u.email = :email 
                  AND u.status = 'active' 
                  AND u.deleted_at IS NULL 
                  LIMIT 1";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
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