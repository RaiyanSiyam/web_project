<?php
// app/controllers/ReportController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

class ReportController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAnalyticsData() {
        $data = [
            'metrics' => [
                'monthly_revenue' => 0.00,
                'total_orders' => 0,
                'avg_order_value' => 0.00,
                'inventory_value' => 0.00
            ],
            'sales_trend' => ['labels' => [], 'values' => []],
            'top_products' => ['labels' => [], 'values' => []]
        ];

        try {
            // 1. Current Month Revenue & Order Count
            $stmt = $this->db->query("
                SELECT SUM(grand_total) as rev, COUNT(id) as orders 
                FROM invoices 
                WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
                AND YEAR(created_at) = YEAR(CURRENT_DATE())
            ");
            $monthData = $stmt->fetch();
            $data['metrics']['monthly_revenue'] = (float)($monthData['rev'] ?? 0);
            $data['metrics']['total_orders'] = (int)($monthData['orders'] ?? 0);
            
            if ($data['metrics']['total_orders'] > 0) {
                $data['metrics']['avg_order_value'] = $data['metrics']['monthly_revenue'] / $data['metrics']['total_orders'];
            }

            // 2. Total Inventory Valuation (Cost of items currently in stock)
            $stmt = $this->db->query("SELECT SUM(price * stock_quantity) as total_val FROM products WHERE deleted_at IS NULL AND status = 'active'");
            $data['metrics']['inventory_value'] = (float)($stmt->fetch()['total_val'] ?? 0);

            // 3. Sales Trend (Last 15 Days)
            $stmt = $this->db->query("
                SELECT DATE(created_at) as date, SUM(grand_total) as daily_total 
                FROM invoices 
                WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 14 DAY)
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at) ASC
            ");
            while ($row = $stmt->fetch()) {
                $data['sales_trend']['labels'][] = date('M d', strtotime($row['date']));
                $data['sales_trend']['values'][] = (float)$row['daily_total'];
            }

            // 4. Top 5 Selling Products (All Time)
            $stmt = $this->db->query("
                SELECT p.name, SUM(ii.quantity) as total_sold 
                FROM invoice_items ii
                JOIN products p ON ii.product_id = p.id
                GROUP BY p.id
                ORDER BY total_sold DESC
                LIMIT 5
            ");
            while ($row = $stmt->fetch()) {
                // Shorten name if too long for the chart label
                $name = strlen($row['name']) > 15 ? substr($row['name'], 0, 15) . '...' : $row['name'];
                $data['top_products']['labels'][] = $name;
                $data['top_products']['values'][] = (int)$row['total_sold'];
            }

        } catch (PDOException $e) {
            error_log("Report Error: " . $e->getMessage());
        }

        return $data;
    }
}
?>