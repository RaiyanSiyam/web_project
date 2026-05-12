<?php
// pages/dashboard/index.php
require_once '../../includes/auth_guard.php';
require_once '../../app/controllers/DashboardController.php';

$controller = new DashboardController();
$metrics = $controller->getDashboardMetrics();
$chartData = json_encode($controller->getSalesChartData());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | UIU Mart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="app-layout">

    <?php include '../../includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include '../../includes/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Total Revenue</span>
                        <span class="kpi-icon bg-green-light">৳</span>
                    </div>
                    <div class="kpi-value">৳<?= number_format($metrics['total_revenue'], 2) ?></div>
                    <div class="kpi-trend positive">↑ 12% from last month</div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Sales Today</span>
                        <span class="kpi-icon bg-blue-light">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        </span>
                    </div>
                    <div class="kpi-value">৳<?= number_format($metrics['sales_today'], 2) ?></div>
                    <div class="kpi-trend positive">↑ 4% from yesterday</div>
                </div>

                <div class="kpi-card warning-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Low Stock Alerts</span>
                        <span class="kpi-icon bg-orange-light">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        </span>
                    </div>
                    <div class="kpi-value"><?= number_format($metrics['low_stock_count']) ?></div>
                    <div class="kpi-trend text-orange">Requires immediate action</div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Pending Reorders</span>
                        <span class="kpi-icon bg-purple-light">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        </span>
                    </div>
                    <div class="kpi-value"><?= number_format($metrics['pending_reorders']) ?></div>
                    <div class="kpi-trend text-muted">Awaiting supplier response</div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="chart-container card">
                    <div class="card-header">
                        <h3>Sales Overview (Last 7 Days)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="quick-actions-container card">
                    <div class="card-header">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="card-body action-grid">
                        <a href="../billing/index.php" class="action-btn">
                            <div class="action-icon">🧾</div>
                            <span>New Sale (POS)</span>
                        </a>
                        <a href="../inventory/index.php?action=add" class="action-btn">
                            <div class="action-icon">📦</div>
                            <span>Add Product</span>
                        </a>
                        <a href="../reorder/index.php" class="action-btn">
                            <div class="action-icon">🔄</div>
                            <span>Process Reorders</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const chartData = <?= $chartData ?>;
    </script>
    <script src="../../assets/js/dashboard.js"></script>
</body>
</html>