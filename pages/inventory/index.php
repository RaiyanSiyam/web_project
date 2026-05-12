<?php
// pages/inventory/index.php
require_once '../../includes/auth_guard.php';
require_once '../../app/controllers/InventoryController.php';

$controller = new InventoryController();
$products = $controller->index();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory | UIU Mart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/inventory.css">
</head>
<body class="app-layout">
    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content">
        <?php include '../../includes/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1>Inventory Management</h1>
                    <p class="text-muted">Manage your products, pricing, and stock levels.</p>
                </div>
                <a href="create.php" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Add Product
                </a>
            </div>

            <div class="card table-card">
                <div class="table-toolbar">
                    <form action="index.php" method="GET" class="search-box">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" name="search" placeholder="Search by name or SKU..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    </form>
                    <div class="toolbar-actions">
                        <button class="btn btn-outline">Filter</button>
                        <button class="btn btn-outline">Export</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($products)): ?>
                            <tr><td colspan="6" class="text-center empty-state">No products found.</td></tr>
                            <?php endif; ?>
                            
                            <?php foreach ($products as $product): 
                                $stockPct = ($product['stock_quantity'] / max($product['min_threshold'] * 3, 1)) * 100;
                                $stockPct = min($stockPct, 100);
                                $stockColor = $product['stock_quantity'] <= $product['min_threshold'] ? 'bg-error' : 'bg-success';
                            ?>
                            <tr id="row-<?= $product['id'] ?>">
                                <td>
                                    <div class="product-cell">
                                        <div class="product-img">
                                            <?php if($product['image_path']): ?>
                                                <img src="../../<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                            <?php else: ?>
                                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                            <?php endif; ?>
                                        </div>
                                        <span class="product-name"><?= htmlspecialchars($product['name']) ?></span>
                                    </div>
                                </td>
                                <td><span class="sku-badge"><?= htmlspecialchars($product['sku']) ?></span></td>
                                <td><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></td>
                                <td class="fw-600">৳<?= number_format($product['price'], 2) ?></td>
                                <td>
                                    <div class="stock-info">
                                        <span><?= $product['stock_quantity'] ?> in stock</span>
                                        <div class="progress-bar">
                                            <div class="progress-fill <?= $stockColor ?>" style="width: <?= $stockPct ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-cell">
                                        <a href="edit.php?id=<?= $product['id'] ?>" class="icon-btn edit-btn" title="Edit">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                        </a>                                        
                                        <button class="icon-btn delete-btn text-danger" onclick="deleteProduct(<?= $product['id'] ?>)" title="Delete"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../includes/delete_modal.php'; ?>
    <script src="../../assets/js/inventory.js"></script>
</body>
</html>