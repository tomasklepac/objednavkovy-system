<?php
/**
 * Třída Database
 * ------------------
 * Zajišťuje připojení k databázi pomocí PDO.
 * Implementuje návrhový vzor "Singleton" – připojení se vytvoří jen jednou
 * a všude v aplikaci se používá stejná instance.
 */
class Database {
    // Jediná instance této třídy (singleton)
    private static $instance = null;

    // PDO objekt (samotné připojení k databázi)
    private $pdo;

    /**
     * Konstruktor je private → nelze vytvořit instanci pomocí new zvenku.
     * Připojí se k databázi a uloží PDO objekt.
     */
    private function __construct() {
        // Přihlašovací údaje k databázi
        $host    = "127.0.0.1";           // databázový server
        $db      = "objednavkovy_system"; // název databáze
        $user    = "root";                // výchozí uživatel v XAMPP
        $pass    = "";                    // heslo (ve výchozím stavu prázdné)
        $charset = "utf8mb4";             // správné kódování češtiny a emoji

        // DSN = Data Source Name (řetězec pro PDO)
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        // Volby pro PDO
        $options = [
            // Chyby se budou házet jako výjimky (lepší pro debug)
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Výsledky se budou vracet jako asociativní pole
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        // Vytvoření samotného PDO objektu (připojení k DB)
        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    /**
     * Statická metoda na získání jediné instance PDO
     *
     * @return PDO
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
