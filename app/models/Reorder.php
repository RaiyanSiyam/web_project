<?php
// app/models/Reorder.php
require_once __DIR__ . '/../config/db.php';

class Reorder {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllReorders() {
        $query = "SELECT r.*, p.name as product_name, p.sku, s.name as supplier_name 
                  FROM reorder_requests r
                  JOIN products p ON r.product_id = p.id
                  JOIN suppliers s ON r.supplier_id = s.id
                  ORDER BY r.created_at DESC";
        return $this->db->query($query)->fetchAll();
    }

    // The Core Auto-Reorder Engine Algorithm
    public function generateAutoReorders() {
        $newOrdersCount = 0;
        
        // 1. Find all active products where stock is at or below the minimum threshold
        $query = "SELECT id, supplier_id, min_threshold, stock_quantity 
                  FROM products 
                  WHERE stock_quantity <= min_threshold 
                  AND status = 'active' 
                  AND deleted_at IS NULL";
        $lowStockProducts = $this->db->query($query)->fetchAll();

        $insertStmt = $this->db->prepare("INSERT INTO reorder_requests (reorder_number, product_id, supplier_id, requested_qty, status) VALUES (:reorder_number, :product_id, :supplier_id, :requested_qty, 'pending')");
        
        // Check to prevent creating multiple pending orders for the same product
        $checkStmt = $this->db->prepare("SELECT id FROM reorder_requests WHERE product_id = :product_id AND status IN ('pending', 'ordered')");

        foreach ($lowStockProducts as $p) {
            $checkStmt->execute(['product_id' => $p['id']]);
            
            // If no pending/ordered request exists, generate one automatically
            if (!$checkStmt->fetch()) {
                // Intelligent Restock Formula: Order enough to reach 3x the threshold, or at least 10
                $restockQty = max($p['min_threshold'] * 3, 10); 
                $orderNumber = 'PO-' . date('ymd') . '-' . rand(1000, 9999);

                $insertStmt->execute([
                    'reorder_number' => $orderNumber,
                    'product_id' => $p['id'],
                    'supplier_id' => $p['supplier_id'],
                    'requested_qty' => $restockQty
                ]);
                $newOrdersCount++;
            }
        }
        return $newOrdersCount;
    }

    public function updateStatus($id, $status) {
        if ($status === 'received') {
            return $this->processReceipt($id);
        } else {
            $stmt = $this->db->prepare("UPDATE reorder_requests SET status = :status WHERE id = :id");
            return $stmt->execute(['status' => $status, 'id' => $id]);
        }
    }

    // Safely add stock when an order is received
    private function processReceipt($id) {
        try {
            $this->db->beginTransaction();
            
            // Lock and fetch the request
            $stmt = $this->db->prepare("SELECT product_id, requested_qty FROM reorder_requests WHERE id = :id AND status != 'received'");
            $stmt->execute(['id' => $id]);
            $order = $stmt->fetch();
            
            if ($order) {
                // Update order status
                $this->db->prepare("UPDATE reorder_requests SET status = 'received' WHERE id = :id")->execute(['id' => $id]);
                
                // Add to actual inventory stock
                $this->db->prepare("UPDATE products SET stock_quantity = stock_quantity + :qty WHERE id = :pid")
                         ->execute(['qty' => $order['requested_qty'], 'pid' => $order['product_id']]);
                
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            return false;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
?>