<?php
// app/controllers/BillingController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Product.php';

class BillingController {
    private $invoiceModel;
    private $productModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->productModel = new Product();
    }

    public function processSale() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get JSON payload
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data || empty($data['cart'])) {
                echo json_encode(['status' => 'error', 'message' => 'Cart is empty.']);
                exit();
            }

            // Secure Recalculation (Never trust frontend JS math)
            $subtotal = 0;
            $verifiedCart = [];
            $db = Database::getInstance();

            foreach ($data['cart'] as $item) {
                // Fetch current actual price from DB
                $stmt = $db->prepare("SELECT price, stock_quantity FROM products WHERE id = :id AND deleted_at IS NULL AND status = 'active'");
                $stmt->execute(['id' => $item['id']]);
                $product = $stmt->fetch();

                if (!$product) {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid product in cart.']);
                    exit();
                }
                if ($product['stock_quantity'] < $item['qty']) {
                    echo json_encode(['status' => 'error', 'message' => 'Not enough stock for item ID: ' . $item['id']]);
                    exit();
                }

                $itemTotal = $product['price'] * $item['qty'];
                $subtotal += $itemTotal;

                $verifiedCart[] = [
                    'id' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $product['price'],
                    'total' => $itemTotal
                ];
            }

            // Calculate final amounts
            $discount = (float)($data['discount'] ?? 0);
            $vatRate = 0.05; // 5% VAT (Change as per your requirement)
            $vatAmount = ($subtotal - $discount) * $vatRate;
            $grandTotal = ($subtotal - $discount) + $vatAmount;
            
            $amountPaid = (float)($data['amount_paid'] ?? 0);
            if ($amountPaid < $grandTotal) {
                echo json_encode(['status' => 'error', 'message' => 'Paid amount is less than Grand Total.']);
                exit();
            }
            $changeReturn = $amountPaid - $grandTotal;

            // Generate unique Invoice Number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);

            $invoiceData = [
                'invoice_number' => $invoiceNumber,
                'customer_name' => filter_var($data['customer_name'] ?? 'Walk-in Customer', FILTER_SANITIZE_STRING),
                'customer_phone' => filter_var($data['customer_phone'] ?? '', FILTER_SANITIZE_STRING),
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'discount_amount' => $discount,
                'grand_total' => $grandTotal,
                'amount_paid' => $amountPaid,
                'change_return' => $changeReturn,
                'created_by' => $_SESSION['user_id']
            ];

            // Save to DB
            $result = $this->invoiceModel->createSale($invoiceData, $verifiedCart);
            echo json_encode($result);
            exit();
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'process') {
    $controller = new BillingController();
    $controller->processSale();
}
?>