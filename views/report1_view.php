<?php
$is_report1_view = true;
$branch_id = $_SESSION['user']['branch_id'] ?? null;
$role = $_SESSION['user']['role'] ?? ''; 
?>


<style>
    /* የሠንጠረዡ አጠቃላይ ዲዛይን እና ቦርደር */
    .system-report-table {
        border-collapse: collapse !important;
        width: 100%;
        background-color: #ffffff;
        font-size: 14px;
    }
    
    .system-report-table th, 
    .system-report-table td {
        border: 1px solid #b0bec5 !important;
        padding: 8px 10px !important;
        vertical-align: middle !important;
    }

    /* የራስጌ (Header) ከለር - ይበልጥ ፕሮፌሽናል እንዲመስል */
    .system-report-table thead th {
        background-color: #34495e !important;
        color: #ffffff !important;
        text-align: center;
        font-weight: bold;
        border-bottom: 2px solid #2c3e50 !important;
    }

    /* የስታይፕ መስመሮች (Stripe Lines) - ተለዋጭ ረድፎች በትንሽ ከለር እንዲለዩ */
    .system-report-table tbody tr:nth-child(even) {
        background-color: #f8f9fa !important;
    }

    /* ሲሠለፍ (Hover) ሲደረግ የረድፉ ከለር እንዲቀየር */
    .system-report-table tbody tr:hover {
        background-color: #e2e8f0 !important;
    }

    /* የጠቅላላ ድምር (Grand Total) ረድፍ ጎልቶ እንዲታይ */
    .system-report-table tfoot tr,
    .system-report-table tbody tr.grand-total {
        background-color: #eef2f7 !important;
        font-weight: bold;
        border-top: 2px solid #34495e !important;
    }
</style>

<div class="card mt-4 shadow-sm border-0">
<div class="card mt-4 shadow-sm border-0">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?php echo htmlspecialchars($title); ?></h5>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table system-report-table text-center">
            <thead>
                <tr>
                    <th rowspan="2" class="align-middle">ተ.ቁ</th>
                    <th rowspan="2" class="align-middle text-start">የተቋሙ / ዞን ስም</th>
                    <th colspan="9">የተሽከርካሪዎች ዓይነት</th>
                    <th rowspan="2" class="align-middle">የተሽከርካሪ ብዛት</th>
                    <th rowspan="2" class="align-middle">የተበላሸ</th>
                    <th rowspan="2" class="align-middle">በሂደት ላይ</th>
                    <th rowspan="2" class="align-middle">በሥራ ላይ ያለ</th>
                </tr>
                <tr>
                    <th>አውቶ-ሞቢል</th>
                    <th>ፒክአፕ ሐ/ሉክስ</th>
                    <th>ሲንግል ካብ</th>
                    <th>ዶብል ካብ</th>
                    <th>ፒካፕ</th>
                    <th>V-8</th>
                    <th>ሚኒባስ/ሚኒ/ባስ</th>
                    <th>አውቶቡስ</th>
                    <th>የድርጅት/ሌሎች</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($reportData) && is_array($reportData)) {
                    $no = 1;
                    $t_automobile = 0; $t_pickup_hilux = 0; $t_single_cab = 0; 
                    $t_double_cab = 0; $t_v8 = 0; $t_minibus = 0; $t_bus = 0; 
                    $t_total_vehicles = 0; $t_damaged = 0; 
                    $t_in_progress = 0; $t_operational = 0;

                    foreach ($reportData as $row) {
                        $t_automobile   += $row['automobile'];
                        $t_pickup_hilux += $row['pickup_hilux'];
                        $t_single_cab   += $row['single_cab'];
                        $t_double_cab   += $row['double_cab'];
                        $t_v8           += $row['v8'];
                        $t_minibus      += $row['minibus'];
                        $t_bus          += $row['bus'];
                        $t_total_vehicles += $row['total_vehicles'];
                        $t_damaged      += $row['damaged'];
                        $t_in_progress  += $row['in_progress'];
                        $t_operational  += $row['operational'];
                ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="text-start fw-bold"><?php echo htmlspecialchars($row['branch_name']); ?></td>
                            <td><?php echo $row['automobile']; ?></td>
                            <td><?php echo $row['pickup_hilux']; ?></td>
                            <td><?php echo $row['single_cab']; ?></td>
                            <td><?php echo $row['double_cab']; ?></td>
                            <td>-</td>
                            <td><?php echo $row['v8']; ?></td>
                            <td><?php echo $row['minibus']; ?></td>
                            <td><?php echo $row['bus']; ?></td>
                            <td>-</td>
                            <td class="fw-bold bg-light"><?php echo $row['total_vehicles']; ?></td>
                            <td class="text-danger"><?php echo $row['damaged']; ?></td>
                            <td class="text-warning"><?php echo $row['in_progress']; ?></td>
                            <td class="text-success fw-bold"><?php echo $row['operational']; ?></td>
                        </tr>
                <?php 
                    }
                ?>
                    <!-- ጠቅላላ ድምር (Grand Total Row) -->
                    <tr class="grand-total fw-bold">
                        <td colspan="2" class="text-end">ጠ/ድምር:</td>
                        <td><?php echo $t_automobile; ?></td>
                        <td><?php echo $t_pickup_hilux; ?></td>
                        <td><?php echo $t_single_cab; ?></td>
                        <td><?php echo $t_double_cab; ?></td>
                        <td>-</td>
                        <td><?php echo $t_v8; ?></td>
                        <td><?php echo $t_minibus; ?></td>
                        <td><?php echo $t_bus; ?></td>
                        <td>-</td>
                        <td><?php echo $t_total_vehicles; ?></td>
                        <td><?php echo $t_damaged; ?></td>
                        <td><?php echo $t_in_progress; ?></td>
                        <td><?php echo $t_operational; ?></td>
                    </tr>
                <?php
                } else {
                    echo "<tr><td colspan='15' class='text-center text-muted py-4'>ምንም መረጃ አልተገኘም (እባክዎ ማጣሪያዎቹን ይምረጡ)</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>