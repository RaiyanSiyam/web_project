<?php
// includes/navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set up resilient local fallbacks to eliminate PHP Notices
$displayName = $_SESSION['user_name'] ?? $_SESSION['username'] ?? 'System User';
$displayRole = $_SESSION['role'] ?? 'cashier';
$avatarInitial = !empty($displayName) ? substr($displayName, 0, 1) : 'U';
?>
<header class="top-navbar">
    <div class="page-title">
        <h2 id="current-page-title">Dashboard</h2>
    </div>
    
    <div class="nav-actions">
        <button class="icon-btn" aria-label="Notifications">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="badge badge-error">2</span>
        </button>
        <div class="user-profile">
            <div class="avatar"><?= htmlspecialchars(strtoupper($avatarInitial)) ?></div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($displayName) ?></span>
                <span class="user-role" style="text-transform: capitalize; font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($displayRole) ?></span>
            </div>
        </div>
    </div>
</header>