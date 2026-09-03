<?php 
$carTypes = $reportData['car_types'] ?? [];
$rows = $reportData['report_data'] ?? [];

// 1. መረጃ ያላቸውን የካር አይነቶች ብቻ ለይተን እናውጣ (Active columns with data > 0)
$activeCarTypes = [];
foreach ($carTypes as $type) {
    $hasData = false;
    foreach ($rows as $row) {
        if (($row['vehicle_type_' . $type['id']] ?? 0) > 0) {
            $hasData = true;
            break;
        }
    }
    if ($hasData) {
        $activeCarTypes[] = $type;
    }
}

// 2. መረጃውን በአጠቃላይ ብዛት (total_vehicles) መሰረት ከትልቁ ወደ ትንሹ (Descending) እንመድበው
usort($rows, function($a, $b) {
    $totalA = $a['total_vehicles'] ?? 0;
    $totalB = $b['total_vehicles'] ?? 0;
    return $totalB - $totalA;
});

$colCount = count($activeCarTypes) + 9; // ተ.ቁ፣ ደረጃ፣ ስም፣ የካር አይነቶች፣ አጠቃላይ እና 5 ስቴተሶች
?>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        /* =========================================================
            A3 LANDSCAPE PRINT & PAGE STYLING (ለሰፊው ሪፖርት የተመቻቸ)
            ========================================================= */
        @page {
            size: A3 landscape;
            margin: 8mm;
        }

        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .a4-page {
            background: #ffffff;
            width: 100%;
            max-width: 400mm;
            margin: 20px auto;
            padding: 12mm 15mm;
            border-radius: 4px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
            border-top: 8px solid #198754; 
        }

        .page-inner-border {
            border: 1px solid #dcdcdc;
            border-top: 3px solid #198754;
            padding: 15px;
            border-radius: 2px;
            position: relative;
        }

        .accent-strip {
            height: 4px;
            background: linear-gradient(90deg, #198754 0%, #ffc107 50%, #198754 100%);
            margin-bottom: 20px;
            border-radius: 2px;
        }

        .report-header {
            margin-bottom: 15px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .report-title {
            font-size: 18px;
            font-weight: 700;
            color: #1b5e20;
            margin: 0;
        }
        .report-date {
            font-size: 12px;
            color: #666;
        }

        .printable-table {
            width: 100%;
            border-collapse: collapse !important;
            font-size: 11px;
            margin-bottom: 20px;
        }
        .printable-table th, 
        .printable-table td {
            border: 1px solid #333 !important;
            padding: 6px 4px !important;
            vertical-align: middle;
        }
        .printable-table thead th {
            background-color: #e8f5e9 !important;
            color: #000 !important;
            font-weight: bold;
            font-size: 11px;
        }
        .printable-table .grand-total {
            background-color: #f1f1f1 !important;
            font-weight: bold;
            font-size: 12px;
        }

        .no-print-bar {
            max-width: 400mm;
            margin: 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @media print {
            body {
                background: #fff !important;
                padding: 0;
                margin: 0;
            }
            .no-print, .no-print-bar {
                display: none !important;
            }
            .a4-page {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                border-top: 8px solid #198754 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .page-inner-border {
                border: 1px solid #000 !important;
                border-top: 3px solid #198754 !important;
            }
            .printable-table thead th {
                background-color: #e8f5e9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .printable-table td, .printable-table th {
                border: 1px solid #000 !important;
            }
            .accent-strip {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

<!-- ኤክስፖርት እና ማተሚያ ቁልፎች የሚገኙበት ባር -->
<div class="no-print-bar no-print">
    <div>
        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">‹ ተመለስ</a>
    </div>
    <div>
        <button id="exportExcelBtn" class="btn btn-success btn-sm">📥 ወደ Excel አውርድ (Export)</button>
        <button id="printBtn" class="btn btn-primary btn-sm">🖨️ አትም (Print / PDF)</button>
    </div>
</div>

<!-- A3 Landscape Page Frame -->
<div class="a4-page">
    <div class="page-inner-border">
        
        <div class="accent-strip"></div>

        <div class="report-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="report-title"><?php echo htmlspecialchars($title); ?> (የተሽከርካሪዎች ማጠቃለያ)</h4>
                <div class="report-date mt-1">
                    ቀን፡ <?php echo date('Y-m-d'); ?> ዓ.ም | በአጠቃላይ ብዛት የተደራጀ የደረጃ ሰንጠረዥ
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="reportTable" class="table printable-table text-center">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle">ተ.ቁ</th>
                        <th rowspan="2" class="align-middle">ደረጃ (Rank)</th>
                        <th rowspan="2" class="align-middle text-start">የተቋሙ / ዞን ስም</th>
                        <th colspan="<?php echo count($activeCarTypes); ?>">የተሽከርካሪዎች ዓይነት</th>
                        <th rowspan="2" class="align-middle">አጠቃላይ ብዛት</th>
                        <th rowspan="2" class="align-middle">ሥራ ላይ ያለ (Active)</th>
                        <th rowspan="2" class="align-middle">ከአገልግሎት ውጭ (Out of Service)</th>
                        <th rowspan="2" class="align-middle">የጠፋ (Lost)</th>
                        <th rowspan="2" class="align-middle">የወደመ (Destroyed)</th>
                        <th rowspan="2" class="align-middle">የተወገደ (Disposed)</th>
                    </tr>
                    <tr>
                        <?php foreach ($activeCarTypes as $type): ?>
                            <th><?php echo htmlspecialchars($type['cartype']); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($rows)) {
                        $no = 1;
                        $rank = 1;
                        $totals = [];
                        foreach ($activeCarTypes as $type) {
                            $totals['type_' . $type['id']] = 0;
                        }
                        
                        $t_total_vehicles = 0; 
                        $t_active = 0; 
                        $t_out_of_service = 0; 
                        $t_lost = 0; 
                        $t_destroyed = 0; 
                        $t_disposed = 0;

                        foreach ($rows as $row) {
                            $activeVal       = $row['status_active'] ?? 0;
                            $outOfServiceVal = $row['status_out_of_service'] ?? 0;
                            $lostVal         = $row['status_lost'] ?? 0;
                            $destroyedVal    = $row['status_destroyed'] ?? 0;
                            $disposedVal     = $row['status_disposed'] ?? 0;

                            $t_total_vehicles += ($row['total_vehicles'] ?? 0);
                            $t_active += $activeVal;
                            $t_out_of_service += $outOfServiceVal;
                            $t_lost += $lostVal;
                            $t_destroyed += $destroyedVal;
                            $t_disposed += $disposedVal;
                    ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><span class="badge bg-secondary"><?php echo $rank++; ?></span></td>
                                <td class="text-start fw-bold"><?php echo htmlspecialchars($row['branch_name'] ?? ''); ?></td>
                                
                                <?php foreach ($activeCarTypes as $type): 
                                    $val = $row['vehicle_type_' . $type['id']] ?? 0;
                                    $totals['type_' . $type['id']] += $val;
                                ?>
                                    <td><?php echo $val > 0 ? $val : '-'; ?></td>
                                <?php endforeach; ?>

                                <td class="fw-bold bg-light"><?php echo $row['total_vehicles'] ?? 0; ?></td>
                                <td class="text-success fw-bold"><?php echo $activeVal > 0 ? $activeVal : '-'; ?></td>
                                <td class="text-warning"><?php echo $outOfServiceVal > 0 ? $outOfServiceVal : '-'; ?></td>
                                <td class="text-danger"><?php echo $lostVal > 0 ? $lostVal : '-'; ?></td>
                                <td class="text-dark"><?php echo $destroyedVal > 0 ? $destroyedVal : '-'; ?></td>
                                <td class="text-secondary"><?php echo $disposedVal > 0 ? $disposedVal : '-'; ?></td>
                            </tr>
                    <?php 
                        }
                    ?>
                        <tr class="grand-total fw-bold">
                            <td colspan="3" class="text-end">ጠ/ድምር:</td>
                            <?php foreach ($activeCarTypes as $type): ?>
                                <td><?php echo $totals['type_' . $type['id']]; ?></td>
                            <?php endforeach; ?>
                            <td><?php echo $t_total_vehicles; ?></td>
                            <td><?php echo $t_active; ?></td>
                            <td><?php echo $t_out_of_service; ?></td>
                            <td><?php echo $t_lost; ?></td>
                            <td><?php echo $t_destroyed; ?></td>
                            <td><?php echo $t_disposed; ?></td>
                        </tr>
                    <?php
                    } else {
                        echo "<tr><td colspan='{$colCount}' class='text-center text-muted py-4'>ምንም መረጃ አልተገኘም</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ከ nonce ጋር የተዋቀረ የ JavaScript ኮድ -->
<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener("DOMContentLoaded", function() {
    const exportBtn = document.getElementById("exportExcelBtn");
    const printBtn = document.getElementById("printBtn");

    if (exportBtn) {
        exportBtn.addEventListener("click", function() {
            let table = document.getElementById("reportTable");
            if (!table) return;
            
            let html = table.outerHTML;
            let blob = new Blob(['\ufeff' + html], {
                type: 'application/vnd.ms-excel'
            });
            
            let url = URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = 'Vehicle_Report_<?php echo date('Y-m-d'); ?>.xls';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    }

    if (printBtn) {
        printBtn.addEventListener("click", function() {
            window.print();
        });
    }
});
</script>

</body>
</html>