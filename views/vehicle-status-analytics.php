<div class="container-fluid px-4">
    <h4>የተሽከርካሪ ሁኔታ ዝርዝር እና ሪፖርት</h4>

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
                                    <th>ተሽከርካሪው ያለበት ሁኔታ</th>
                                    <th>የመኪና ብዛት</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reportData) && is_array($reportData)): ?>
                                    <?php $i = 1; foreach ($reportData as $row): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($row['CallerName'] ?? '') ?></td>
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
                    <h6 class="m-0 font-weight-bold text-dark">በተሽከርካሪ ሁኔታ የብዛት ቻርት</h6>
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

    // በ CallerName (የተሽከርካሪ ሁኔታ) እየሰበሰበ አጠቃላይ የመኪና ብዛትን ማሰላት
    const aggregatedData = {};
    rawData.forEach(item => {
        let statusName = item.CallerName ? item.CallerName.trim() : 'ሌላ';
        let vehicles = parseInt(item.vehicle_count) || 0;
        
        aggregatedData[statusName] = (aggregatedData[statusName] || 0) + vehicles;
    });

    const labels = Object.keys(aggregatedData);
    const dataValues = Object.values(aggregatedData);

    // ለእያንዳንዱ የተሽከርካሪ ሁኔታ ተስማሚ ከለር በሁኔታው ስም (Label) እየመረጠ የሚሰጥ ሎጂክ
    const backgroundColors = labels.map(label => {
        if (label.includes('በስራ ላይ ያለ')) {
            return 'rgba(25, 135, 84, 0.7)';  // አረንጓዴ (በስራ ላይ ያለ)
        } else if (label.includes('በብልሽት የቆመ')) {
            return 'rgba(255, 193, 7, 0.7)';  // ቢጫ (በብልሽት የቆመ)
        } else if (label.includes('የወደመ')) {
            return 'rgba(220, 53, 69, 0.7)';  // ቀይ (የወደመ)
        } else if (label.includes('የጠፋ')) {
            return 'rgba(108, 117, 125, 0.7)'; // ግራጫ (የጠፋ)
        } else if (label.includes('የተወገደ')) {
            return 'rgba(52, 58, 64, 0.7)';   // ጥቁር/ጽኑ ግራጫ (የተወገደ)
        } else {
            return 'rgba(13, 202, 240, 0.7)';  // ሰማያዊ (ለሌሎች)
        }
    });

    const borderColors = labels.map(label => {
        if (label.includes('በስራ ላይ ያለ')) {
            return 'rgb(25, 135, 84)';
        } else if (label.includes('በብልሽት የቆመ')) {
            return 'rgb(255, 193, 7)';
        } else if (label.includes('የወደመ')) {
            return 'rgb(220, 53, 69)';
        } else if (label.includes('የጠፋ')) {
            return 'rgb(108, 117, 125)';
        } else if (label.includes('የተወገደ')) {
            return 'rgb(52, 58, 64)';
        } else {
            return 'rgb(13, 202, 240)';
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