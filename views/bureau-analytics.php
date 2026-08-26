<div class="container-fluid px-4">
    <h5>የቢሮና ተጠሪ ተቋማት ዝርዝር እና የመኪና ብዛት</h5>

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
                                    <th>የተቋማት ዝርዝር</th>
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
                    <h6 class="m-0 font-weight-bold text-dark">በተቋም ዓይነት የመኪና ብዛት ቻርት</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="bureausBarChart"></canvas>
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

    // በቢሮ ዓይነት (CallerName) እየሰበሰበ አጠቃላይ የመኪና ብዛትን ማሰላት
    const aggregatedData = {};
    rawData.forEach(item => {
        let type = item.CallerName ? item.CallerName.trim() : 'ሌላ';
        let vehicles = parseInt(item.vehicle_count) || 0;
        
        aggregatedData[type] = (aggregatedData[type] || 0) + vehicles;
    });

    const canvasElement = document.getElementById('bureausBarChart');
    if (!canvasElement) {
        console.error("የካንቫስ ኤለመንት (bureausBarChart) አልተገኘም!");
        return;
    }

    const ctx = canvasElement.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        plugins: [ChartDataLabels], // የቁጥሮቹን ፕለጊን እዚህ እናገናኘዋለን
        data: {
            labels: Object.keys(aggregatedData),
            datasets: [{
                label: 'አጠቃላይ የመኪና ብዛት',
                data: Object.values(aggregatedData),
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
                    suggestedMax: 10, // የቻርቱ ከፍታ ከፍተኛው ቁጥር ካለው ትንሽ ከፍ እንዲል በማድረግ ቁጥሮቹ እንዳይጋረዱ ያደርጋል
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: { display: false },
                datalabels: { // ቁጥሮቹ ከባሮቹ አናት ላይ እንዲቀመጡ የሚረዳው ውቅር
                    anchor: 'end',
                    align: 'top',
                    color: '#333',
                    font: {
                        weight: 'bold',
                        size: 13
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