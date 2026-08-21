<?php
$is_report_view = true;
$branch_id = $_SESSION['user']['branch_id'] ?? null;
$role = $_SESSION['user']['role'] ?? ''; 
$r_type = $_GET['report_type'] ?? ''; // የተመረጠው ዓይነት
$secondSelect = $_GET['second_select'] ?? ''; // ሁለተኛው ሳጥን ቫልዩ
?>


<div class="card-body p-4">
    <form action="" method="GET" id="reportForm">
        
        <!-- 1. መዋቅር/ተቋም ምርጫ በሬዲዮ ቁልፎች -->
        <label class="form-label fw-bold mb-3 text-secondary">የሚፈልጉትን የሪፖርት መዋቅር ይምረጡ፦</label>
        
<div class="row g-2 mb-4 bg-white p-3 border rounded-3 shadow-sm">
    
    <!-- የክልል ተጠሪ ተቋም -->
    <div class="col-md">
        <div class="form-check">
            <input class="form-check-input report-type-radio" type="radio" name="report_type" id="type_regional" value="regional" <?= ($r_type == 'regional') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold small" for="type_regional">
                🏛️ የክልል ተጠሪ
            </label>
        </div>
    </div>

    <!-- ተጠሪ መስሪያ ቤት -->
    <div class="col-md">
        <div class="form-check">
            <input class="form-check-input report-type-radio" type="radio" name="report_type" id="type_agency" value="agency" <?= ($r_type == 'agency') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold small" for="type_agency">
                🏢 ተጠሪ ተቋማት
            </label>
        </div>
    </div>

    <!-- መምሪያ -->
    <div class="col-md">
        <div class="form-check">
            <input class="form-check-input report-type-radio" type="radio" name="report_type" id="type_department" value="department" <?= ($r_type == 'department') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold small" for="type_department">
                🗂️ መምሪያ
            </label>
        </div>
    </div>

    <!-- ዞን (አዲስ የተጨመረው) -->
    <div class="col-md">
        <div class="form-check">
            <input class="form-check-input report-type-radio" type="radio" name="report_type" id="type_zone" value="zone" <?= ($r_type == 'zone') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold small" for="type_zone">
                🌐 ዞን
            </label>
        </div>
    </div>

    <!-- ወረዳ -->
    <div class="col-md">
        <div class="form-check">
            <input class="form-check-input report-type-radio" type="radio" name="report_type" id="type_wereda" value="wereda" <?= ($r_type == 'wereda') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold small" for="type_wereda">
                📍 ወረዳ
            </label>
        </div>
    </div>

    <!-- በመጠሪያ ስማቸው -->
    <div class="col-md">
        <div class="form-check">
            <input class="form-check-input report-type-radio" type="radio" name="report_type" id="type_calls" value="calls" <?= ($r_type == 'calls') ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold small" for="type_calls">
                🏷️ በወል መጠሪያቸው
            </label>
        </div>
    </div>
    
</div>



       <!-- 2. ዝርዝር መራጮች (Dropdowns) -->
    <div class="row g-3 p-3 bg-white border rounded-3 mb-4 shadow-sm">
        
<!-- ሁለተኛው ሳጥን -->
        <div class="col-md-6" id="box_second" style="<?= (!empty($r_type)) ? 'display:block;' : 'display:none;' ?>">
            <label for="second_select" class="form-label fw-bold">
                <?php 
                    if ($r_type == 'regional') echo 'የክልል ቢሮዎችን ይምረጡ';
                    elseif ($r_type == 'agency') echo 'ቢሮውን ይምረጡ';
                    elseif ($r_type == 'department') echo 'መምሪያ ይምረጡ';
                    elseif ($r_type == 'zone') echo 'ዞን ይምረጡ';
                    elseif ($r_type == 'wereda') echo 'ዞን ይምረጡ';
                    elseif ($r_type == 'calls') echo 'የወል መጠሪያ ይምረጡ';
                    else echo 'ይምረጡ';
                ?>
            </label>
            
            <select  class="form-control" id="second_select" name="second_select" onchange="this.form.submit()">
                <option value="">-- ምረጡ --</option>
                <option value="all" <?= ($secondSelect == 'all') ? 'selected' : '' ?>>-- ሁሉም (All) --</option>
                <?php 
                if (isset($secondList) && is_array($secondList)) {
                    foreach ($secondList as $item) {
                        // ለ calls ሲሆን ቫልዩ የ branch_type ጽሁፍ ነው (ለምሳሌ regional, agency...)
                        if ($r_type == 'calls') {
                            $val = $item['branch_type'] ?? $item;
                            $selected = ($secondSelect == $val) ? 'selected' : '';
                            echo "<option value='{$val}' {$selected}>" . ucfirst(str_replace('_', ' ', $val)) . "</option>";
                        } else {
                            // ለሌሎቹ መደበኛ የ id እና name ዝርዝር ነው
                            $selected = ($secondSelect == $item['id']) ? 'selected' : '';
                            echo "<option value='{$item['id']}' {$selected}>{$item['name']}</option>";
                        }
                    }
                }
                ?>
            </select>
        </div>
