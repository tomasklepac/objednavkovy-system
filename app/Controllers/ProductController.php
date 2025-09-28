<?php
require_once __DIR__ . '/../Models/Product.php';

class ProductController {
    private $productModel;

    public function __construct($pdo) {
        $this->productModel = new Product($pdo);
    }

    // vrátí všechny produkty (voláme model)
    public function index() {
        return $this->productModel->getAllProducts();
    }
}
