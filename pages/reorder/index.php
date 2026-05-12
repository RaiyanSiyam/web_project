<?php
// pages/reorder/index.php
require_once '../../includes/auth_guard.php';
require_once '../../app/controllers/ReorderController.php';

$controller = new ReorderController();
$orders = $controller->index();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Auto-Reorder Engine | UIU Mart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/reorder.css">
</head>
<body class="app-layout">
    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../../includes/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="engine-header card">
                <div class="engine-info">
                    <div class="engine-icon bg-purple-light">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    </div>
                    <div>
                        <h2>Smart Reorder Engine</h2>
                        <p class="text-muted">Automatically generates purchase orders for low-stock items based on threshold rules.</p>
                    </div>
                </div>
                <button class="btn btn-primary btn-lg" id="btnRunEngine">
                    <span class="btn-text">Run Engine Now</span>
                    <span class="loader hidden" id="engineLoader"></span>
                </button>
            </div>

            <div class="card table-card mt-4">
                <div class="table-header">
                    <h3>Purchase Orders & Requisitions</h3>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Product</th>
                                <th>Supplier</th>
                                <th>Req. Qty</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($orders)): ?>
                            <tr><td colspan="7" class="text-center empty-state">No reorder requests found. Run the engine to check stock.</td></tr>
                            <?php endif; ?>
                            
                            <?php foreach ($orders as $order): 
                                $badgeClass = '';
                                if($order['status'] == 'pending') $badgeClass = 'badge-warning';
                                if($order['status'] == 'ordered') $badgeClass = 'badge-info';
                                if($order['status'] == 'received') $badgeClass = 'badge-success';
                                if($order['status'] == 'cancelled') $badgeClass = 'badge-danger';
                            ?>
                            <tr>
                                <td><span class="fw-600"><?= htmlspecialchars($order['reorder_number']) ?></span></td>
                                <td>
                                    <div class="product-info">
                                        <span class="product-name"><?= htmlspecialchars($order['product_name']) ?></span>
                                        <span class="sku-text text-muted"><?= htmlspecialchars($order['sku']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($order['supplier_name']) ?></td>
                                <td class="fw-600"><?= $order['requested_qty'] ?> units</td>
                                <td><span class="status-badge <?= $badgeClass ?>"><?= ucfirst($order['status']) ?></span></td>
                                <td class="text-muted"><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <?php if($order['status'] == 'pending'): ?>
                                        <button class="btn btn-outline btn-sm" onclick="updateOrderStatus(<?= $order['id'] ?>, 'ordered')">Mark Ordered</button>
                                    <?php elseif($order['status'] == 'ordered'): ?>
                                        <button class="btn btn-success btn-sm" onclick="updateOrderStatus(<?= $order['id'] ?>, 'received')">Receive Stock</button>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../includes/success_modal.php'; ?>
    <script src="../../assets/js/reorder.js"></script>
</body>
</html>