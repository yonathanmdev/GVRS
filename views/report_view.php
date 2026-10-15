<?php
$is_report_view = true;
$branch_id = $_SESSION['user']['branch_id'] ?? null;
$role = $_SESSION['user']['role'] ?? ''; 
$r_type = $_GET['report_type'] ?? ''; // የተመረጠው ዓይነት
$secondSelect = $_GET['second_select'] ?? ''; // ሁለተኛው ሳጥን ቫልዩ
$thirdSelect = $_GET['third_select'] ?? ''; // ሦስተኛው ሳጥን ቫልዩ
$fifthSelect = $_GET['fifth_select'] ?? ''; // የሪፖርት ዓይነት ቫልዩ
$zoneSubFilter = $_GET['zone_sub_filter'] ?? ''; // የዞን ንዑስ ማጣሪያ አማራጭ

// Escape helper for everything printed into HTML attributes / text
$e = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };

// መዋቅር/ተቋም ምርጫ (one place to add/remove a type)
$reportTypes = [
    'all'        => 'ማጠቃለያ ሪፖርት',
    'regional'   => '🏛️ የክልል ቢሮ',
    'agency'     => '🏢 ተጠሪ ተቋማት',
    'department' => '🗂️ መምሪያ',
    'zone'       => '🌐 ዞን',
    'wereda'     => '📍 ወረዳ',
    'calls'      => '🏷️ በልዩ መጠሪያቸው',
];

// Font Awesome icon per type (display only – replaces the emoji in the card)
$reportIcons = [
    'all'        => 'fa-chart-pie',
    'regional'   => 'fa-landmark',
    'agency'     => 'fa-building',
    'department' => 'fa-sitemap',
    'zone'       => 'fa-globe',
    'wereda'     => 'fa-map-marker-alt',
    'calls'      => 'fa-tags',
];

// የሁለተኛው ሳጥን ርዕስ
$secondLabels = [
    'regional'   => 'የክልል ቢሮ ይምረጡ',
    'agency'     => 'ቢሮውን ይምረጡ',
    'department' => 'መምሪያ ይምረጡ',
    'zone'       => 'ዞን ይምረጡ',
    'wereda'     => 'ዞን ይምረጡ',
    'calls'      => 'የልዩ መጠሪያ ይምረጡ',
];

// የሦስተኛው ሳጥን ርዕስ
$thirdLabels = [
    'department' => 'የሚገኝበትን ዞን ይምረጡ',
    'wereda'     => 'ወረዳ ይምረጡ',
];

// የሪፖርት ዓይነቶች → route (single source of truth, also used by the JS below)
$reportViews = [
    'ሠ1' => ['label' => 'የተሽከርካሪዎች ጥቅል ሪፖርት (ሠ1)', 'route' => 'report1_view'],
    'ሠ2' => ['label' => 'በአገልግሎት ዘመን ሪፖርት (ሠ2)',    'route' => 'report2_view'],
];
$reportRoutes = array_map(function ($r) { return $r['route']; }, $reportViews);
?>

<style nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
/* ==========================================================
   REPORT FILTER – 2-Column Professional Dashboard Layout
   ========================================================== */
.report-filter {
    --rf-primary: #4361ee;
    --rf-primary-hover: #3a0ca3;
    --rf-text: #1e293b;
    --rf-muted: #64748b;
    --rf-border: #cbd5e1;
    --rf-border-light: #e2e8f0;
    --rf-card: #ffffff;
    --rf-radius: 12px;
    --rf-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.08);

    font-family: "Noto Sans Ethiopic", "Nyala", "Abyssinica SIL", system-ui, -apple-system,
                 "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: var(--rf-text);
    max-width: 1200px;
    margin-inline: auto;
}

.report-filter .rf-card {
    border: 1px solid var(--rf-border-light);
    border-radius: var(--rf-radius);
    background: var(--rf-card);
    box-shadow: var(--rf-shadow);
    overflow: hidden;
}
.report-filter .rf-card-body { 
    padding: 1.75rem; 
}

/* ---- Section Title styling ---- */
.report-filter .ownership-title {
    display: flex;
    align-items: center;
    gap: .65rem;
    margin-bottom: 1rem;
}
.report-filter .ownership-title::before {
    content: '';
    width: 4px;
    height: 22px;
    border-radius: 4px;
    background: var(--rf-primary);
}
.report-filter .ownership-title label {
    margin: 0;
    color: var(--rf-text);
    font-size: 1rem;
    font-weight: 700;
}

