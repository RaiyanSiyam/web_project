<?php
// pages/inventory/edit.php
require_once '../../includes/auth_guard.php';
require_once '../../app/models/Product.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$productModel = new Product();
$productId = (int)$_GET['id'];
$product = $productModel->getProductById($productId);

if (!$product) {
    header("Location: index.php");
    exit();
}

$categories = $productModel->getCategories();
$suppliers = $productModel->getSuppliers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | UIU Mart</title>
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
                    <a href="index.php" class="back-link">← Back to Inventory</a>
                    <h1>Edit Product: <?= htmlspecialchars($product['name']) ?></h1>
                </div>
            </div>

            <div class="card form-card">
                <form action="../../app/controllers/InventoryController.php?action=update" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    
                    <div class="form-grid">
                        <div class="form-group col-span-2">
                            <label>Product Name *</label>
                            <input type="text" name="name" required class="form-control" value="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>SKU (Stock Keeping Unit) *</label>
                            <input type="text" name="sku" required class="form-control" value="<?= htmlspecialchars($product['sku']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Selling Price (৳) *</label>
                            <input type="number" step="0.01" name="price" required class="form-control" value="<?= $product['price'] ?>">
                        </div>

                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category_id" required class="form-control">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Supplier *</label>
                            <select name="supplier_id" required class="form-control">
                                <?php foreach($suppliers as $sup): ?>
                                    <option value="<?= $sup['id'] ?>" <?= ($product['supplier_id'] == $sup['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sup['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Stock Quantity *</label>
                            <input type="number" name="stock_quantity" required class="form-control" value="<?= $product['stock_quantity'] ?>">
                        </div>

                        <div class="form-group">
                            <label>Low Stock Alert Threshold *</label>
                            <input type="number" name="min_threshold" required class="form-control" value="<?= $product['min_threshold'] ?>">
                        </div>

                        <div class="form-group col-span-2">
                            <label>Product Image</label>
                            <div class="file-upload-box" id="fileUploadBox">
                                <input type="file" name="image" id="imageInput" accept="image/*" class="hidden-file-input">
                                <div class="upload-content <?= $product['image_path'] ? 'hidden' : '' ?>">
                                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p>Click or drag new image to replace</p>
                                </div>
                                <img id="imagePreview" src="<?= $product['image_path'] ? '../../' . htmlspecialchars($product['image_path']) : '' ?>" class="<?= $product['image_path'] ? '' : 'hidden' ?>" style="max-height: 150px;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions-footer">
                        <a href="index.php" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="../../assets/js/inventory.js"></script>
</body>
</html>