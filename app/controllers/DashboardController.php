<?php
// app/controllers/DashboardController.php
require_once __DIR__ . '/../config/db.php';

class DashboardController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getDashboardMetrics() {
        $metrics = [
            'total_revenue' => 0.00,
            'sales_today' => 0.00,
            'low_stock_count' => 0,
            'pending_reorders' => 0
        ];

        try {
            // Total Revenue
            $stmt = $this->db->query("SELECT SUM(grand_total) as total FROM invoices WHERE payment_status = 'paid'");
            $metrics['total_revenue'] = $stmt->fetch()['total'] ?? 0.00;

            // Sales Today
            $stmt = $this->db->query("SELECT SUM(grand_total) as total FROM invoices WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid'");
            $metrics['sales_today'] = $stmt->fetch()['total'] ?? 0.00;

            // Low Stock Items
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity <= min_threshold AND status = 'active' AND deleted_at IS NULL");
            $metrics['low_stock_count'] = $stmt->fetch()['count'] ?? 0;

            // Pending Reorders
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM reorder_requests WHERE status = 'pending'");
            $metrics['pending_reorders'] = $stmt->fetch()['count'] ?? 0;

        } catch (PDOException $e) {
            error_log("Dashboard Metrics Error: " . $e->getMessage());
        }

        return $metrics;
    }

    public function getSalesChartData() {
        // Last 7 days sales data for Chart.js
        $data = ['labels' => [], 'values' => []];
        try {
            $stmt = $this->db->query("
                SELECT DATE(created_at) as sale_date, SUM(grand_total) as daily_total 
                FROM invoices 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at) ASC
            ");
            $results = $stmt->fetchAll();
            foreach ($results as $row) {
                $data['labels'][] = date('M d', strtotime($row['sale_date']));
                $data['values'][] = (float)$row['daily_total'];
            }
        } catch (PDOException $e) {
            error_log("Chart Data Error: " . $e->getMessage());
        }
        return $data;
    }
}
?>