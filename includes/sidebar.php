<?php
// includes/sidebar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_role = $_SESSION['role'] ?? 'cashier';
$current_uri = $_SERVER['REQUEST_URI'];
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-box"></div>
        <span>UIU Mart</span>
    </div>
    
    <nav class="sidebar-nav">
        <!-- 1. OVERVIEW VIEWPORT: Authorized for Administration & Store Management -->
        <?php if ($user_role === 'admin' || $user_role === 'manager'): ?>
            <div class="nav-label">OVERVIEW</div>
            <a href="../dashboard/index.php" class="nav-item <?= (strpos($current_uri, 'dashboard/') !== false) ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Dashboard
            </a>
        <?php endif; ?>
        
        <!-- 2. MANAGEMENT VIEWPORT -->
        <div class="nav-label">MANAGEMENT</div>
        
        <?php if ($user_role === 'admin' || $user_role === 'manager'): ?>
            <a href="../inventory/index.php" class="nav-item <?= (strpos($current_uri, 'inventory/') !== false) ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                Inventory
            </a>
        <?php endif; ?>

        <!-- Point Of Sale terminal: Accessible by all roles, defaults for Cashier -->
        <a href="../billing/index.php" class="nav-item <?= (strpos($current_uri, 'billing/') !== false) ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            Point of Sale
        </a>

        <?php if ($user_role === 'admin' || $user_role === 'manager'): ?>
            <a href="../reorder/index.php" class="nav-item <?= (strpos($current_uri, 'reorder/') !== false) ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                Reorders
            </a>
        <?php endif; ?>
        
        <!-- 3. STAFF PROVISIONING: Strictly Restricted to Root System Administrators -->
        <?php if ($user_role === 'admin'): ?>
            <div class="nav-label">CONTROL</div>
            <a href="../users/index.php" class="nav-item <?= (strpos($current_uri, 'users/') !== false) ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Employee Accounts
            </a>
        <?php endif; ?>

        <!-- 4. ANALYTICS VIEWPORT: Authorized for Administration & Store Management -->
        <?php if ($user_role === 'admin' || $user_role === 'manager'): ?>
            <div class="nav-label">ANALYTICS</div>
            <a href="../reports/index.php" class="nav-item <?= (strpos($current_uri, 'reports/') !== false) ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Reports
            </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="../../app/controllers/AuthController.php?action=logout" class="nav-item text-danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Logout
        </a>
    </div>
</aside>