<?php
// includes/auth_guard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

require_once __DIR__ . '/../app/config/db.php';
$db = Database::getInstance();

// 2. Heartbeat: Update user's last activity timestamp to keep their session slot alive
$stmt = $db->prepare("UPDATE users SET last_activity = NOW() WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);

// 3. Role-Based Access Control (RBAC) URL Protection
$current_page = $_SERVER['SCRIPT_NAME'];
$role = $_SESSION['role'] ?? 'cashier';

if ($role === 'cashier') {
    // Cashiers are restricted strictly to the POS module
    if (strpos($current_page, 'pages/billing/') === false) {
        header("Location: ../../pages/billing/index.php");
        exit();
    }
} elseif ($role === 'manager') {
    // Store Managers can see everything EXCEPT the employee/user management page
    if (strpos($current_page, 'pages/users/') !== false) {
        header("Location: ../../pages/dashboard/index.php");
        exit();
    }
}
// Admins bypass all checks and can access everything
?>