/* ---- Top Radio options as icon cards ---- */
.report-filter .report-types {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    margin: 0 0 1.75rem 0;
    padding: 0;
}
.report-filter .report-types > .col-md {
    flex: 1 1 130px;
    width: auto;
    max-width: none;
    padding: 0;
    margin: 0;
}
.report-filter .report-types .form-check {
    position: relative;
    height: 100%;
    padding: 0;
    margin: 0;
    min-height: 0;
}
.report-filter .report-types .form-check-input {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    margin: 0;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.report-filter .report-types .form-check-label {
    width: 100%;
    height: 100%;
    min-height: 85px;
    margin: 0;
    padding: .85rem .75rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    border: 1px solid var(--rf-border-light);
    border-radius: 10px;
    background: #f8fafc;
    color: var(--rf-muted);
    font-size: .85rem;
    font-weight: 600;
    line-height: 1.4;
    cursor: pointer;
    user-select: none;
    transition: all .2s ease;
}
.report-filter .report-types .form-check-label i {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: .4rem;
    border-radius: 8px;
    background: #ffffff;
    color: var(--rf-primary);
    font-size: 1rem;
    box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04);
    transition: all .2s ease;
}
.report-filter .report-types .form-check:hover .form-check-label {
    transform: translateY(-2px);
    border-color: var(--rf-primary);
    color: var(--rf-primary);
    background: #ffffff;
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.08);
}
.report-filter .report-types .form-check-input:checked + .form-check-label {
    color: #ffffff;
    background: var(--rf-primary);
    border-color: var(--rf-primary);
    box-shadow: 0 6px 16px rgba(67, 97, 238, 0.25);
}
.report-filter .report-types .form-check-input:checked + .form-check-label i {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}
.report-filter .report-types .form-check-input:focus-visible + .form-check-label {
    outline: 2px solid var(--rf-primary);
    outline-offset: 2px;
}

/* ---- 2-Column Section Layout ---- */
.report-filter .two-column-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    align-items: stretch;
}

.report-filter .column-card {
    background: #f8fafc;
    border: 1px solid var(--rf-border-light);
    border-radius: 10px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 1.25rem;
}

.report-filter .column-card-title {
    font-size: .9rem;
    font-weight: 700;
    color: var(--rf-primary);
    display: flex;
    align-items: center;
    gap: .5rem;
    border-bottom: 1px solid var(--rf-border-light);
    padding-bottom: .75rem;
    margin-bottom: .25rem;
}

.report-filter .form-group-item {
    display: flex;
    flex-direction: column;
    gap: .4rem;
}

.report-filter .form-group-item .form-label {
    margin: 0;
    color: var(--rf-text);
    font-size: .85rem;
    font-weight: 600;
}

/* ---- Select inputs styling ---- */
.report-filter .form-control {
    width: 100%;
    min-height: 46px;
    height: 46px;
    padding: 0 2.5rem 0 1rem;
    font-size: .9rem;
    color: var(--rf-text);
    background-color: #ffffff;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    border: 1px solid var(--rf-border);
    border-radius: 8px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    cursor: pointer;
    transition: all .2s ease;
}
.report-filter .form-control:hover { 
    border-color: var(--rf-primary); 
}
.report-filter .form-control:focus {
    border-color: var(--rf-primary);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    outline: 0;
}

/* ---- Submit button ---- */
.report-filter #showReportBtn {
    width: 100%;
    min-height: 46px;
    padding: 0 1.75rem !important;
    border: 0;
    border-radius: 8px;
    color: #fff;
    font-size: .95rem;
    font-weight: 600;
    background: var(--rf-primary);
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
    cursor: pointer;
    transition: all .2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
}
.report-filter #showReportBtn:hover {
    background: var(--rf-primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(67, 97, 238, 0.3);
}

