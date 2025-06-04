<?php
    error_reporting(E_ALL & ~E_NOTICE);

    $dataSeries = [];
    $errorMessage = '';

    $limit = $conf_historyRecLimit ?? 100;
    $queryParams = http_build_query(['limit' => $limit]);
    $apiUrl = 'http://'.$_SERVER['HTTP_HOST'].'/api/getHomeData.php?' . $queryParams;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

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

    if ($response === false || $httpCode !== 200) {
        $errorMessage = !empty($curlError) ? $curlError : 'Unable to fetch API data.';
    } else {
        $data = json_decode($response, true);

        if ($data && $data['status'] === 'success') {
            $items = $data['data'];

            foreach ($items as $item) {
                $originalDate = $item['datum'] ?? '';
                $formattedDate = !empty($originalDate) ? date('d.m.Y', strtotime($originalDate)) : '';
                $label = htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8');

                foreach ($item as $key => $value) {
                    if ($key === 'datum' || !isset($keyMapping[$key])) {
                        continue;  
                    } 

                    $numericValue = floatval($value);
                    if ($numericValue > 0) {
                        $dataSeries[$key][] = [
                            "label" => $label,
                            "y" => $numericValue
                        ];
                    }
                }
            }
        } else {
            $errorMessage = $data['message'] ?? 'Internal Server Error.';
        }
    }
?>



<script src="/scripts/canvasjs/canvasjs-chart-3.12.12/canvasjs.min.js"></script>

<div id="dataSelectorContainer" class="dataSelectorContainer">
    <h2>Vyberte data:</h2>
    <div id="checkboxContainer">
        <?php foreach ($dataSeries as $key => $series): ?>
            <?php if (isset($keyMapping[$key])): ?>
                <label>
                    <input type="checkbox" class="dataSelector" data-series="<?= htmlspecialchars($key) ?>">
                    <?= htmlspecialchars($keyMapping[$key]) ?>
                </label>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>    

<div id="graphContainer" class="graphContainer">
    <div id="grafSpojnicovy1" class="grafSpojnicovy1"></div>
    <div id="grafSpojnicovy1Data" class="grafSpojnicovy1Data">
        <div id="graphData" class="graphData" style="display: none;">
            <!--<h2>Data:</h2>-->
            <table id="dataTable" class="dataTable">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <?php foreach ($dataSeries as $key => $series): ?>
                            <?php if (isset($keyMapping[$key])): ?>
                                <th><?= htmlspecialchars($keyMapping[$key]) ?></th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['datum'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <?php foreach ($dataSeries as $key => $series): ?>
                                <?php if (isset($keyMapping[$key])): ?>
                                    <td><?= htmlspecialchars($item[$key] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <img src="/img/down-circle.svg" alt="expand" onclick="showGraphData()">
    </div>
</div>

<script>

    function showGraphData() {
        const graphData = document.getElementById('graphData');
        const graphDataImg = document.querySelector('#grafSpojnicovy1Data img');
        const graphContainer = document.getElementById('graphContainer');
        if (graphData.style.display === 'none') {
            graphData.style.display = 'block';
            graphDataImg.src = '/img/down-circle.svg';
            graphContainer.style.height = '520px';
            graphDataImg.style.transform = 'rotate(180deg)';
        } else {
            graphData.style.display = 'none';
            graphDataImg.src = '/img/down-circle.svg';
            graphContainer.style.height = '400px';
            graphDataImg.style.transform = 'rotate(0deg)';
        }
    }
</script>
    

<?php if (!empty($errorMessage)): ?>
    <div style="color: red; margin-top: 10px;">
        Chyba: <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<script>
window.onload = function () {
    const rawData = <?php echo json_encode($dataSeries, JSON_NUMERIC_CHECK); ?>;
    const keyMapping = <?php echo json_encode($keyMapping, JSON_UNESCAPED_UNICODE); ?>;
    const selectedSeries = {};

    const chart = new CanvasJS.Chart("grafSpojnicovy1", {
        animationEnabled: true,
        theme: "light2",
        title: { text: "Spojnicový graf" },
        axisY: {
            title: "Hodnota",
            logarithmic: true,
            includeZero: true,
            labelFormatter: addSymbols
        },
        toolTip: {
            shared: true
        },
        legend: {
            cursor: "pointer",
            itemclick: function (e) {
                e.dataSeries.visible = !(typeof e.dataSeries.visible === "undefined" || e.dataSeries.visible);
                chart.render();
            }
        },
        data: []
    });

    document.querySelectorAll('.dataSelector').forEach(cb => {
        cb.addEventListener('change', function () {
            const key = this.dataset.series;
            if (this.checked) {
                selectedSeries[key] = {
                    type: "line",
                    name: keyMapping[key] || key,
                    showInLegend: true,
                    dataPoints: rawData[key],
                    markerSize: 0
                };
            } else {
                delete selectedSeries[key];
            }
            chart.options.data = Object.values(selectedSeries);
            chart.render();
        });
    });

    function addSymbols(e) {
        const suffixes = ["", "K", "M", "B"];
        const order = Math.max(Math.floor(Math.log(Math.abs(e.value)) / Math.log(1000)), 0);
        return CanvasJS.formatNumber(e.value / Math.pow(1000, order)) + suffixes[order];
    }
};
</script>

</body>
</html>
