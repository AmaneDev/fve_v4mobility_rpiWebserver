<?php
    $dataFetcher = new DataFetcher(); //konstuujeme objekt pro načítání dat
    $data = $dataFetcher->fetchAllFromTracker(); //v objektu voláme metodu jeho třídy pro načítání dat

    foreach ($data as $value) {
        echo "<div class='data-row'>";
        echo "<p>Id: " . htmlspecialchars($value['id']) . "</p>";
        echo "<p>Time: " . htmlspecialchars($value['time']) . "</p>";
        echo "<p>Azimuth: " . htmlspecialchars($value['azimuth']) . "</p>";
        echo "<p>Elevation: " . htmlspecialchars($value['elevation']) . "</p>";
        echo "<p>Temperature: " . htmlspecialchars($value['temperature']) . "</p>";
        echo "<p>Humidity: " . htmlspecialchars($value['humidity']) . "</p>";
        echo "</div>";
    }

?>

