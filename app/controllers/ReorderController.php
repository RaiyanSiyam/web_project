<?php
// app/controllers/ReorderController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Reorder.php';

class ReorderController {
    private $reorderModel;

    public function __construct() {
        $this->reorderModel = new Reorder();
    }

    public function index() {
        return $this->reorderModel->getAllReorders();
    }

    public function runEngine() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $count = $this->reorderModel->generateAutoReorders();
            echo json_encode(['status' => 'success', 'message' => "Engine ran successfully. Generated $count new purchase orders."]);
            exit();
        }
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
            $id = (int)$_POST['id'];
            $status = $_POST['status'];
            
            if ($this->reorderModel->updateStatus($id, $status)) {
                echo json_encode(['status' => 'success', 'message' => 'Order status updated.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update status.']);
            }
            exit();
        }
    }
}

// Router
if (isset($_GET['action'])) {
    $controller = new ReorderController();
    if ($_GET['action'] === 'run_engine') {
        $controller->runEngine();
    } elseif ($_GET['action'] === 'update_status') {
        $controller->updateStatus();
    }
}
?>