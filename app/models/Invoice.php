<?php
// app/models/Invoice.php
require_once __DIR__ . '/../config/db.php';

class Invoice {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

   public function createSale($invoiceData, $cartItems) {
        try {
            $this->db->beginTransaction();

            // 1. Insert Invoice
            $query = "INSERT INTO invoices (invoice_number, customer_name, customer_phone, subtotal, vat_amount, discount_amount, grand_total, amount_paid, change_return, payment_status, created_by) 
                      VALUES (:invoice_number, :customer_name, :customer_phone, :subtotal, :vat_amount, :discount_amount, :grand_total, :amount_paid, :change_return, :payment_status, :created_by)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'invoice_number' => $invoiceData['invoice_number'],
                'customer_name' => $invoiceData['customer_name'],
                'customer_phone' => $invoiceData['customer_phone'],
                'subtotal' => $invoiceData['subtotal'],
                'vat_amount' => $invoiceData['vat_amount'],
                'discount_amount' => $invoiceData['discount_amount'],
                'grand_total' => $invoiceData['grand_total'],
                'amount_paid' => $invoiceData['amount_paid'],
                'change_return' => $invoiceData['change_return'],
                'payment_status' => 'paid',
                'created_by' => $invoiceData['created_by']
            ]);

            $invoiceId = $this->db->lastInsertId();

            // 2. Insert Invoice Items & Deduct Stock
            $itemQuery = "INSERT INTO invoice_items (invoice_id, product_id, quantity, unit_price, total_price) 
                          VALUES (:invoice_id, :product_id, :quantity, :unit_price, :total_price)";
            $itemStmt = $this->db->prepare($itemQuery);

            // FIX: Use separate parameters for deduction and condition checks
            $stockQuery = "UPDATE products SET stock_quantity = stock_quantity - :qty_deduct WHERE id = :id AND stock_quantity >= :qty_check";
            $stockStmt = $this->db->prepare($stockQuery);

            foreach ($cartItems as $item) {
                // Insert line item
                $itemStmt->execute([
                    'invoice_id' => $invoiceId,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['total']
                ]);

                // Deduct stock safely
                $stockStmt->execute([
                    'qty_deduct' => $item['qty'],
                    'qty_check' => $item['qty'],
                    'id' => $item['id']
                ]);

                if ($stockStmt->rowCount() === 0) {
                    throw new Exception("Insufficient stock for product ID: " . $item['id']);
                }
            }

            $this->db->commit();
            return ['status' => 'success', 'invoice_id' => $invoiceId, 'invoice_number' => $invoiceData['invoice_number']];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
?>