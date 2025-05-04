<?php
header('Content-Type: application/json'); // Vracej JSON jako response
require_once __DIR__ . '/connectIncludes.php';   //Připojení k databází a includy (soubory s třídou DataFetcher a sqlPdo)

/*ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);*/ //dbg


if ($_SERVER['REQUEST_METHOD'] !== 'GET') { //:D
    http_response_code(405); // pokud ne-GET requestnuto, vrati 405 (Method not Allowed)
    echo json_encode([
        'status' => 'error',
        'message' => 'Method Not Allowed'
    ]);
    exit;
}

try {
    $limit = $_GET['limit'] ?? 10; //pokud není limit v GETu, nastavíme defaultně na 10
    $dataFetcher = new DataFetcher(); //vytvoří objekt $dataFetcher podle třídy DataFetcher   (nachází se v root/dbFetch.php)
    $data = $dataFetcher->fetchAllFromTracker($limit); // fetchne data pomocí metody fetchAllFromTracker() z třídy DataFetcher 
    if (empty($data)) {
        throw new Exception('No data returned from the database'); //pokud nevrátí žádná data, chyba...
    }

    //JSON response
    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);
} catch (Exception $e) {
    http_response_code(500); //pokud jsme thrownuli exception  (nejsou data (v try bloku) - hoď Err500)
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
