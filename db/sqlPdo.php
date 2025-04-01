<?php
//konfigurační soubor kde vytváříme objekt $pdo pro připojení k databází - inicializujeme typ db(sqlite) a cestu k souboru


class DatabaseConnection {
    private $pdo;
    private $dbFile = __DIR__ . '/../sensordata.db';

    public function __construct() {
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