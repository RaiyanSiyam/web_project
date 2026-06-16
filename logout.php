$db = Database::getInstance();
$db->prepare("UPDATE users SET last_activity = NULL WHERE id = :id")->execute(['id' => $_SESSION['user_id']]);
session_destroy();