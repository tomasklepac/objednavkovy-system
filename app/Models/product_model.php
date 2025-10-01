<?php

class product_model {
    private $pdo; // připojení k databázi

    // -------------------------------------------------
    // KONSTRUKTOR
    // -------------------------------------------------
    // Dostane PDO objekt a uloží ho do proměnné třídy
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // -------------------------------------------------
    // READ: načte všechny aktivní produkty
    // -------------------------------------------------
    public function getAllProducts() {
        // pošleme dotaz do databáze
        $stmt = $this->pdo->query("SELECT * FROM products WHERE is_active = 1");

        // vrátíme všechny výsledky jako pole asociativních polí
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
