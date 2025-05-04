<?php
    //error_reporting(E_ERROR | E_PARSE); // zakazani warningů a notices

    $apiUrl = 'http://localhost/api/getHomeData.php?limit=1'; 
    //echo $apiUrl; //dbg
    $response = file_get_contents($apiUrl);  //fetchneme si data z API (api vrací JSON)
    $data = json_decode($response, true);

    if ($data && $data['status'] === 'success') {  //jen kontrolujeme zda jsme dostali success při requestu, pokud ne, throwneme chybu
        $items = $data['data'];   //datovou část (s SQL odpovědí) uložíme do proměnné $items, později ji budeme iterovat
    } else {
        $items = []; //přiřadíme prázdný array, aby php neřvalo warningy ohledně undefined var (od php 7 je to pain, i když jednodušší je to potlačit pomocí error_reporting metody (což i dělám) ale debugging je pak čistší bez warns takže tak ...)
        $errorMessage = $data['message'] ?? 'Internal Server Error.';  //pokud jsme nedostali success (ale error), tak vyhodíme error message o chybe serveru (což fakticky je prava bo je to chyba API části :D)
    }
?>


<div id="homeTiles">
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
            <?php foreach ($item as $key => $value): ?>
                <div class="tile">
                    <h1><?php echo htmlspecialchars($key); ?></h1>
                    <p><?php echo htmlspecialchars($value ?? 'N/A'); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="errDiv">
            <?php echo htmlspecialchars($errorMessage ?? 'No data available.'); ?>
            <img class="err500" src="../../img/500.svg">
        </div>
    <?php endif; ?>
</div>
<script>
    function refreshData() {
        fetch('<?php echo $apiUrl; ?>')
            .then(response => response.json())
            .then(data => {
                const keyMapping = {
                    datum: 'Datum údajů',
                    cas: 'Aktuální čas',
                    intenzitaS: 'Slunce - LUX',
                    vstupVI: 'Vstup 1', //vstupní napětí pro intenzitu
                    vstupV2: 'Vstup 2',
                    vstupV3: 'Vstup 3',
                    //vstupVI: 'Vstup 4',   TODO: zruseni i v DB!!
                    bocniVI: 'Boční napětí',
                    zatezVI: 'Napěťová zátěž',
                    proudI: 'Proud 1',
                    vstupV1: 'Vstup 1', //proudový sensor pro intenzitu
                    bocniV1: 'Boční 1',
                    zatezV1: 'Zátěž 1',
                    proud1: 'Proud 1',
                    vstupV2: 'Vstup 2',
                    bocniV2: 'Boční 2',
                    zatezV2: 'Zátěž 2',
                    proud2: 'Proud 2',
                    vstupV3: 'Vstup 3',
                    bocniV3: 'Boční 3',
                    zatezV3: 'Zátěž 3',
                    proud3: 'Proud 3'
                    
                };

                const resultDiv = document.getElementById('homeTiles');
                resultDiv.innerHTML = ''; // clear previous content

                if (data.status === 'success' && data.data.length > 0) {
                    let tileIndex = 1; // start iteration from 1
                    data.data.forEach(item => {
                        for (const [key, value] of Object.entries(item)) {
                            const displayKey = keyMapping[key] || key; // použije namapované klíče nebo fallbackne na originalni z JSON resp
                            const tile = document.createElement('div');
                            tile.className = `tile tile-${tileIndex} tile-${key}`; // přidáme classu s indexem a classu podle $key
                            tile.innerHTML = `<h1>${displayKey}</h1><p>${value ?? 'N/A'}</p>`;
                            resultDiv.appendChild(tile);
                            tileIndex++; // interacni index pro classu tilu - do budoucna vyuzijem maybe
                        }
                    });
                } else {
                    const errorDiv = document.createElement('div');
                    errorDiv.style.color = 'red';
                    errorDiv.textContent = data.message || 'No data available.';
                    resultDiv.appendChild(errorDiv);
                }
            })
            /*
            .then(data => {    //bez mappingu
                const resultDiv = document.getElementById('homeTiles');
                resultDiv.innerHTML = ''; // clearne predchozi content

                if (data.status === 'success' && data.data.length > 0) {
                    let tileIndex = 1; // začneme iteraci od 1
                    data.data.forEach(item => {
                        for (const [key, value] of Object.entries(item)) {
                            const tile = document.createElement('div');
                            tile.className = `tile tile-${tileIndex} tile-${key}`; 
                            tile.innerHTML = `<h1>${key}</h1><p>${value ?? 'N/A'}</p>`;
                            resultDiv.appendChild(tile);
                            tileIndex++; 
                        }
                    });
                } else {
                    const errorDiv = document.createElement('div');
                    errorDiv.style.color = 'red';
                    errorDiv.textContent = data.message || 'No data available.';
                    resultDiv.appendChild(errorDiv);
                }
            })*/
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    function getRealTime() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        return now.toLocaleTimeString('cs-CZ', options); // vrátí čas v českém formátu
    }
    function updateTimeOnTile(){
        const timeTile = document.querySelector('.tile-cas'); // najde tile s classou time
        if (timeTile) {
            timeTile.querySelector('p').textContent = getRealTime(); // aktualizuje čas v tile
        }
    }

    refreshData();
    setInterval(refreshData, 1500);   //refreshujem kadych 10s (10000ms)
    setInterval(updateTimeOnTile, 1); // aktualizujeme čas každou sekundu
</script>
