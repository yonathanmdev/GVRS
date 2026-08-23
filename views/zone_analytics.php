<div class="container-fluid px-4">
    <h5>የአስተዳደራዊ መዋቅር ዝርዝር እና የመኪና ብዛት</h5>

    <div class="row">
        <!-- 1. የሰንጠረዥ ክፍል (ግራ በኩል) -->
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow-sm mb-4">

                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>ተ.ቁ</th>
                                    <th>የመዋቅር ስም</th>
                                    <th>ዓይነት</th>
                                    <th>የመኪና ብዛት</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reportData) && is_array($reportData)): ?>
                                    <?php $i = 1; foreach ($reportData as $row): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($row['Name'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['CallerName'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['vehicle_count'] ?? 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-danger">ምንም መረጃ አልተገኘም!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. የቻርት/ግራፍ ክፍል (ቀኝ በኩል) -->
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-dark">በአስተዳደራዊ መዋቅር ዓይነት የመኪና ብዛት ቻርት</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="zoneBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js እና DataLabels ፕለጊን ከ CDN (በሲስተሙ nonce ደህንነት ታጅቧል) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js" nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></script>

<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener("DOMContentLoaded", function() {
    const rawData = <?php echo json_encode($reportData ?? []); ?>;
    
    if (!rawData || rawData.length === 0) {
        console.warn("ቻርቱ የሚስለው ዳታ አልተገኘም!");
        return;
    }

    // በ CallerName (የቅርንጫፍ ዓይነት) እየሰበሰበ አጠቃላይ የመኪና ብዛትን ማሰላት
    const aggregatedData = {};
    rawData.forEach(item => {
        let callerType = item.CallerName ? item.CallerName.trim() : 'ሌላ';
        let vehicles = parseInt(item.vehicle_count) || 0;
        
        aggregatedData[callerType] = (aggregatedData[callerType] || 0) + vehicles;
    });

    const canvasElement = document.getElementById('zoneBarChart');
    if (!canvasElement) {
        console.error("የካንቫስ ኤለመንት (zoneBarChart) አልተገኘም!");
        return;
    }

    const ctx = canvasElement.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: {
            labels: Object.keys(aggregatedData), // የ CallerName ዝርዝር በ X-axis ላይ ይወጣል
            datasets: [{
                label: 'አጠቃላይ የመኪና ብዛት',
                data: Object.values(aggregatedData), // በእያንዳንዱ CallerName የተጠቃለለው የመኪና ድምር
                backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(13, 202, 240, 0.7)'
                ],
                borderColor: [
                    'rgb(13, 110, 253)',
                    'rgb(25, 135, 84)',
                    'rgb(255, 193, 7)',
                    'rgb(220, 53, 69)',
                    'rgb(13, 202, 240)'
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 5,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    color: '#333',
                    font: {
                        weight: 'bold',
                        size: 12
                    },
                    formatter: function(value) {
                        return value;
                    }
                }
            }
        }
    });
});
</script>