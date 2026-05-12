<?php
// login.php
session_start();

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: pages/dashboard/index.php");
    exit();
}

$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']); // Clear error after displaying
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | UIU Mart</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-left">
        <div class="auth-content">
            <div class="brand-logo">
                <div class="logo-box"></div>
                <span>UIU Mart</span>
            </div>
            
            <div class="auth-header">
                <h1>Welcome back</h1>
                <p>Enter your details to access the dashboard.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="app/controllers/AuthController.php?action=login" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="name@company.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" id="togglePassword" class="btn-icon" aria-label="Toggle password visibility">
                            <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <div class="form-actions">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember" id="remember">
                        <span class="checkmark"></span>
                        <span class="label-text">Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="loginBtn">
                    <span class="btn-text">Sign In</span>
                    <span class="loader hidden"></span>
                </button>
            </form>
        </div>
    </div>
    
    <div class="auth-right">
        <div class="illustration-panel">
            <div class="glass-card">
                <h3>System Status</h3>
                <div class="status-row">
                    <span class="dot green"></span> All systems operational
                </div>
                <p>UIU Mart POS & Inventory Engine</p>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/auth.js"></script>
</body>
</html>