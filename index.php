<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">  
    <?php $title = "Solární tracker - webserver"; ?> 
    <link rel="icon" href="/img/favicon.svg" type="image/x-icon">
    <title><?php echo $title ?></title>
</head>
<body>
    <?php
        //$title = "Solární tracker - webserver";
        require_once $_SERVER['DOCUMENT_ROOT']. '/db/sqlPdo.php';
        require_once $_SERVER['DOCUMENT_ROOT']. '/db/dbFetch.php';

        $page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 'home'; //pokud není page -> je home, pokud je page -> je to co je v GETu
        $page = preg_replace('/[^a-zA-Z0-9_]/', '', $page); //pouze alfanumerické znaky a podtržítka
        
        echo '<header>'; 
        require_once $_SERVER['DOCUMENT_ROOT']. '/webBase/header.php'; //header
        echo '</header>';

        echo '<div class="page">';
        if($page == "home") include_once $_SERVER['DOCUMENT_ROOT']. '/pages/home.php';
        echo '</div>';


        echo '<footer>';
        require_once $_SERVER['DOCUMENT_ROOT']. '/webBase/footer.php'; //footer
        echo '</footer>';

    ?>
</body>
</html>