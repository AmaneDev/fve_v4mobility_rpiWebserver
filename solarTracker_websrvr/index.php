<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">   
    <title><?php echo $title ?></title>
</head>
<body>
    <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    ?>
    <?php
        $title = "Solární tracker - webserver";
        require_once $_SERVER['DOCUMENT_ROOT']. '/db/sqlPdo.php';
        require_once $_SERVER['DOCUMENT_ROOT']. '/db/dbFetch.php';

        $page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 'home'; //pokud není page -> je home, pokud je page -> je to co je v GETu
        $page = preg_replace('/[^a-zA-Z0-9_]/', '', $page); //pouze alfanumerické znaky a podtržítka
        
        if($page == "home") include_once $_SERVER['DOCUMENT_ROOT']. '/pages/home.php';

    ?>
</body>
</html>