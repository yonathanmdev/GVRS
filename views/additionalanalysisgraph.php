<div style="padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; color: #333;">
    
    <!-- 1. የገጽ  തല ር እና ሪፖርት አውርድ አዝራር -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <h2 style="margin: 0; font-size: 22px; color: #2c3e50; font-weight: bold;">የተሽከርካሪዎች አጠቃላይ ዳሽቦርድ እና ሪፖርት</h2>
        <!--<a href="<?= rtrim($_ENV['BASE_URL'], '/') ?>/additionalanalysisgraph" target="_blank" rel="noopener noreferrer" style="background-color: #0d6efd; color: white; padding: 10px 18px; border-radius: 5px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-download"></i> ሪፖርት አውርድ (PDF)
        </a>-->
    </div>

    <!-- አጭር ማጠቃለያ ካርዶች (Summary Cards Grid) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
        
        <!-- አጠቃላይ ተሽከርካሪዎች -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; border-left: 5px solid #0d6efd; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 12px; font-weight: bold; color: #0d6efd; text-transform: uppercase; margin-bottom: 5px;">አጠቃላይ ተሽከርካሪዎች</div>
                <div style="font-size: 24px; font-weight: bold; color: #2c3e50;"><?= $summary['total_vehicles'] ?? null ?></div>
            </div>
            <div style="background: #cfe2ff; color: #0d6efd; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-car fa-lg"></i>
            </div>
        </div>

        <!-- በስራ ላይ ያሉ -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; border-left: 5px solid #198754; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 12px; font-weight: bold; color: #198754; text-transform: uppercase; margin-bottom: 5px;">በስራ ላይ ያሉ</div>
                <div style="font-size: 24px; font-weight: bold; color: #2c3e50;"><?= $summary['active_vehicles'] ?? null ?></div>
            </div>
            <div style="background: #d1e7dd; color: #198754; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle fa-lg"></i>
            </div>
        </div>

        <!-- በጥገና ላይ ያሉ -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; border-left: 5px solid #ffc107; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 12px; font-weight: bold; color: #d39e00; text-transform: uppercase; margin-bottom: 5px;">በብልሽት የቆሙ</div>
                <div style="font-size: 24px; font-weight: bold; color: #2c3e50;"><?= $summary['maintenance_vehicles'] ?? 180 ?></div>
            </div>
            <div style="background: #fff3cd; color: #ffc107; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-tools fa-lg"></i>
            </div>
        </div>

        <!-- ጉዳት የደረሰባቸው -->
        <div style="background: #fff; padding: 20px; border-radius: 8px; border-left: 5px solid #dc3545; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 12px; font-weight: bold; color: #dc3545; text-transform: uppercase; margin-bottom: 5px;">ጉዳት የደረሰባቸው / የቆሙ</div>
                <div style="font-size: 24px; font-weight: bold; color: #2c3e50;"><?= $summary['damaged_vehicles'] ?? 80 ?></div>
            </div>
            <div style="background: #f8d7da; color: #dc3545; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
            </div>
        </div>
    </div>

    <!-- 2. የግራፎች ክፍል (Charts Grid Layout) -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
        
        <!-- የመጀመሪያው ቻርት: የአገልግሎት ዓይነት (Bar Chart) -->
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
                በተሽከርካሪ አገልግሎት ዓይነት ትንተና
            </div>
            <div style="padding: 20px;">
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="serviceTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ሁለተኛው ቻርት: የተሽከርካሪ ሁኔታ (Doughnut Chart) -->
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
                የተሽከርካሪ ወቅታዊ ሁኔታ
            </div>
            <div style="padding: 20px;">
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="statusDoughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        
        <!-- ሦስተኛው ቻርት: የተሽከርካሪ ዕድሜ ክልል (Bar Chart) -->
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
                በተሽከርካሪ ዕድሜ ክልል ሪፖርት
            </div>
            <div style="padding: 20px;">
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="vehicleAgeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- አራተኛው ቻርት: መኪናዎቹ በተመረቱበት ዓ.ም. -->
<div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
        መኪናዎቹ በተመረቱበት ዓ.ም.
    </div>
    <div style="padding: 20px;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>

 <!-- አምስተኛ ቻርት: ምንም አይነት ተሽከርካሪ የሌላቸው ተቋማት. -->
<div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
        ምንም አይነት ተሽከርካሪ የሌላቸው ተቋማት
    </div>
    <div style="padding: 20px;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>


 <!-- ስድስተኛ ቻርት: በየተቋማቱ የወደሙ ተሽከርካሪዎች. -->
<div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
        በየተቋማቱ የወደሙ ተሽከርካሪዎች
    </div>
    <div style="padding: 20px;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>


 <!-- በብዛት ተሽከርካሪ ያላቸው ተቋማት. -->
<div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
        በብዛት ተሽከርካሪ ያላቸው ተቋማት
    </div>
    <div style="padding: 20px;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>



 <!-- ተሽከርካሪዎች የተገዙበት ዓመት -->
<div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
        ተሽከርካሪዎች የተገዙበት ዓመት
    </div>
    <div style="padding: 20px;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>




 <!-- በየዞኑ እየሰሩ ያሉ ተሽከርካሪዎች -->
<div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
        በየዞኑ እየሰሩ ያሉ ተሽከርካሪዎች
    </div>
    <div style="padding: 20px;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>


 <!-- በየዞኑ የወደሙ ተሽከርካሪዎች. -->
