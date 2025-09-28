<?php

class Product {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // načte všechny produkty
    public function getAllProducts() {
        $stmt = $this->pdo->query("SELECT * FROM products WHERE is_active = 1");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
