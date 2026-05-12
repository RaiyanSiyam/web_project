<?php
// app/controllers/InventoryController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Product.php';

class InventoryController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function index() {
        $search = $_GET['search'] ?? '';
        return $this->productModel->getAllProducts($search);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'sku' => filter_var($_POST['sku'], FILTER_SANITIZE_STRING),
                'name' => filter_var($_POST['name'], FILTER_SANITIZE_STRING),
                'category_id' => (int)$_POST['category_id'],
                'supplier_id' => (int)$_POST['supplier_id'],
                'description' => filter_var($_POST['description'], FILTER_SANITIZE_STRING),
                'price' => (float)$_POST['price'],
                'stock_quantity' => (int)$_POST['stock_quantity'],
                'min_threshold' => (int)$_POST['min_threshold'],
                'status' => $_POST['status'] ?? 'active',
                'image_path' => null
            ];

            // Handle Image Upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../assets/images/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
                }
                
                $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($fileExt, $allowedExts)) {
                    $fileName = uniqid('prod_') . '.' . $fileExt;
                    $targetPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $data['image_path'] = 'assets/images/products/' . $fileName;
                    }
                }
            }

            if ($this->productModel->createProduct($data)) {
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Product added successfully.'];
                header("Location: ../../pages/inventory/index.php");
                exit();
            } else {
                $_SESSION['toast'] = ['type' => 'error', 'message' => 'Failed to add product.'];
                header("Location: ../../pages/inventory/create.php");
                exit();
            }
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            if ($this->productModel->softDelete($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Product deleted.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Deletion failed.']);
            }
            exit();
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $data = [
                'sku' => filter_var($_POST['sku'], FILTER_SANITIZE_STRING),
                'name' => filter_var($_POST['name'], FILTER_SANITIZE_STRING),
                'category_id' => (int)$_POST['category_id'],
                'supplier_id' => (int)$_POST['supplier_id'],
                'description' => filter_var($_POST['description'], FILTER_SANITIZE_STRING),
                'price' => (float)$_POST['price'],
                'stock_quantity' => (int)$_POST['stock_quantity'],
                'min_threshold' => (int)$_POST['min_threshold'],
                'status' => $_POST['status'] ?? 'active',
                'image_path' => null
            ];

            // Handle Image Upload if a new one is provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../assets/images/products/';
                $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($fileExt, $allowedExts)) {
                    $fileName = uniqid('prod_') . '.' . $fileExt;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                        $data['image_path'] = 'assets/images/products/' . $fileName;
                    }
                }
            }

            if ($this->productModel->updateProduct($id, $data)) {
                $_SESSION['toast'] = ['type' => 'success', 'message' => 'Product updated successfully.'];
                header("Location: ../../pages/inventory/index.php");
                exit();
            } else {
                $_SESSION['toast'] = ['type' => 'error', 'message' => 'Failed to update product.'];
                header("Location: ../../pages/inventory/edit.php?id=" . $id);
                exit();
            }
        }
    }

}

// Route actions
if (isset($_GET['action'])) {
    $controller = new InventoryController();
    if ($_GET['action'] === 'store') {
        $controller->store();
    } elseif ($_GET['action'] === 'delete') {
        $controller->delete();
    } elseif ($_GET['action'] === 'update') {
        $controller->update();
    }
}
?>