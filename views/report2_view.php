<?php 
$rows = $reportData['report_data'] ?? [];

// 1. መረጃውን በ "በጣም ዝቅተኛ (years_36_plus)" ብዛት መሰረት ከትልቁ ወደ ትንሹ (Descending) እንመድበው
usort($rows, function($a, $b) {
    $valA = $a['years_36_plus'] ?? 0;
    $valB = $b['years_36_plus'] ?? 0;
    if ($valB === $valA) {
        $totalA = ($a['years_0_to_5'] ?? 0) + ($a['years_6_to_15'] ?? 0) + ($a['years_16_to_25'] ?? 0) + ($a['years_26_to_35'] ?? 0) + ($a['years_36_plus'] ?? 0);
        $totalB = ($b['years_0_to_5'] ?? 0) + ($b['years_6_to_15'] ?? 0) + ($b['years_16_to_25'] ?? 0) + ($b['years_26_to_35'] ?? 0) + ($b['years_36_plus'] ?? 0);
        return $totalB - $totalA;
    }
    return $valB - $valA;
});

$colCount = 13; 
?>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'የተሽከርካሪዎች አገልግሎት ዘመን ሪፖርት (ሠ2)'); ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        @page {
            size: A4 landscape;
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
            max-width: 287mm; 
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
            font-size: 10px; 
            margin-bottom: 20px;
        }
        .printable-table th, 
        .printable-table td {
            border: 1px solid #333 !important;
            padding: 5px 3px !important;
            vertical-align: middle;
        }
        .printable-table thead th {
            background-color: #e8f5e9 !important;
            color: #000 !important;
            font-weight: bold;
            font-size: 10px;
        }
        .printable-table .grand-total {
            background-color: #f1f1f1 !important;
            font-weight: bold;
            font-size: 11px;
        }

        .no-print-bar {
            max-width: 287mm;
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

<div class="a4-page">
    <div class="page-inner-border">
        
        <div class="accent-strip"></div>

        <div class="report-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="report-title"><?php echo htmlspecialchars($title ?? 'የተሽከርካሪዎች አገልግሎት ዘመን ሪፖርት'); ?></h4>
                <div class="report-date mt-1">
                    ቀን፡ <?php echo date('Y-m-d'); ?> ዓ.ም | በጣም ዝቅተኛ በሆነው (36+ ዓመት) ተደርጎ የተዘጋጀ
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="reportTable" class="table printable-table text-center">
                <thead>
                    <!-- የላይኛው ሄደር (የጥራት ምደባ ከዓመት ክልል ጋር እና የየራሳቸው ደረጃዎች) -->
                    <tr>
                        <th rowspan="2" class="align-middle">ተ.ቁ</th>
                        <th rowspan="2" class="align-middle text-start">የተቋሙ / ዞን ስም</th>
                        
                        <th colspan="2" class="text-success">በጣም ጥሩ<br><span style="font-weight:normal; font-size:9px;">(እስከ 5 ዓመት የሠራ)</span></th>
                        <th colspan="2" class="text-primary">ጥሩ<br><span style="font-weight:normal; font-size:9px;">(ከ 6- 15 ዓመት የሠራ)</span></th>
                        <th colspan="2" class="text-warning text-dark">መካከለኛ<br><span style="font-weight:normal; font-size:9px;">(ከ 16- 25 ዓመት የሠራ)</span></th>
                        <th colspan="2" class="text-danger">ዝቅተኛ<br><span style="font-weight:normal; font-size:9px;">(ከ 26- 35 ዓመት የሠራ)</span></th>
                        <th colspan="2" class="text-dark bg-secondary bg-opacity-25">በጣም ዝቅተኛ<br><span style="font-weight:normal; font-size:9px;">(ከ 36 ዓመት በላይ የሠራ)</span></th>
                        
                        <th rowspan="2" class="align-middle">አጠቃላይ<br>ድምር</th>
                    </tr>
                    <!-- የታችኛው ሄደር (ብዛት እና ደረጃ ንዑስ ርዕሶች) -->
                    <tr>
                        <th class="text-success">ብዛት</th>
                        <th class="text-success">ደረጃ</th>
                        
                        <th class="text-primary">ብዛት</th>
                        <th class="text-primary">ደረጃ</th>
                        
                        <th class="text-warning text-dark">ብዛት</th>
                        <th class="text-warning text-dark">ደረጃ</th>
                        
                        <th class="text-danger">ብዛት</th>
                        <th class="text-danger">ደረጃ</th>
                        
                        <th class="text-dark">ብዛት</th>
                        <th class="text-dark">ደረጃ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($rows)) {
                        $sortedByVGood = $rows;
                        usort($sortedByVGood, fn($a, $b) => ($b['years_0_to_5'] ?? 0) - ($a['years_0_to_5'] ?? 0));

                        $sortedByGood = $rows;
                        usort($sortedByGood, fn($a, $b) => ($b['years_6_to_15'] ?? 0) - ($a['years_6_to_15'] ?? 0));

                        $sortedByMed = $rows;
                        usort($sortedByMed, fn($a, $b) => ($b['years_16_to_25'] ?? 0) - ($a['years_16_to_25'] ?? 0));

                        $sortedByLow = $rows;
                        usort($sortedByLow, fn($a, $b) => ($b['years_26_to_35'] ?? 0) - ($a['years_26_to_35'] ?? 0));

                        $sortedByVLow = $rows;
                        usort($sortedByVLow, fn($a, $b) => ($b['years_36_plus'] ?? 0) - ($a['years_36_plus'] ?? 0));

                        function getRank($item, $sortedList) {
                            $rank = 1;
                            foreach ($sortedList as $row) {
                                if (($row['branch_name'] ?? '') === ($item['branch_name'] ?? '')) {
                                    return $rank;
                                }
                                $rank++;
                            }
                            return $rank;
                        }

                        $no = 1;
                        
                        $t_vgood = 0; 
                        $t_good = 0; 
                        $t_med = 0; 
                        $t_low = 0; 
                        $t_vlow = 0; 
                        $t_all_total = 0;

                        foreach ($rows as $row) {
                            $vGood = $row['years_0_to_5'] ?? 0;
                            $good  = $row['years_6_to_15'] ?? 0;
                            $med   = $row['years_16_to_25'] ?? 0;
                            $low   = $row['years_26_to_35'] ?? 0;
                            $vLow  = $row['years_36_plus'] ?? 0;
                            
                            $rowTotal = $vGood + $good + $med + $low + $vLow;

                            $t_vgood += $vGood;
                            $t_good  += $good;
                            $t_med   += $med;
                            $t_low   += $low;
                            $t_vlow  += $vLow;
                            $t_all_total += $rowTotal;
                    ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td class="text-start fw-bold"><?php echo htmlspecialchars($row['branch_name'] ?? ''); ?></td>
                                
                                <!-- በጣም ጥሩ -->
                                <td class="text-success fw-bold"><?php echo $vGood > 0 ? $vGood : '-'; ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo getRank($row, $sortedByVGood); ?></span></td>

                                <!-- ጥሩ -->
                                <td class="text-primary"><?php echo $good > 0 ? $good : '-'; ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo getRank($row, $sortedByGood); ?></span></td>

                                <!-- መካከለኛ -->
                                <td class="text-warning text-dark"><?php echo $med > 0 ? $med : '-'; ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo getRank($row, $sortedByMed); ?></span></td>

                                <!-- ዝቅተኛ -->
                                <td class="text-danger"><?php echo $low > 0 ? $low : '-'; ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo getRank($row, $sortedByLow); ?></span></td>

                                <!-- በጣም ዝቅተኛ -->
                                <td class="text-dark fw-bold"><?php echo $vLow > 0 ? $vLow : '-'; ?></td>
                                <td><span class="badge bg-secondary"><?php echo getRank($row, $sortedByVLow); ?></span></td>
                                
                                <!-- አጠቃላይ ድምር -->
                                <td class="fw-bold bg-light"><?php echo $rowTotal > 0 ? $rowTotal : '-'; ?></td>
                            </tr>
                    <?php 
                        }
                    ?>
                        <!-- ጠቅላላ ድምር (Grand Total) -->
                        <tr class="grand-total fw-bold">
                            <td colspan="2" class="text-end">ጠ/ድምር:</td>
                            <td colspan="2" class="text-success text-center"><?php echo $t_vgood; ?></td>
                            <td colspan="2" class="text-primary text-center"><?php echo $t_good; ?></td>
                            <td colspan="2" class="text-warning text-dark text-center"><?php echo $t_med; ?></td>
                            <td colspan="2" class="text-danger text-center"><?php echo $t_low; ?></td>
                            <td colspan="2" class="text-dark text-center"><?php echo $t_vlow; ?></td>
                            <td class="text-center"><?php echo $t_all_total; ?></td>
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

<!-- ከ nonce ጋር የተዋቀረ የ JavaScript እና ኤክስፖርት ኮድ -->
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
            a.download = 'Vehicle_Service_Report_<?php echo date('Y-m-d'); ?>.xls';
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