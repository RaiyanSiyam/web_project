<?php
// pages/inventory/create.php
require_once '../../includes/auth_guard.php';
require_once '../../app/models/Product.php';

$productModel = new Product();
// Note: You need to insert at least 1 category and 1 supplier in DB for these dropdowns to populate
$categories = $productModel->getCategories();
$suppliers = $productModel->getSuppliers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product | UIU Mart</title>
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
                    <h1>Add New Product</h1>
                </div>
            </div>

            <div class="card form-card">
                <form action="../../app/controllers/InventoryController.php?action=store" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group col-span-2">
                            <label>Product Name *</label>
                            <input type="text" name="name" required class="form-control" placeholder="e.g. Fresh Organic Apples">
                        </div>
                        
                        <div class="form-group">
                            <label>SKU (Stock Keeping Unit) *</label>
                            <input type="text" name="sku" required class="form-control" placeholder="APP-ORG-001">
                        </div>

                        <div class="form-group">
                            <label>Selling Price (৳) *</label>
                            <input type="number" step="0.01" name="price" required class="form-control" placeholder="0.00">
                        </div>

                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category_id" required class="form-control">
                                <option value="">Select Category</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Supplier *</label>
                            <select name="supplier_id" required class="form-control">
                                <option value="">Select Supplier</option>
                                <?php foreach($suppliers as $sup): ?>
                                    <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Initial Stock Quantity *</label>
                            <input type="number" name="stock_quantity" required class="form-control" value="0">
                        </div>

                        <div class="form-group">
                            <label>Low Stock Alert Threshold *</label>
                            <input type="number" name="min_threshold" required class="form-control" value="10">
                        </div>

                        <div class="form-group col-span-2">
                            <label>Product Image</label>
                            <div class="file-upload-box" id="fileUploadBox">
                                <input type="file" name="image" id="imageInput" accept="image/*" class="hidden-file-input">
                                <div class="upload-content">
                                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p>Click or drag image here</p>
                                    <span class="text-muted">JPG, PNG, WEBP up to 2MB</span>
                                </div>
                                <img id="imagePreview" src="" class="hidden">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions-footer">
                        <a href="index.php" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="../../assets/js/inventory.js"></script>
</body>
</html>