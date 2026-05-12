<?php
// app/models/Product.php
require_once __DIR__ . '/../config/db.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllProducts($search = '') {
        $query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                  FROM products p
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN suppliers s ON p.supplier_id = s.id
                  WHERE p.deleted_at IS NULL ";
        
        if (!empty($search)) {
            $query .= " AND (p.name LIKE :search OR p.sku LIKE :search) ";
        }
        
        $query .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($query);
        
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createProduct($data) {
        $query = "INSERT INTO products (sku, name, category_id, supplier_id, description, price, stock_quantity, min_threshold, image_path, status) 
                  VALUES (:sku, :name, :category_id, :supplier_id, :description, :price, :stock_quantity, :min_threshold, :image_path, :status)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'sku' => $data['sku'],
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'supplier_id' => $data['supplier_id'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'],
            'min_threshold' => $data['min_threshold'],
            'image_path' => $data['image_path'],
            'status' => $data['status']
        ]);

        
    }

    public function softDelete($id) {
        $query = "UPDATE products SET deleted_at = CURRENT_TIMESTAMP, status = 'discontinued' WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Helper to get dropdown data
    public function getCategories() {
        return $this->db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    }

    public function getSuppliers() {
        return $this->db->query("SELECT * FROM suppliers WHERE deleted_at IS NULL ORDER BY name ASC")->fetchAll();
    }

    public function getProductById($id) {
        $query = "SELECT * FROM products WHERE id = :id AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateProduct($id, $data) {
        $query = "UPDATE products SET 
                    sku = :sku, 
                    name = :name, 
                    category_id = :category_id, 
                    supplier_id = :supplier_id, 
                    description = :description, 
                    price = :price, 
                    stock_quantity = :stock_quantity, 
                    min_threshold = :min_threshold, 
                    status = :status";

        // Only update image path if a new image was uploaded
        if ($data['image_path'] !== null) {
            $query .= ", image_path = :image_path";
        }
        
        $query .= " WHERE id = :id";

        $stmt = $this->db->prepare($query);
        
        $params = [
            'id' => $id,
            'sku' => $data['sku'],
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'supplier_id' => $data['supplier_id'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock_quantity' => $data['stock_quantity'],
            'min_threshold' => $data['min_threshold'],
            'status' => $data['status']
        ];

        if ($data['image_path'] !== null) {
            $params['image_path'] = $data['image_path'];
        }

        return $stmt->execute($params);
    }
}
?>