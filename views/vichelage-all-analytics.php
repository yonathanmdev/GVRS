<div class="container-fluid px-4">
    <h4>የተሽከርካሪ በዕድሜ ሪፖርት</h4>

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
                                    <th>የተሽከርካሪ ዕድሜ</th>
                                    <th>የመኪና ብዛት</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reportData) && is_array($reportData)): ?>
                                    <?php $i = 1; foreach ($reportData as $row): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($row['vichel_age'] ?? '') ?></td>
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
                    <h6 class="m-0 font-weight-bold text-dark">በተሽከርካሪ ዕድሜ ቻርት</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <!-- ID ከጃቫስክሪፕቱ ጋር እንዲመሳሰል ተደረገ -->
                        <canvas id="vehicleAgeChart"></canvas>
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

    const labels = rawData.map(item => item.vichel_age);
    const dataValues = rawData.map(item => item.vehicle_count);

    // እንደ ዕድሜ ክልሉ (Label) ሁኔታ ከለሮችን በሉፕ መምረጥ
    const backgroundColors = labels.map(ageLabel => {
        let label = ageLabel ? ageLabel.trim() : '';
        if (label.includes('0 - 5')) {
            return 'rgba(25, 135, 84, 0.7);';   // አረንጓዴ (ከ 0 - 5 ዓመት)
        } else if (label.includes('6 - 15')) {
            return 'rgba(13, 110, 253, 0.7)';  // ሰማያዊ
        } else if (label.includes('16 - 25')) {
            return 'rgba(13, 202, 240, 0.7)';  // ሲያን (Cyan)
        } else if (label.includes('26 - 35')) {
            return 'rgba(255, 193, 7, 0.7)';   // ቢጫ/ብርቱካናማ
        } else if (label.includes('36') || label.includes('ከዚሁ በላይ')) {
            return 'rgba(220, 53, 69, 0.7)';   // ቀይ (36 እና ከዚያ በላይ)
        } else {
            return 'rgba(108, 117, 125, 0.7)'; // ነባሪ ግራጫ
        }
    });

    const borderColors = labels.map(ageLabel => {
        let label = ageLabel ? ageLabel.trim() : '';
        if (label.includes('0 - 5')) {
            return 'rgb(25, 135, 84)';
        } else if (label.includes('6 - 15')) {
            return 'rgb(13, 110, 253)';
        } else if (label.includes('16 - 25')) {
            return 'rgb(13, 202, 240)';
        } else if (label.includes('26 - 35')) {
            return 'rgb(255, 193, 7)';
        } else if (label.includes('36') || label.includes('ከዚሁ በላይ')) {
            return 'rgb(220, 53, 69)';
        } else {
            return 'rgb(108, 117, 125)';
        }
    });

    const canvasElement = document.getElementById('vehicleAgeChart');
    if (!canvasElement) {
        console.error("የካንቫስ ኤለመንት (vehicleAgeChart) አልተገኘም!");
        return;
    }

    const ctx = canvasElement.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: {
            labels: labels,
            datasets: [{
                label: 'የተሽከርካሪ ብዛት',
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