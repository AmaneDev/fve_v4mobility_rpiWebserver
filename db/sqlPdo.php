<?php
require_once $_SERVER['DOCUMENT_ROOT']."/config.inc";
//konfigurační soubor kde vytváříme objekt $pdo pro připojení k databází - inicializujeme typ db(sqlite) a cestu k souboru


class DatabaseConnection {
    private $pdo;
    private $dbFile;

    public function __construct() {
        global $dbPath; // Přístup k proměnné $dbPath definované v config.inc
        $this->dbFile = $_SERVER['DOCUMENT_ROOT'] . $dbPath;
        try {
            $this->pdo = new PDO('sqlite:' . $this->dbFile);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>