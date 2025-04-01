<?php

class DataFetcher {
    private $db;

    public function __construct() {
        $databaseConnection = new DatabaseConnection();
        $this->db = $databaseConnection->getConnection();
    }

    public function fetchAllFromTracker() {
        try {
            $query = "SELECT * FROM data";
            $stmt = $this->db->prepare($query);   //připravíme si dotaz (statement) a následně jej executneme - ochrana proti SQL inj.
            $stmt->execute(); 
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Chyba při provádění SQL dotazu: " . $e->getMessage());
        }
    }
}


//echo json_encode($data); //dbg

?>