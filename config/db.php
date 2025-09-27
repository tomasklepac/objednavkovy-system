<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = "127.0.0.1";
        $db   = "objednavkovy_system";
        $user = "root"; // výchozí v XAMPPu
        $pass = "";     // prázdné heslo, pokud sis nenastavil
        $charset = "utf8mb4";

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
