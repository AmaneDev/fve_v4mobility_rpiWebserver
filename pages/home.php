<script src="/scripts/weatherAnim.js"></script>
<section class="home">
    <div class="sunLeft">
        <div class="weatherAnim" id="weatherAnim">
            <div class="weatherAnimContainer" id="weatherAnimContainer">
                <img src="/img/weatherWidget/sun.svg" alt="" class="sun" >
                <img class="cloud" src="/img/weatherWidget/cloud.svg" alt=""  class="cloud" style="display:none">
            </div>
            <!--<h1>Solární tracker</h1>-->
        </div>
    </div>  
    <div class="statsRight">
        <div class="statsTable">
            <h1 class="statsHeader">Přehled</h1>
            <div class="statsGridCols" id="homeTiles">
                <?php
                    require_once $_SERVER['DOCUMENT_ROOT'].'/webBase/TileData.php';

                    $apiUrl = 'http://'.$_SERVER['HTTP_HOST'].'/api/getHomeData.php?limit=1';
                    $fetcher = new TileData($apiUrl);
                    $items = $fetcher->getItems();
                    $errorMessage = $fetcher->getErrorMessage();

                    /*$keyMapping = [
                        'datum' => 'Datum údajů',
                        'cas' => 'Aktuální čas',
                        'intenzitaS' => 'Slunce - LUX',
                        'vstupVI' => 'Vstup 1',
                        'vstupV2' => 'Vstup 2',
                        'vstupV3' => 'Vstup 3',
                        'bocniVI' => 'Boční napětí',
                        'zatezVI' => 'Napěťová zátěž',
                        'proudI' => 'Proud 1',
                        'vstupV1' => 'Vstup 1',
                        'bocniV1' => 'Boční 1',
                        'zatezV1' => 'Zátěž 1',
                        'proud1' => 'Proud 1',
                        'bocniV2' => 'Boční 2',
                        'zatezV2' => 'Zátěž 2',
                        'proud2' => 'Proud 2',
                        'bocniV3' => 'Boční 3',
                        'zatezV3' => 'Zátěž 3',
                        'proud3' => 'Proud 3'
                    ];*/

                    if (!empty($items)) {
                        $item = $items[0];

                        foreach ($item as $key => $value) {
                            $displayKey = $keyMapping[$key] ?? $key;
                            echo '<div class="tile tile-' . htmlspecialchars($key) . '">';
                            echo '<h1>' . htmlspecialchars($displayKey) . '</h1>';
                            echo '<p>' . htmlspecialchars($value ?? 'N/A') . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="errDiv">';
                        echo htmlspecialchars($errorMessage ?? 'No data available.');
                        echo '<img class="err500" src="/img/500.svg">';
                        echo '</div>';
                    }
                ?>
            </div>
        </div>
    </div>
</section>

<script>
    function refreshData() {
    fetch('<?php echo $apiUrl; ?>')
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('homeTiles');
            resultDiv.innerHTML = '';

            if (data.status !== 'success' || data.data.length === 0) {
                return showError(resultDiv, data.message || 'Žádná data nejsou k dispozici.');
            }

            const item = data.data[0];

            // mapovani klicu
            const keyMapping = {
                datum: 'Datum údajů',
                cas: 'Aktuální čas',
                intenzitaS: 'Slunce [LUX]',
                vstupVI: 'Vstup 1',
                vstupV2: 'Vstup 2',
                vstupV3: 'Vstup 3',
                bocniVI: 'Boční napětí',
                zatezVI: 'Napěťová zátěž',
                proudI: 'Proud 1',
            };

        
            const aggregates = {
                zatez: { label: 'Zátěž [V]', keys: [] },
                proud: { label: 'Proud [A]', keys: [] },
                bocni: { label: 'Boční napětí [V]', keys: [] },
                vstup: { label: 'Vstupní napětí [V]', keys: [] },
            };

            const otherTiles = [];

            // Sort values into aggregates or others
            for (const [key, value] of Object.entries(item)) {
                let matched = false;

                for (const aggKey in aggregates) {
                    if (key.toLowerCase().startsWith(aggKey)) {
                        aggregates[aggKey].keys.push(parseFloat(value) || 0);
                        matched = true;
                        break;
                    }
                }

                if (!matched) {
                    otherTiles.push({
                        key,
                        label: keyMapping[key] || key,
                        value: value ?? 'N/A'
                    });
                }
            }


            // renderovani ostatnich dlazdic (bez scitani)
            otherTiles.forEach(tile => {
                renderTile(resultDiv, tile.key, tile.label, tile.value);
            });

            // renderovani agregovanych dlazdic s hodnotami
            for (const [aggKey, agg] of Object.entries(aggregates)) {
                const total = agg.keys.reduce((a, b) => a + b, 0);
                //renderTile(resultDiv, aggKey, agg.label, total.toFixed(2));  //dve desetinny mista
                renderTile(resultDiv, aggKey, agg.label, Math.round(total)); //zaokroouhleni

            }

            

        })
        .catch(error => { //fallback pro error
            console.error('Error fetching data:', error);
            showError(document.getElementById('homeTiles'), 'Chyba při načítání dat.');
        });
    }

    function renderTile(container, key, label, value) {   //finalni renderovani dlazdic
        const tile = document.createElement('div');
        tile.className = `tile tile-${key}`;
        tile.innerHTML = `<h1>${label}</h1><p>${value}</p>`;
        container.appendChild(tile);
    }

    function showError(container, message) {  //zobrazi 500 pri chybe
        const errorDiv = document.createElement('div');
        errorDiv.className = 'errDiv';
        errorDiv.innerHTML = `
            <p>${message}</p>
            <img class="err500" src="/img/500.svg">
        `;
        container.appendChild(errorDiv);
    }


    function getRealTime() {  //ziskani real time 
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        return now.toLocaleTimeString('cs-CZ', options);
    }

    function updateTimeOnTile(){   //aktualizuje cas primo na dlazdici z casem asynchronne oproti zbytku
        const timeTile = document.querySelector('.tile-cas');
        if (timeTile) {
            const p = timeTile.querySelector('p');
            if (p) p.textContent = getRealTime();
        }
    }

    refreshData();
    const item = { intenzitaS: <?php echo json_encode($items[0]['intenzitaS'] ?? null); ?> };
    getWeatherAnim(item);
    setInterval(refreshData, 10000);    // Každých 10s nové hodnoty
    setInterval(updateTimeOnTile, 1000); // Každou sekundu aktualizace času
</script>

<?php //echo json_encode($items[0]['intenzitaS'] ?? null); //dbg ?>