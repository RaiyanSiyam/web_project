<?php
// pages/billing/index.php
require_once '../../includes/auth_guard.php';
require_once '../../app/models/Product.php';

$productModel = new Product();
// Fetch all active products to pass to JS for instant offline searching
$db = Database::getInstance();
$stmt = $db->query("SELECT id, sku, name, price, stock_quantity, image_path FROM products WHERE deleted_at IS NULL AND status = 'active' AND stock_quantity > 0");
$availableProducts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS | UIU Mart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/billing.css">
</head>
<body class="app-layout pos-mode">
    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../../includes/navbar.php'; ?>

        <div class="pos-container">
            <div class="pos-cart-section">
                <div class="pos-search-bar">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="productSearch" placeholder="Search product by name or SKU (Press Enter)" autocomplete="off">
                    <div id="searchDropdown" class="search-dropdown hidden"></div>
                </div>

                <div class="cart-table-wrapper">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr id="emptyCartRow">
                                <td colspan="5" class="text-muted" style="padding: 60px 0;">
                                    <div style="text-align: center; margin-left: 20px;">Cart is empty. Search products to add.</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pos-summary-section">
                <div class="customer-info">
                    <h3>Customer Details</h3>
                    <input type="text" id="custName" class="modern-input" placeholder="Customer Name (Optional)">
                    <input type="text" id="custPhone" class="modern-input" placeholder="Phone Number (Optional)">
                </div>

                <div class="summary-details">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="sumSubtotal" style="font-weight: 600;">৳0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Discount (৳)</span>
                        <input type="number" id="inpDiscount" class="inline-input" value="0" min="0" step="1">
                    </div>
                    <div class="summary-row">
                        <span>VAT (5%)</span>
                        <span id="sumVat" style="font-weight: 600;">৳0.00</span>
                    </div>
                    <div class="summary-row grand-total">
                        <span>Total Due</span>
                        <span id="sumTotal">৳0.00</span>
                    </div>
                </div>

                <div class="payment-section">
                    <span class="payment-label">Amount Tendered (৳)</span>
                    <input type="number" id="inpTendered" class="lg-input" placeholder="0.00">
                    
                    <div class="change-row">
                        <span>Change Return</span>
                        <span id="sumChange" class="change-amount">৳0.00</span>
                    </div>
                </div>

                <div class="checkout-actions">
                    <button class="btn-clear" id="btnClearCart">Clear</button>
                    <button class="btn-checkout" id="btnCheckout" disabled>
                        <span class="btn-text">Complete Sale</span>
                        <span class="loader hidden" id="checkoutLoader"></span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        const posProducts = <?= json_encode($availableProducts) ?>;
    </script>
    <?php include '../../includes/success_modal.php'; ?>
    <script src="../../assets/js/billing.js"></script>
</body>
</html>