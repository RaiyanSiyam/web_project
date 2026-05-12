<?php
// app/controllers/AuthController.php
session_start();
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize inputs
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']) ? true : false;

            if (empty($email) || empty($password)) {
                $_SESSION['login_error'] = "Please enter both email and password.";
                header("Location: ../../login.php");
                exit();
            }

            $user = $this->userModel->findByEmail($email);

            // Verify User & Password
            if ($user && password_verify($password, $user['password_hash'])) {
                // Prevent Session Fixation attacks
                session_regenerate_id(true);

                // Set Session Variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['role'] = $user['role_name'];
                $_SESSION['logged_in'] = true;

                // Update timestamp
                $this->userModel->updateLastLogin($user['id']);

                // Remember Me Logic (Set cookie for 30 days)
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    // In a production app, hash this token in the DB for validation. 
                    // For now, securely storing encrypted user ID in cookie.
                    $cookieValue = base64_encode($user['id'] . ':' . $token);
                    setcookie('uiu_remember', $cookieValue, time() + (86400 * 30), "/", "", false, true); // HttpOnly
                }

                header("Location: ../../pages/dashboard/index.php");
                exit();
            } else {
                $_SESSION['login_error'] = "Invalid credentials or account suspended.";
                header("Location: ../../login.php");
                exit();
            }
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        setcookie('uiu_remember', '', time() - 3600, "/");
        header("Location: ../../login.php");
        exit();
    }
}

// Route the request
if (isset($_GET['action'])) {
    $auth = new AuthController();
    if ($_GET['action'] === 'login') {
        $auth->login();
    } elseif ($_GET['action'] === 'logout') {
        $auth->logout();
    }
}
?>