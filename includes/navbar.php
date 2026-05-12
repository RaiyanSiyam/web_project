<header class="top-navbar">
    <div class="page-title">
        <h2 id="current-page-title">Dashboard</h2>
    </div>
    
    <div class="nav-actions">
        <button class="icon-btn" aria-label="Notifications">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            <span class="badge badge-error">2</span>
        </button>
        <div class="user-profile">
            <div class="avatar"><?= substr($_SESSION['user_name'], 0, 1) ?></div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <span class="user-role"><?= htmlspecialchars($_SESSION['role']) ?></span>
            </div>
        </div>
    </div>
</header>