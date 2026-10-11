<div class="container-fluid px-4">
    <h4>የተሽከርካሪ በአገልግሎት ዓይነት እና ሪፖርት</h4>

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
                                    <th>የአገልግሎት ዓይነት</th>
                                    <th>የመኪና ብዛት</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reportData) && is_array($reportData)): ?>
                                    <?php $i = 1; foreach ($reportData as $row): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($row['service_name'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['vehicle_count'] ?? 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-danger">ምንም መረጃ አልተገኘም!</td>
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
                    <h6 class="m-0 font-weight-bold text-dark">በተሽከርካሪ የአግልግሎት አይነት ቻርት</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="vehicleStatusChart"></canvas>
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

    // በ service_name (የአገልግሎት ዓይነት) እየሰበሰበ አጠቃላይ የመኪና ብዛትን ማሰላት
    const aggregatedData = {};
    rawData.forEach(item => {
        let serviceName = item.service_name ? item.service_name.trim() : 'ሌላ';
        let vehicles = parseInt(item.vehicle_count) || 0;
        
        aggregatedData[serviceName] = (aggregatedData[serviceName] || 0) + vehicles;
    });

    const labels = Object.keys(aggregatedData);
    const dataValues = Object.values(aggregatedData);

    // ለእያንዳንዱ የአገልግሎት ዓይነት ተስማሚ ከለር በስሙ (Label) እየመረጠ የሚሰጥ ሎጂክ
    const backgroundColors = labels.map(label => {
        if (label.includes('የህዝብ')) {
            return 'rgba(13, 110, 253, 0.7)';   // ሰማያዊ
        } else if (label.includes('የጭነት')) {
            return 'rgba(255, 193, 7, 0.7)';    // ቢጫ
        } else if (label.includes('የእሳት አደጋ')) {
            return 'rgba(220, 53, 69, 0.7)';    // ቀይ
        } else if (label.includes('የኮንስትራክሽን እና ግንባታ')) {
            return 'rgba(108, 117, 125, 0.7)'; // ግራጫ
        } else if (label.includes('የህክምና አገልግሎት/አምቡላንስ') || label.includes('አምቡላንስ')) {
            return 'rgba(25, 135, 84, 0.7)';    // አረንጓዴ
        } else if (label.includes('የፍሳሽ ማስወገጃ') || label.includes('የፍሳሽ ማስወገጃ')) {
            return 'rgba(253, 126, 20, 0.7)';   // ብርቱካናማ
        } else if (label.includes('የደርቅ ቆሻሻ ማስወገጃ')) {
            return 'rgba(111, 66, 193, 0.7)';   // ሃምራዊ (Purple)
        } else if (label.includes('Shower truck(የፈሳሽ ጭነት)')) {
            return 'rgba(13, 202, 240, 0.7)';   // ሲያን (Cyan)
        } else {
            return 'rgba(102, 16, 242, 0.7)';   // ሌላ ልዩ ከለር
        }
    });

    const borderColors = labels.map(label => {
        if (label.includes('የህዝብ')) {
            return 'rgb(13, 110, 253)';
        } else if (label.includes('የጭነት')) {
            return 'rgb(255, 193, 7)';
        } else if (label.includes('የእሳት አደጋ')) {
            return 'rgb(220, 53, 69)';
        } else if (label.includes('የኮንስትራክሽን እና ግንባታ')) {
            return 'rgb(108, 117, 125)';
        } else if (label.includes('የህክምና አገልግሎት/አምቡላንስ') || label.includes('አምቡላንስ')) {
            return 'rgb(25, 135, 84)';
        } else if (label.includes('የፍሳሽ ማስወገጃ') || label.includes('የፍሳሽ ማስወገጃ')) {
            return 'rgb(253, 126, 20)';
        } else if (label.includes('የደርቅ ቆሻሻ ማስወገጃ')) {
            return 'rgb(111, 66, 193)';
        } else if (label.includes('Shower truck(የፈሳሽ ጭነት)')) {
            return 'rgb(13, 202, 240)';
        } else {
            return 'rgb(102, 16, 242)';
        }
    });

    const canvasElement = document.getElementById('vehicleStatusChart');
    if (!canvasElement) {
        console.error("የካንቫስ ኤለመንት (vehicleStatusChart) አልተገኘም!");
        return;
    }

    const ctx = canvasElement.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: {
            labels: labels,
            datasets: [{
                label: 'አጠቃላይ የመኪና ብዛት',
                data: dataValues,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
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
                    suggestedMax: 1000,
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