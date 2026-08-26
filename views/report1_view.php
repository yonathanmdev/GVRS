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
$colCount = count($activeCarTypes) + 8; // ተ.ቁ፣ ስም፣ የካር አይነቶች፣ አጠቃላይ እና 5 ስቴተሶች
?>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Bootstrap CSS (ካለዎት) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        /* =========================================================
            A4 LANDSCAPE PRINT & PAGE STYLING
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

        /* 1. የገጽ ማስቀመጫ ኮንቴነር (A4 Landscape Page Frame) */
        .a4-page {
            background: #ffffff;
            width: 100%;
            max-width: 287mm; /* Standard A4 Landscape printable width */
            margin: 20px auto;
            padding: 12mm 15mm;
            border-radius: 4px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: relative;
            border-top: 8px solid #198754; 
        }

        /* 2. የውስጥ የጌጣጌጥ ቦርደር መስመር (Inner Frame Strip) */
        .page-inner-border {
            border: 1px solid #dcdcdc;
            border-top: 3px solid #198754;
            padding: 15px;
            border-radius: 2px;
            position: relative;
        }

        /* 3. ያማረ የቀለም ስትሪፕ (Color Gradient Accent Strip) */
        .accent-strip {
            height: 4px;
            background: linear-gradient(90deg, #198754 0%, #ffc107 50%, #198754 100%);
            margin-bottom: 20px;
            border-radius: 2px;
        }

        /* 4. የሪፖርት ራስጌ (Report Header) */
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

        /* 5. የሠንጠረዥ ስታይል (Table Styling for Crisp Print) */
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

        /* 6. የፊርማ ቦታ (Signature Block) */
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

        /* 7. ማተሚያ ቁልፍ (Print Controls) */
        .no-print-bar {
            max-width: 287mm;
            margin: 10px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* =========================================================
            PRINT MEDIA QUERIES (ለህትመት ብቻ የሚሰሩ ደንቦች)
            ========================================================= */
        @media print {
            body {
                background: #fff !important;
                padding: 0;
                margin: 0;
            }
            .no-print, .no-print-bar, div[style*="monospace"] {
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

<!-- ለማተም እና ለመመለስ የሚያገለግል የቁጥጥር ባር -->
<div class="no-print-bar no-print"></div>

<!-- A4 Landscape Page Frame -->
<div class="a4-page">
    <div class="page-inner-border">
        
        <!-- የውበት ቦርደር ስትሪፕ line -->
        <div class="accent-strip"></div>

        <!-- የሪፖርት ራስጌ -->
        <div class="report-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="report-title"><?php echo htmlspecialchars($title); ?></h4>
                <div class="report-date mt-1">
                    ቀን፡ <?php echo date('Y-m-d'); ?> ዓ.ም | የተሽከርካሪዎች መረጃ ማጠቃለያ
                </div>
            </div>
        </div>

        <!-- የሪፖርት ሠንጠረዥ -->
        <div class="table-responsive">
            <table class="table printable-table text-center">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle">ተ.ቁ</th>
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
                        $totals = [];
                        foreach ($activeCarTypes as $type) {
                            $totals['type_' . $type['id']] = 0;
                        }
                        
                        // የስታተስ ድምር ማከማቻ ተለዋዋጮች 
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
                        <!-- ጠቅላላ ድምር (Grand Total) -->
                        <tr class="grand-total fw-bold">
                            <td colspan="2" class="text-end">ጠ/ድምር:</td>
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

        <!-- የህጋዊነት እና ፊርማ ክፍል -->
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