/* ---- Responsive adjustments ---- */
@media (max-width: 850px) {
    .report-filter .two-column-grid {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 767.98px) {
    .report-filter .rf-card-body { padding: 1.25rem; }
    .report-filter .report-types { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; }
    .report-filter .report-types .form-check-label { min-height: 82px; }
}
@media (prefers-reduced-motion: reduce) {
    .report-filter * { transition: none !important; }
}
</style>

<div class="card-body p-4 report-filter">
  <div class="card shadow-sm rf-card">
    <div class="rf-card-body">
    <form action="" method="GET" id="reportForm">
        
        <!-- 1. መዋቅር/ተቋም ምርጫ በሬዲዮ ቁልፎች -->
        <div class="ownership-title">
            <label class="mb-0 font-weight-bold">የሚፈልጉትን የሪፖርት መዋቅር ይምረጡ፦ <span class="text-danger">*</span></label>
        </div>
        
        <div class="row report-types">
            <?php foreach ($reportTypes as $typeKey => $typeLabel):
                $isChecked = ($typeKey === 'all')
                    ? (empty($r_type) || $r_type == 'all')
                    : ($r_type == $typeKey);
                $plainLabel = trim(preg_replace('/^[^\p{L}\p{N}]+/u', '', $typeLabel));
                $iconClass  = $reportIcons[$typeKey] ?? 'fa-folder';
            ?>
            <div class="col-md">
                <div class="form-check">
                    <input class="form-check-input report-type-radio" type="radio" name="report_type" id="type_<?= $e($typeKey) ?>" value="<?= $e($typeKey) ?>" <?= $isChecked ? 'checked' : '' ?> required>
                    <label class="form-check-label" for="type_<?= $e($typeKey) ?>">
                        <i class="fas <?= $e($iconClass) ?>"></i>
                        <span><?= $e($plainLabel) ?></span>
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- 2. Two-Column Grid Layout for Report Selection -->
        <div class="two-column-grid">
            
            <!-- COLUMN 1: Structure & Location Filters -->
            <div class="column-card">
                <div>
                    <div class="column-card-title">
                        <i class="fas fa-filter"></i>
                        <span>የመዋቅር ማጣሪያዎች</span>
                    </div>

                    <!-- Default message when 'all' is selected -->
                    <div id="box_default_msg" style="<?= (empty($r_type) || $r_type === 'all') ? 'display:block;' : 'display:none;' ?>" class="text-muted small py-2">
                        <i class="fas fa-info-circle me-1"></i> ለጠቅላላ ማጠቃለያ ሪፖርት ተጨማሪ ምርጫ አያስፈልግም።
                    </div>
                    
                    <!-- ሁለተኛው ሳጥን -->
                    <?php $showSecond = (!empty($r_type) && $r_type !== 'all'); ?>
                    <div class="form-group-item mb-3" id="box_second" style="<?= $showSecond ? 'display:flex;' : 'display:none;' ?>">
                        <label for="second_select" class="form-label">
                            <?= $e($secondLabels[$r_type] ?? 'ይምረጡ') ?> <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="second_select" name="second_select" <?= $showSecond ? 'required' : '' ?>>
                            <option value="">-- ምረጡ --</option>
                            <?php 
                                $allLabel = '-- የሁሉንም በአንድ ላይ --';
                                if ($r_type == 'wereda') $allLabel = '-- የሁሉንም ዞኖች --';
                                elseif ($r_type == 'zone') $allLabel = '-- የሁሉንም ዞኖች --';
                                elseif ($r_type == 'department') $allLabel = '-- የሁሉንም መምሪያዎች --';
                                elseif ($r_type == 'agency') $allLabel = '-- የሁሉንም ተጠሪ ተቋማት --';
                                elseif ($r_type == 'regional') $allLabel = '-- የሁሉንም የክልል ቢሮዎች --';
                            ?>
                            <option value="all" <?= ($secondSelect == 'all') ? 'selected' : '' ?>><?= $e($allLabel) ?></option>
                            <?php  
                            if (isset($secondList) && is_array($secondList)) {
                                foreach ($secondList as $item) {
                                    if ($r_type == 'calls') {
                                        $val = $item['branch_type'] ?? $item;
                                        $selected = ($secondSelect == $val) ? 'selected' : '';
                                        echo '<option value="' . $e($val) . '" ' . $selected . '>' . $e(ucfirst(str_replace('_', ' ', $val))) . '</option>';
                                    } else {
                                        $selected = ($secondSelect == $item['id']) ? 'selected' : '';
                                        echo '<option value="' . $e($item['id']) . '" ' . $selected . '>' . $e($item['name']) . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- 3ኛ ሳጥን (ለ agency, department, wereda) - hidden if agency & second_select == 'all' -->
                    <?php 
                    $showThird = false;
                    if ($r_type == 'department' || $r_type == 'wereda' || ($r_type == 'agency' && $secondSelect !== 'all' && $secondSelect !== '')) {
                        $showThird = true;
                    }
                    ?>
                    <div class="form-group-item mb-3" id="box_third" style="<?= $showThird ? 'display:flex;' : 'display:none;' ?>">
                        <label for="third_select" class="form-label">
                            <?= $e($thirdLabels[$r_type] ?? 'ተጠሪ ተቋማትን ይምረጡ') ?> <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="third_select" name="third_select" <?= $showThird ? 'required' : '' ?>>
                            <option value="">-- ምረጡ --</option>
                            <option value="all" <?= ($thirdSelect == 'all') ? 'selected' : '' ?>>-- የሁሉም (All) --</option>
                            <?php  
                            if (isset($thirdList) && is_array($thirdList)) {
                                foreach ($thirdList as $item) {
                                    $selected = ($thirdSelect == $item['id']) ? 'selected' : '';
                                    echo '<option value="' . $e($item['id']) . '" ' . $selected . '>' . $e($item['name']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Automatically pass value 'all' for third select if agency and second_select is 'all' -->
                    <?php if ($r_type == 'agency' && $secondSelect == 'all'): ?>
                        <input type="hidden" name="third_select" value="all">
                    <?php endif; ?>

                    <!-- 🌟 የዞን 3 አማራጮች ማጣሪያ (ዞን ሲመረጥ ብቻ የሚታይ) -->
                    <?php $showZone = ($r_type == 'zone'); ?>
                    <div class="form-group-item" id="box_zone_sub_filter" style="<?= $showZone ? 'display:flex;' : 'display:none;' ?>">
                        <label for="zone_sub_filter" class="form-label">የዞን ሪፖርት ዓይነት ይምረጡ <span class="text-danger">*</span></label>
                        <select class="form-control" id="zone_sub_filter" name="zone_sub_filter" <?= $showZone ? 'required' : '' ?>>
                            <option value="" <?= ($zoneSubFilter === '') ? 'selected' : '' ?> disabled>-- ምረጡ --</option>
                            <option value="all" <?= ($zoneSubFilter == 'all') ? 'selected' : '' ?>>ሁሉንም (ወረዳ እና መምሪያ)</option>
                            <option value="memriya_only" <?= ($zoneSubFilter == 'memriya_only') ? 'selected' : '' ?>>የመምሪያ መኪናዎች ብቻ</option>
                            <option value="wereda_only" <?= ($zoneSubFilter == 'wereda_only') ? 'selected' : '' ?>>የወረዳ መኪናዎች ብቻ</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- COLUMN 2: Report View Selection & Generate Action -->
            <div class="column-card">
                <div>
                    <div class="column-card-title">
                        <i class="fas fa-file-alt"></i>
                        <span>የሪፖርት ዓይነት እና ማሳያ</span>
                    </div>

                    <!-- ሪፖርት ዓይነት ሳጥን -->
                    <div class="form-group-item mb-4">
                        <label for="fifth_select" class="form-label">የሚፈልጉትን የሪፖርት ዓይነት ይምረጡ <span class="text-danger">*</span></label>
                        <select class="form-control" id="fifth_select" name="fifth_select" required>
                            <option value="">-- ሪፖርት ምረጡ --</option>
                            <?php foreach ($reportViews as $viewKey => $view): ?>
                            <option value="<?= $e($viewKey) ?>" <?= ($fifthSelect == $viewKey) ? 'selected' : '' ?>><?= $e($view['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Action Button -->
                <div>
                    <button type="submit" id="showReportBtn" class="btn btn-primary">
                        <i class="fas fa-eye"></i> ሪፖርት አሳይ
                    </button>
                </div>
            </div>

        </div>

    </form>
    </div>
  </div>
</div>
<script nonce="<?php echo htmlspecialchars($GLOBALS['nonce'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
document.addEventListener('DOMContentLoaded', function () {
    const reportForm = document.getElementById('reportForm') || document.getElementById('reportFilterForm');
    if (!reportForm) return;

    // report value → route (comes from $reportViews in PHP, one place to edit)
    const REPORT_ROUTES = <?= json_encode($reportRoutes, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const base = (typeof BASE_URL !== 'undefined') ? BASE_URL : '';

    reportForm.addEventListener('change', function (e) {
        const t = e.target;

        if (t.matches('.report-type-radio')) {
            // When the main report type changes, completely reset all downstream fields 
            // so old parameters (like second_select or third_select) disappear from the URL.
            const sec = document.getElementById('second_select');
            const thd = document.getElementById('third_select');
            const zone = document.getElementById('zone_sub_filter');
            const fifth = document.getElementById('fifth_select');

            if (sec) sec.value = '';
            if (thd) thd.value = '';
            if (zone) zone.value = '';
            if (fifth) fifth.value = '';

            reportForm.submit();
        } else if (t.matches('#second_select')) {
            // When second select changes, clear downstream third_select and fifth_select
            const thd = document.getElementById('third_select');
            const fifth = document.getElementById('fifth_select');

            if (thd) thd.value = '';
            if (fifth) fifth.value = '';

            reportForm.submit();
        }
    });

    reportForm.addEventListener('submit', function (e) {
        e.stopPropagation();

        if (!document.querySelector('input.report-type-radio:checked')) {
            e.preventDefault();
            alert("እባክዎ ከላይ ከሚገኙት የሪፖርት አይነቶች ውስጥ ቢያንስ አንዱን ይምረጡ!");
            const firstRadio = document.getElementById('type_all');
            if (firstRadio) firstRadio.focus();
            return;
        }

        const route = REPORT_ROUTES[document.getElementById('fifth_select').value];
        if (!route) return;

        e.preventDefault();
        const query = new URLSearchParams(new FormData(reportForm)).toString();
        window.open(base + '/' + route + '?' + query, '_blank', 'noopener');
    });
});
</script>