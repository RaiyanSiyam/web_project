<?php
// app/controllers/AuthController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    private $db;

    public function __construct() {
        $this->userModel = new User();
        $this->db = Database::getInstance();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']) ? true : false;

            if (empty($email) || empty($password)) {
                $_SESSION['login_error'] = "Please enter both email and password.";
                header("Location: ../../login.php");
                exit();
            }

            $user = $this->userModel->findByEmail($email);

            if ($user && (password_verify($password, $user['password_hash']) || password_verify($password, $user['password']))) {
                
                // FIX: Prioritize the original role_name column over the new fallback column
                $rawRole = 'cashier';
                if (!empty($user['role_name'])) {
                    $rawRole = $user['role_name'];
                } elseif (!empty($user['role'])) {
                    $rawRole = $user['role'];
                }
                
                $assignedRole = strtolower(trim($rawRole)); // Ensures 'Admin' safely normalizes to 'admin'

                // STAGE 1: ENFORCE MAXIMUM 4 CONCURRENT CASHIERS LIMIT
                if ($assignedRole === 'cashier') {
                    $stmt = $this->db->query("SELECT COUNT(*) as active_cashiers FROM users WHERE role = 'cashier' AND last_activity > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
                    $result = $stmt->fetch();
                    
                    if ($result['active_cashiers'] >= 4) {
                        $_SESSION['login_error'] = "Login Denied: All 4 cash counters are currently occupied.";
                        header("Location: ../../login.php");
                        exit();
                    }
                }

                // STAGE 2: AUTO-DETECT USER NAME VARIATIONS
                if (!empty($user['first_name']) || !empty($user['last_name'])) {
                    $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                } else {
                    $fullName = $user['username'] ?? 'System User';
                }

                // STAGE 3: BIND RECONCILED VARIABLES TO SESSION
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $fullName;
                $_SESSION['user_name'] = $fullName; 
                $_SESSION['role'] = $assignedRole;   // Clean lowercase string rule ('admin', 'manager', 'cashier')
                $_SESSION['logged_in'] = true;

                // Update system heartbeat
                $updateStmt = $this->db->prepare("UPDATE users SET last_activity = NOW() WHERE id = :id");
                $updateStmt->execute(['id' => $user['id']]);

                if (method_exists($this->userModel, 'updateLastLogin')) {
                    $this->userModel->updateLastLogin($user['id']);
                }

                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $cookieValue = base64_encode($user['id'] . ':' . $token);
                    setcookie('uiu_remember', $cookieValue, time() + (86400 * 30), "/", "", false, true);
                }

                // STAGE 4: PRIVILEGE PATHWAY REDIRECTION
                if ($assignedRole === 'cashier') {
                    header("Location: ../../pages/billing/index.php");
                } else {
                    header("Location: ../../pages/dashboard/index.php");
                }
                exit();

            } else {
                $_SESSION['login_error'] = "Invalid credentials or account suspended.";
                header("Location: ../../login.php");
                exit();
            }
        }
    }

    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $updateStmt = $this->db->prepare("UPDATE users SET last_activity = NULL WHERE id = :id");
            $updateStmt->execute(['id' => $_SESSION['user_id']]);
        }
        session_unset();
        session_destroy();
        if (isset($_COOKIE['uiu_remember'])) {
            setcookie('uiu_remember', '', time() - 3600, "/");
        }
        header("Location: ../../login.php");
        exit();
    }
}

if (isset($_GET['action'])) {
    $auth = new AuthController();
    if ($_GET['action'] === 'login') {
        $auth->login();
    } elseif ($_GET['action'] === 'logout') {
        $auth->logout();
    }
}
?>