<div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
        በየዞኑ የወደሙ ተሽከርካሪዎች
    </div>
    <div style="padding: 20px;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>



 <!-- በሪጅኦ ፖሊታን ከተሞች ያሉ ተሽከርካሪዎች -->
<div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
        በሪጅኦ ፖሊታን ከተሞች ያሉ ተሽከርካሪዎች
    </div>
    <div style="padding: 20px;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>



 <!-- የጠፉ ተሽከርካሪዎች በየተቋሙ -->
<div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #eee; font-weight: bold; color: #2c3e50;">
        በየተቋሙ የወደሙ ተሽከርካሪዎች
    </div>
    <div style="padding: 20px;">
        <div style="position: relative; height: 300px; width: 100%;">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>
<!-- Chart.js እና DataLabels ፕለጊን ከ CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js" nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></script>

<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener("DOMContentLoaded", function() {
// 1. Service Type Bar Chart
    const ctxService = document.getElementById('serviceTypeChart').getContext('2d');
    new Chart(ctxService, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: {
            labels: ['የህዝብ', 'የጭነት', 'የእሳት አደጋ', 'የኮንስትራክሽን እና ግንባታ', 'የህክምና አገልግሎት/አምቡላንስ', 'የፍሳሽ ማስወገጃ', 'የደርቅ ቆሻሻ ማስወገጃ', 'Shower truck(የፈሳሽ ጭነት)'],
            datasets: [{
                label: 'ብዛት',
                data: [<?php echo isset($serviceData[0]['vehicle_count']) ? $serviceData[0]['vehicle_count'] : 0; ?>, <?php echo isset($serviceData[1]['vehicle_count']) ? $serviceData[1]['vehicle_count'] : 0; ?>, <?php echo isset($serviceData[2]['vehicle_count']) ? $serviceData[2]['vehicle_count'] : 0; ?>, <?php echo isset($serviceData[3]['vehicle_count']) ? $serviceData[3]['vehicle_count'] : 0; ?>, <?php echo isset($serviceData[4]['vehicle_count']) ? $serviceData[4]['vehicle_count'] : 0; ?>, <?php echo isset($serviceData[5]['vehicle_count']) ? $serviceData[5]['vehicle_count'] : 0; ?>, <?php echo isset($serviceData[6]['vehicle_count']) ? $serviceData[6]['vehicle_count'] : 0; ?>, <?php echo isset($serviceData[7]['vehicle_count']) ? $serviceData[7]['vehicle_count'] : 0; ?>],
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: 'rgb(13, 110, 253)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: { anchor: 'end', align: 'top', font: { weight: 'bold' } }
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    max: 1000 
                } 
            }
        }
    });

    // 2. Status Doughnut Chart
    const ctxStatus = document.getElementById('statusDoughnutChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: [
                <?php foreach($statusData as $row): ?>
                    "<?php echo htmlspecialchars($row['CallerName'], ENT_QUOTES, 'UTF-8'); ?>",
                <?php endforeach; ?>
            ],
            datasets: [{
                data: [
                    <?php foreach($statusData as $row): ?>
                        <?php echo (int)$row['vehicle_count']; ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.8)',   // በስራ ላይ ያለ (አረንጓዴ)
                    'rgba(255, 193, 7, 0.8)',    // በብልሽት የቆመ (ቢጫ)
                    'rgba(220, 53, 69, 0.8)',    // የጠፋ (ቀይ)
                    'rgba(108, 117, 125, 0.8)',  // የተወገደ (ግራጫ)
                    'rgba(52, 58, 64, 0.8)'      // የወደመ (ጥቁር ግራጫ)
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                datalabels: { display: false }
            }
        }
    });

    // 3. Vehicle Age Bar Chart
    const ctxAge = document.getElementById('vehicleAgeChart').getContext('2d');
    new Chart(ctxAge, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: {
            labels: ['0-5 ዓመት', '6-15 ዓመት', '16-25 ዓመት', '26-35 ዓመት', '36+ ዓመት'],
            datasets: [{
                label: 'የተሽከርካሪ ብዛት',
                data: [
                    <?php echo isset($reportData[0]['vehicle_count']) ? $reportData[0]['vehicle_count'] : 0; ?>, 
                    <?php echo isset($reportData[1]['vehicle_count']) ? $reportData[1]['vehicle_count'] : 0; ?>, 
                    <?php echo isset($reportData[2]['vehicle_count']) ? $reportData[2]['vehicle_count'] : 0; ?>, 
                    <?php echo isset($reportData[3]['vehicle_count']) ? $reportData[3]['vehicle_count'] : 0; ?>, 
                    <?php echo isset($reportData[4]['vehicle_count']) ? $reportData[4]['vehicle_count'] : 0; ?>
                ],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(13, 202, 240, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: { anchor: 'end', align: 'top', font: { weight: 'bold' } }
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    max: 1000 
                } 
            }
        }
    });

   // 4. መኪናዎቹ በተመረቱበት ዓ.ም. 
    const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($trendData, 'manufactured_year'), JSON_UNESCAPED_UNICODE); ?>,
            datasets: [{
                label: 'የተሽከርካሪ ብዛት',
                data: <?php echo json_encode(array_column($trendData, 'activity_count')); ?>,
                fill: true,
                backgroundColor: 'rgba(13, 110, 253, 0.05)',
                borderColor: 'rgb(13, 110, 253)',
                tension: 0.3,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: { display: false }
            },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>