<!-- 3ኛ ሳጥን -->
<div class="col-md-6" id="box_third" style="<?= ($r_type == 'agency' || $r_type == 'department' || $r_type == 'wereda') ? '' : 'display:none;' ?>">
        <label for="third_select" class="form-label fw-bold">
            <?php 
                if ($r_type == 'department') {
                    echo 'የሚገኝበትን ዞን ይምረጡ';
                } elseif ($r_type == 'wereda') {
                    echo 'ወረዳ ይምረጡ'; // ወረዳ ሲመረጥ በሶስተኛው የሚወጣው
                } else {
                    echo 'ተጠሪ ተቋማትን ይምረጡ';
                }
            ?>
        </label>
        <select  class="form-control" id="third_select" name="third_select" onchange="this.form.submit()">
            <option value="">-- ምረጡ --</option>
            <option value="all" <?= (isset($_GET['third_select']) && $_GET['third_select'] == 'all') ? 'selected' : '' ?>>-- ሁሉም (All) --</option>
            <?php 
            if (isset($thirdList) && is_array($thirdList)) {
                foreach ($thirdList as $item) {
                    $selected = (isset($_GET['third_select']) && $_GET['third_select'] == $item['id']) ? 'selected' : '';
                    echo "<option value='{$item['id']}' {$selected}>{$item['name']}</option>";
                }
            }
            ?>
        </select>
    </div>
    

            <div class="col-md-4">
                <label for="fifth_select" class="form-label fw-bold ">የሚፈልጉትን የሪፖርት ዓይነት ይምረጡ</label>
                <select class="form-control" id="fifth_select" name="fifth_select" required>
                    <option value="">-- ሪፖርት ምረጡ --</option>
                    <option value="ሠ1" <?= (isset($_GET['fifth_select']) && $_GET['fifth_select'] == 'ሠ1') ? 'selected' : '' ?>>የተሽከርካሪዎች ጥቅል ሪፖርት (ሠ1)</option>
                    <option value="ሠ2" <?= (isset($_GET['fifth_select']) && $_GET['fifth_select'] == 'ሠ2') ? 'selected' : '' ?>>በአገልግሎት ዘመን ሪፖርት (ሠ2)</option>
                </select>
            </div>

            <div class="col-md-2">
            <div class="d-flex align-items-end h-100">
                <button type="submit" name="show_report"   class="btn btn-primary px-4">
                    አሳይ
                </button>
            </div>
            </div>
        </div>

    </form>
</div>

<script nonce="<?php echo $GLOBALS['nonce'] ?? ''; ?>">
document.addEventListener('DOMContentLoaded', function() {
    const reportForm = document.getElementById('reportForm');
    
    // የሲስተምዎ የባዝ ዩአርኤል (ከ PHP ተቀብሎ ማስገባት ይቻላል)
    const baseUrl = "<?php echo $baseUrl ?? ''; ?>"; // ወይም ቀጥታ የተወሰነ ሊንክ ከሆነ እዚህ ማስገባት ይቻላል

    // የሬዲዮ በተኖች ሲቀየሩ ፎርሙ በራሱ እንዲሰር (Submit) እንዲደረግ
    const radioButtons = document.querySelectorAll('.report-type-radio');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            reportForm.action = ""; 
            reportForm.removeAttribute('target');
            reportForm.submit();
        });
    });

    // የሁለተኛው ሳጥን (second_select) ሲቀየርም ፎርሙ እንዲሰር
    const secondSelect = document.getElementById('second_select');
    if (secondSelect) {
        secondSelect.addEventListener('change', function() {
            reportForm.action = "";
            reportForm.removeAttribute('target');
            reportForm.submit();
        });
    }

    // «ሪፖርቱን አሳይ» ቁልፍ ሲጫን እንደ ሪፖርት አይነቱ መድረሻውን እና ታቡን መቀየር
    reportForm.addEventListener('submit', function(e) {
        const fifthSelect = document.getElementById('fifth_select').value;
        
        if (fifthSelect === 'ሠ1') {
            reportForm.action = `${baseUrl}/GVRS/report1_view`;
            reportForm.target = "_blank"; // በአዲስ ታብ እንዲከፈት
        } else if (fifthSelect === 'ሠ2') {
            reportForm.action = `${baseUrl}/GVRS/report2_view`;
            reportForm.target = "_blank"; // በአዲስ ታብ እንዲከፈት
        }
    });
});
</script>