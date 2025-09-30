<?php
/**
 * Třída Database
 * ------------------
 * Zajišťuje připojení k databázi pomocí PDO.
 * Používá návrhový vzor "Singleton" – připojení se vytvoří jen jednou
 * a pak se stále používá stejné.
 */
class Database {
    // Uloží jednu jedinou instanci (singleton)
    private static $instance = null;

    // PDO objekt (samotné připojení k databázi)
    private $pdo;

    /**
     * Konstruktor je private → nelze vytvořit instanci pomocí new zvenku
     */
    private function __construct() {
        // Přihlašovací údaje k databázi
        $host = "127.0.0.1";          // databázový server
        $db   = "objednavkovy_system"; // název databáze
        $user = "root";               // výchozí uživatel v XAMPP
        $pass = "";                   // heslo (ve výchozím stavu prázdné)
        $charset = "utf8mb4";         // správné kódování češtiny a emoji

        // DSN = Data Source Name → string, který PDO použije k připojení
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        // Volby pro PDO
        $options = [
            // Chyby se budou házet jako výjimky (lepší pro debug)
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

            // Každý fetch vrátí asociativní pole (["id" => 1, "name" => "Tomáš"])
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        // Vytvoření samotného PDO objektu (připojení k DB)
        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    /**
     * Statická metoda na získání jediné instance PDO
     */
    public static function getInstance() {
        // Pokud ještě instance neexistuje → vytvoříme ji
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        // Vracíme samotný PDO objekt
        return self::$instance->pdo;
    }
}
