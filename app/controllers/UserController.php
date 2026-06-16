<?php
// app/controllers/UserController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

class UserController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
            exit();
        }
    }

    public function index() {
        return $this->db->query("SELECT id, first_name, last_name, email, role, phone, salary, last_activity FROM users ORDER BY role ASC")->fetchAll();
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $firstName = trim($_POST['first_name'] ?? '');
                $lastName = trim($_POST['last_name'] ?? '');
                $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? 'cashier';
                $phone = trim($_POST['phone'] ?? '');
                $salary = !empty($_POST['salary']) ? floatval($_POST['salary']) : null;

                if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($role)) {
                    echo json_encode(['status' => 'error', 'message' => 'Name, email, password, and role are required.']);
                    exit();
                }

                $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                if ($stmt->fetch()) {
                    echo json_encode(['status' => 'error', 'message' => 'Email address is already registered.']);
                    exit();
                }

                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Proactively setting role_id based on string role to prevent missing default value errors
                $roleId = ($role === 'admin') ? 1 : (($role === 'manager') ? 2 : 3);
                $stmt = $this->db->prepare("INSERT INTO users (role_id, first_name, last_name, email, password_hash, role, phone, salary, status) VALUES (:role_id, :first_name, :last_name, :email, :password_hash, :role, :phone, :salary, 'active')");
                
                $success = $stmt->execute([
                    'role_id' => $roleId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password_hash' => $hashedPassword,
                    'role' => $role,
                    'phone' => $phone,
                    'salary' => $salary
                ]);

                echo json_encode(['status' => 'success', 'message' => 'Employee profile created successfully.']);
                exit();

            } catch (PDOException $e) {
                // THIS WILL CATCH THE FATAL ERROR AND ALERT IT TO YOUR SCREEN!
                echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . $e->getMessage()]);
                exit();
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()]);
                exit();
            }
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = intval($_POST['id'] ?? 0);
                $firstName = trim($_POST['first_name'] ?? '');
                $lastName = trim($_POST['last_name'] ?? '');
                $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
                $role = $_POST['role'] ?? 'cashier';
                $phone = trim($_POST['phone'] ?? '');
                $salary = !empty($_POST['salary']) ? floatval($_POST['salary']) : null;
                $password = $_POST['password'] ?? '';

                if (empty($id) || empty($firstName) || empty($email)) {
                    echo json_encode(['status' => 'error', 'message' => 'Required fields missing.']);
                    exit();
                }

                $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
                $stmt->execute(['email' => $email, 'id' => $id]);
                if ($stmt->fetch()) {
                    echo json_encode(['status' => 'error', 'message' => 'Email is already used by another account.']);
                    exit();
                }

                $roleId = ($role === 'admin') ? 1 : (($role === 'manager') ? 2 : 3);
                if (!empty($password)) {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $this->db->prepare("UPDATE users SET role_id = :rid, first_name = :fn, last_name = :ln, email = :em, role = :role, phone = :phone, salary = :salary, password_hash = :pw WHERE id = :id");
                    $success = $stmt->execute(['rid' => $roleId, 'fn' => $firstName, 'ln' => $lastName, 'em' => $email, 'role' => $role, 'phone' => $phone, 'salary' => $salary, 'pw' => $hashedPassword, 'id' => $id]);
                } else {
                    $stmt = $this->db->prepare("UPDATE users SET role_id = :rid, first_name = :fn, last_name = :ln, email = :em, role = :role, phone = :phone, salary = :salary WHERE id = :id");
                    $success = $stmt->execute(['rid' => $roleId, 'fn' => $firstName, 'ln' => $lastName, 'em' => $email, 'role' => $role, 'phone' => $phone, 'salary' => $salary, 'id' => $id]);
                }

                echo json_encode(['status' => 'success', 'message' => 'Employee updated successfully.']);
                exit();

            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . $e->getMessage()]);
                exit();
            }
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = intval($_POST['id'] ?? 0);
                if ($id === intval($_SESSION['user_id'])) {
                    echo json_encode(['status' => 'error', 'message' => 'You cannot delete your own active session.']);
                    exit();
                }

                $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
                $stmt->execute(['id' => $id]);

                echo json_encode(['status' => 'success', 'message' => 'Employee removed from system.']);
                exit();

            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . $e->getMessage()]);
                exit();
            }
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new UserController();
    if ($_GET['action'] === 'create') {
        $controller->create();
    } elseif ($_GET['action'] === 'update') {
        $controller->update();
    } elseif ($_GET['action'] === 'delete') {
        $controller->delete();
    }
}
?>