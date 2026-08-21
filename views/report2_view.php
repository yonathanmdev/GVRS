<?php 
$rows = $reportData['report_data'] ?? [];
$colCount = 8; // ተ.ቁ፣ ስም፣ 5 የአገልግሎት ዘመን ስቴተሶች፣ እና አጠቃላይ ድምር
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
        /* =========================================================
            A4 LANDSCAPE PRINT & PAGE STYLING (ከ ሠ1 የተወሰደ ማራኪ ዲዛይን)
            ========================================================= */
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

        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            padding: 0 20px;
        }
        .sig-box {
            width: 30%;
            border-top: 1px dashed #555;
            padding-top: 5px;
            text-align: center;
        }

        .no-print-bar {
            max-width: 287mm;
            margin: 10px auto;
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

<div class="no-print-bar no-print"></div>

<div class="a4-page">
    <div class="page-inner-border">
        
        <div class="accent-strip"></div>

        <div class="report-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="report-title"><?php echo htmlspecialchars($title ?? 'የተሽከርካሪዎች አገልግሎት ዘመን ሪፖርት'); ?></h4>
                <div class="report-date mt-1">
                    ቀን፡ <?php echo date('Y-m-d'); ?> ዓ.ም | በአገልግሎት ዘመን የተመደበ
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table printable-table text-center">
                <thead>
                    <!-- የላይኛው ሄደር (ጥራት ምደባ) -->
                    <tr>
                        <th rowspan="2" class="align-middle">ተ.ቁ</th>
                        <th rowspan="2" class="align-middle text-start">የተቋሙ / ዞን ስም</th>
                        <th class="text-success">በጣም ጥሩ</th>
                        <th class="text-primary">ጥሩ</th>
                        <th class="text-warning text-dark">መካከለኛ</th>
                        <th class="text-danger">ዝቅተኛ</th>
                        <th class="text-dark bg-secondary bg-opacity-25">በጣም ዝቅተኛ</th>
                        <th rowspan="2" class="align-middle">አጠቃላይ<br>ድምር</th>
                    </tr>
                    <!-- የታችኛው ሄደር (የዓመት ክልል) -->
                    <tr>
                        <th>እስከ 5 ዓመት የሠራ</th>
                        <th>ከ 6- 15 ዓመት የሠራ</th>
                        <th>ከ 16- 25 ዓመት የሠራ</th>
                        <th>ከ 26- 35 ዓመት የሠራ</th>
                        <th>ከ 36 ዓመት በላይ የሠራ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($rows)) {
                        $no = 1;
                        
                        // የድምር ማከማቻዎች (Totals)
                        $t_vgood = 0; // በጣም ጥሩ (0-5)
                        $t_good = 0;  // ጥሩ (6-15)
                        $t_med = 0;   // መካከለኛ (16-25)
                        $t_low = 0;   // ዝቅተኛ (26-35)
                        $t_vlow = 0;  // በጣም ዝቅተኛ (36+)
                        $t_all_total = 0; // የሁሉም ድምር

                        foreach ($rows as $row) {
                            // ከዳታቤዝ የሚመጡት አምዶች ስም እንደዚህ ይሆናል ተብሎ ይታሰባል (በSQL ላይ እናስተካክለዋለን)
                            $vGood = $row['years_0_to_5'] ?? 0;
                            $good  = $row['years_6_to_15'] ?? 0;
                            $med   = $row['years_16_to_25'] ?? 0;
                            $low   = $row['years_26_to_35'] ?? 0;
                            $vLow  = $row['years_36_plus'] ?? 0;
                            
                            $rowTotal = $vGood + $good + $med + $low + $vLow;

                            // አጠቃላይ ድምሮችን መደመር
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
                                
                                <td class="text-success fw-bold"><?php echo $vGood > 0 ? $vGood : '-'; ?></td>
                                <td class="text-primary"><?php echo $good > 0 ? $good : '-'; ?></td>
                                <td class="text-warning text-dark"><?php echo $med > 0 ? $med : '-'; ?></td>
                                <td class="text-danger"><?php echo $low > 0 ? $low : '-'; ?></td>
                                <td class="text-dark"><?php echo $vLow > 0 ? $vLow : '-'; ?></td>
                                
                                <td class="fw-bold bg-light"><?php echo $rowTotal > 0 ? $rowTotal : '-'; ?></td>
                            </tr>
                    <?php 
                        }
                    ?>
                        <!-- ጠቅላላ ድምር (Grand Total) -->
                        <tr class="grand-total fw-bold">
                            <td colspan="2" class="text-end">ጠ/ድምር:</td>
                            <td class="text-success"><?php echo $t_vgood; ?></td>
                            <td class="text-primary"><?php echo $t_good; ?></td>
                            <td class="text-warning text-dark"><?php echo $t_med; ?></td>
                            <td class="text-danger"><?php echo $t_low; ?></td>
                            <td class="text-dark"><?php echo $t_vlow; ?></td>
                            <td><?php echo $t_all_total; ?></td>
                        </tr>
                    <?php
                    } else {
                        echo "<tr><td colspan='{$colCount}' class='text-center text-muted py-4'>ምንም መረጃ አልተገኘም</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="signature-section">
            <div class="sig-box">
                ያዘጋጀው ስም እና ፊርማ
            </div>
            <div class="sig-box">
                ያረጋገጠው ስም እና ፊርማ
            </div>
            <div class="sig-box">
                ያጸደቀው ኃላፊ ስም እና ፊርማ
            </div>
        </div>

    </div>
</div>

</body>
</html>