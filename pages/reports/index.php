<?php
// pages/reports/index.php
require_once '../../includes/auth_guard.php';
require_once '../../app/controllers/ReportController.php';

$controller = new ReportController();
$analytics = $controller->getAnalyticsData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports & Analytics | UIU Mart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/reports.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="app-layout">
    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../../includes/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="page-header reports-header">
                <div>
                    <h1>Business Analytics</h1>
                    <p class="text-muted">Comprehensive overview of sales, inventory, and performance.</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-outline" onclick="window.print()">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        Print Report
                    </button>
                    <select class="report-filter">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>This Year</option>
                    </select>
                </div>
            </div>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Monthly Revenue</div>
                    <div class="kpi-val">৳<?= number_format($analytics['metrics']['monthly_revenue'], 2) ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Total Orders (Month)</div>
                    <div class="kpi-val"><?= number_format($analytics['metrics']['total_orders']) ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Average Order Value</div>
                    <div class="kpi-val">৳<?= number_format($analytics['metrics']['avg_order_value'], 2) ?></div>
                </div>
                <div class="kpi-card highlight-card">
                    <div class="kpi-label">Total Inventory Value</div>
                    <div class="kpi-val">৳<?= number_format($analytics['metrics']['inventory_value'], 2) ?></div>
                </div>
            </div>

            <div class="charts-layout">
                <div class="chart-box main-chart">
                    <div class="chart-header">
                        <h3>Sales Trend (Last 15 Days)</h3>
                    </div>
                    <div class="chart-body">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>

                <div class="chart-box side-chart">
                    <div class="chart-header">
                        <h3>Top Performing Products</h3>
                    </div>
                    <div class="chart-body doughnut-container">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const analyticsData = <?= json_encode($analytics) ?>;
    </script>
    <script src="../../assets/js/reports.js"></script>
</body>
</html>