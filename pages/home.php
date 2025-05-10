<section class="home">
    <div class="sunLeft">
        <div class="weatherAnim" id="weatherAnim">
            <div class="weatherAnimContainer" id="weatherAnimContainer">
                <img src="/img/weatherWidget/sun.svg" alt="" class="sun">
                <img class="cloud" src="/img/weatherWidget/cloud.svg" alt="" style="display:none" class="cloud">
            </div>
            <h1>Solární tracker</h1>
        </div>
    </div>  
    <div class="statsRight">
        <div class="statsTable">
            <h1>Přehled</h1>
            <div class="statsGridCols" id="homeTiles">
                <?php
                    require_once $_SERVER['DOCUMENT_ROOT'].'/webBase/TileData.php';

                    $apiUrl = 'http://localhost/api/getHomeData.php?limit=1';
                    $fetcher = new TileData($apiUrl);
                    $items = $fetcher->getItems();
                    $errorMessage = $fetcher->getErrorMessage();

                    $keyMapping = [
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
                    ];

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
                const keyMapping = {
                    datum: 'Datum údajů',
                    cas: 'Aktuální čas',
                    intenzitaS: 'Slunce - LUX',
                    vstupVI: 'Vstup 1',
                    vstupV2: 'Vstup 2',
                    vstupV3: 'Vstup 3',
                    bocniVI: 'Boční napětí',
                    zatezVI: 'Napěťová zátěž',
                    proudI: 'Proud 1',
                    vstupV1: 'Vstup 1',
                    bocniV1: 'Boční 1',
                    zatezV1: 'Zátěž 1',
                    proud1: 'Proud 1',
                    bocniV2: 'Boční 2',
                    zatezV2: 'Zátěž 2',
                    proud2: 'Proud 2',
                    bocniV3: 'Boční 3',
                    zatezV3: 'Zátěž 3',
                    proud3: 'Proud 3'
                };

                const resultDiv = document.getElementById('homeTiles');
                resultDiv.innerHTML = '';

                if (data.status === 'success' && data.data.length > 0) {
                    data.data.forEach(item => {
                        for (const [key, value] of Object.entries(item)) {
                            const displayKey = keyMapping[key] || key;
                            const tile = document.createElement('div');
                            tile.className = `tile tile-${key}`;
                            tile.innerHTML = `<h1>${displayKey}</h1><p>${value ?? 'N/A'}</p>`;
                            resultDiv.appendChild(tile);
                        }
                    });
                } else {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'errDiv';
                    errorDiv.innerHTML = `
                        <p>${data.message || 'No data available.'}</p>
                        <img class="err500" src="/img/500.svg">
                    `;
                    resultDiv.appendChild(errorDiv);
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    function getRealTime() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        return now.toLocaleTimeString('cs-CZ', options);
    }

    function updateTimeOnTile(){
        const timeTile = document.querySelector('.tile-cas');
        if (timeTile) {
            const p = timeTile.querySelector('p');
            if (p) p.textContent = getRealTime();
        }
    }

    refreshData();
    setInterval(refreshData, 1500);    // Každých 1,5s nové hodnoty
    setInterval(updateTimeOnTile, 1000); // Každou sekundu aktualizace času
</script>
