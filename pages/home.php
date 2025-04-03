<?php
    error_reporting(E_ERROR | E_PARSE); // zakazani warningů a notices

    $apiUrl = '../api/getHomeData.php';
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
                    <strong><?php echo htmlspecialchars($key); ?>:</strong>
                    <div><?php echo htmlspecialchars($value ?? 'N/A'); ?></div>
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
                const resultDiv = document.getElementById('homeTiles');
                resultDiv.innerHTML = ''; // clearne predchozi content

                if (data.status === 'success' && data.data.length > 0) {
                    let tileIndex = 1; // začneme iteraci od 1
                    data.data.forEach(item => {
                        for (const [key, value] of Object.entries(item)) {
                            const tile = document.createElement('div');
                            tile.className = `tile tile-${tileIndex} tile-${key}`; // přidáme classu s indexem a classu podle $key
                            tile.innerHTML = `<strong>${key}:</strong><div>${value ?? 'N/A'}</div>`;
                            resultDiv.appendChild(tile);
                            tileIndex++; // interacni index pro classu tilu - do budoucna vyuzijem for sure 
                        }
                    });
                } else {
                    const errorDiv = document.createElement('div');
                    errorDiv.style.color = 'red';
                    errorDiv.textContent = data.message || 'No data available.';
                    resultDiv.appendChild(errorDiv);
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    refreshData();
    setInterval(refreshData, 10000);   //refreshujem kadych 10s (10000ms)
</script>
