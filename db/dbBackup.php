<?php
set_time_limit(0); // Allow script to run forever

$source = __DIR__ . '/../sensordata.db';
$backupDir = __DIR__ . '/dbBackup';

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

while (true) {
    // hledani kopirovanych souboru a posledniho cisla
    $files = glob($backupDir . '/sensordata_*.db');
    $maxNum = 0;
    foreach ($files as $file) {
        if (preg_match('/sensordata_(\d+)\.db$/', $file, $matches)) {
            $num = (int)$matches[1];
            if ($num > $maxNum) {
                $maxNum = $num;
            }
        }
    }
    $nextNum = $maxNum + 1;
    $dest = $backupDir . "/sensordata_{$nextNum}.db";

    // kopirovani souboru
    if (file_exists($source)) {
        copy($source, $dest);
    }

    sleep(60 * 60 * 24); // ceka 24h
}
?>