<?php

use App\Helpers\ViewHelper;

$is_vehicles_list_page = true;

$search         = $search ?? '';
$selectedBranch = $selectedBranch ?? '';
$selectedCategory = $selectedCategory ?? '';
$selectedBranchName = $selectedBranchName ?? null;
$totalCount = $totalCount ?? count($vehicles ?? []);

$basePath = rtrim($_ENV['BASE_URL'], '/') . '/vehicles-list';

// Active filters (used for pagination links, etc.)
$qs = array_filter([
    'search'    => $search,
    'branch_id' => $selectedBranch,
    'category'  => $selectedCategory,
], fn($v) => $v !== null && $v !== '');

$basePathWithQuery = $basePath . (!empty($qs) ? '?' . http_build_query($qs) : '');

// URL that clears only the search term, keeping branch_id/category (and any other active filter)
$clearSearchQs  = array_filter([
    'branch_id' => $selectedBranch,
    'category'  => $selectedCategory,
], fn($v) => $v !== null && $v !== '');
$clearSearchUrl = $basePath . (!empty($clearSearchQs) ? '?' . http_build_query($clearSearchQs) : '');

// Category options — mirrors the ownership_category toggle used on the
// vehicle registration form, so filtering here uses the same language.
$categories = [
    'regional'    => ['label' => 'የክልል ተጠሪ ተቋም', 'icon' => 'fa-landmark'],
    'institution' => ['label' => 'ተጠሪ መ/ቤት',      'icon' => 'fa-building'],
    'department'  => ['label' => 'መምሪያ',          'icon' => 'fa-sitemap'],
    'woreda'      => ['label' => 'ወረዳ',           'icon' => 'fa-map-marker-alt'],
];

// Labels for the summary panel — same set as $categories plus the
// "all" default, since $categories alone doesn't cover ''.
$categoryLabels = [
    ''            => 'አጠቃላይ',
    'regional'    => 'የክልል ተጠሪ ተቋም',
    'institution' => 'ተጠሪ መ/ቤት',
    'department'  => 'መምሪያ',
    'woreda'      => 'ወረዳ',
];

$isFilterActive = $selectedCategory !== '' || !empty($selectedBranch);

?>
<style>
/* =========================================================
   DESIGN TOKENS — navy / gold (matches vehicle edit modal)
   ========================================================= */

.vehicle-filter-wrapper,
.vehicle-filter-summary {
    --navy-900: #0f2138;
    --navy-800: #16304f;
    --navy-700: #1d3a61;
    --navy-100: #e8edf5;
    --gold-600: #b8892b;
    --gold-500: #c9a227;
    --gold-100: #faf3df;
    --ink-600:  #5b6472;
    --ink-800:  #2a313c;
    --line:     #e3e7ee;
}

/* =========================================================
   PAGE / CARD CHROME
   ========================================================= */

.content .card.card-primary.card-outline {
    border-top: 3px solid var(--navy-800, #16304f);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 33, 56, .06);
}

.content .card.card-primary.card-outline > .card-header {
    background: linear-gradient(180deg, #ffffff, #fafbfc);
    border-bottom: 1px solid var(--line, #e3e7ee);
    padding: .9rem 1.15rem;
}

.content .card.card-primary.card-outline > .card-header .card-title {
    display: flex;
    align-items: center;

    margin: 0;

    color: var(--navy-900, #0f2138);
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: .01em;
}

.content .card.card-primary.card-outline > .card-header .card-title i {
    color: var(--gold-600, #b8892b);
}


/* =========================================================
   VEHICLE FILTER / SEARCH
   Table is intentionally NOT styled here.
   ========================================================= */

.vehicle-filter-wrapper {
    padding: 1.1rem 1.15rem;
    margin-bottom: 1rem;

    background: #fbfcfe;
    border: 1px solid var(--line, #e3e7ee);
    border-radius: 10px;
}


/* =========================================================
   CATEGORY FILTER
   ========================================================= */

.vehicle-category-filter {
    display: grid !important;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;

    width: 100%;
    margin-bottom: 1.1rem !important;
}

.vehicle-category-filter .btn {
    min-height: 64px;

    margin: 0 !important;
    padding: .5rem;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    gap: 5px;

    background: #fff;
    border: 1px solid var(--line, #e3e7ee) !important;
    border-radius: 8px !important;

    color: var(--ink-600, #5b6472);
    font-size: .78rem;
    font-weight: 600;

    transition:
        background-color .15s ease,
        border-color .15s ease,
        color .15s ease,
        box-shadow .15s ease;
}

.vehicle-category-filter .btn i {
    font-size: 1.05rem;
    color: var(--navy-700, #1d3a61);
    transition: color .15s ease;
}

.vehicle-category-filter .btn:hover {
    background-color: var(--navy-100, #e8edf5);
    border-color: var(--navy-700, #1d3a61) !important;
    color: var(--navy-900, #0f2138);
}

.vehicle-category-filter .btn.active {
    background-color: var(--navy-900, #0f2138);
    border-color: var(--navy-900, #0f2138) !important;
    color: #fff;

    box-shadow: 0 2px 6px rgba(15, 33, 56, .25);
}

.vehicle-category-filter .btn.active i {
    color: var(--gold-500, #c9a227);
}

.vehicle-category-filter input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}


/* =========================================================
   BRANCH SELECT
   ========================================================= */

.vehicle-branch-filter {
    margin-bottom: 1.1rem !important;
}

.vehicle-branch-filter label {
    margin-bottom: .4rem;

    color: var(--ink-800, #2a313c);
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
}

.vehicle-branch-filter .form-control {
    min-height: 40px;

    background: #fff;
    border: 1px solid var(--line, #e3e7ee);
    border-radius: 6px;

    box-shadow: none;
}

.vehicle-branch-filter .form-control:focus {
    border-color: var(--navy-700, #1d3a61);

    box-shadow: 0 0 0 .15rem rgba(29, 58, 97, .12);
}


/* =========================================================
   SEARCH
   ========================================================= */

.vehicle-search-area {
    width: 100%;
}

.vehicle-search-area .input-group {
    width: 100%;
}

.vehicle-search-area .input-group-text {
    min-width: 42px;

    justify-content: center;

    background: #fff;
    border-color: var(--line, #e3e7ee);
}

.vehicle-search-area .form-control {
    height: 42px;

    max-width: none !important;

    border-color: var(--line, #e3e7ee);
    box-shadow: none;
}

.vehicle-search-area .form-control:focus {
    border-color: var(--navy-700, #1d3a61);

    box-shadow: 0 0 0 .15rem rgba(29, 58, 97, .12);
}

.vehicle-search-area .input-group-append .btn {
    min-width: 100px;
    height: 42px;

    background: var(--navy-900, #0f2138);
    border-color: var(--navy-900, #0f2138);

    font-weight: 600;

    transition: background-color .15s ease;
}

.vehicle-search-area .input-group-append .btn:hover {
    background: var(--navy-800, #16304f);
    border-color: var(--navy-800, #16304f);
}


/* =========================================================
   SEARCH RESULT
   ========================================================= */

.vehicle-search-result {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: .5rem;
    margin-top: .6rem;
}

.vehicle-search-result .result-count {
    color: var(--ink-600, #5b6472);
    font-size: .78rem;
}

.vehicle-search-result .btn-outline-secondary {
    border-color: var(--line, #e3e7ee);
    color: var(--ink-600, #5b6472);
}

.vehicle-search-result .btn-outline-secondary:hover {
    background: var(--navy-100, #e8edf5);
    border-color: var(--navy-700, #1d3a61);
    color: var(--navy-900, #0f2138);
}


/* =========================================================
   FILTER SUMMARY
   ========================================================= */

.vehicle-filter-summary {
    margin-bottom: 1rem !important;

    border: 1px solid var(--line, #e3e7ee) !important;
    border-radius: 10px !important;
    border-left: 3px solid var(--gold-500, #c9a227) !important;

    background: #fff;

    box-shadow: none !important;
}

.vehicle-filter-summary .card-body {
    padding: .8rem 1rem !important;
}

.vehicle-summary-content {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 1rem;
}

.vehicle-summary-items {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: .4rem 1.75rem;
}

.vehicle-summary-item {
    display: flex;
    align-items: center;

    gap: .4rem;
}

.vehicle-summary-label {
    color: var(--ink-600, #5b6472);
    font-size: .75rem;
}

.vehicle-summary-value {
    color: var(--navy-900, #0f2138);
    font-size: .82rem;
    font-weight: 700;
}

.vehicle-summary-actions {
    display: flex;
    align-items: center;

    gap: .5rem;

    flex-shrink: 0;
}

.vehicle-summary-actions .badge {
    padding: .45rem .7rem;

    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .02em;
}

.vehicle-summary-actions .badge-success {
    background: var(--gold-100, #faf3df);
    color: var(--gold-600, #b8892b);
}

.vehicle-summary-actions .btn-outline-secondary {
    border-color: var(--line, #e3e7ee);
    color: var(--ink-600, #5b6472);

    transition: all .15s ease;
}

.vehicle-summary-actions .btn-outline-secondary:hover {
    background: var(--navy-100, #e8edf5);
    border-color: var(--navy-700, #1d3a61);
    color: var(--navy-900, #0f2138);
}


/* =========================================================
   TABLET
   ========================================================= */

@media (max-width: 991.98px) {

    .vehicle-category-filter {
        grid-template-columns: repeat(3, 1fr);
    }

    .vehicle-summary-content {
        align-items: flex-start;
        flex-direction: column;
    }

    .vehicle-summary-actions {
        width: 100%;
    }
}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 767.98px) {

    .vehicle-filter-wrapper {
        padding: .85rem;
    }

    .vehicle-category-filter {
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
    }

    .vehicle-category-filter .btn {
        min-height: 60px;
        font-size: .72rem;
    }

    .vehicle-category-filter .btn i {
        font-size: .95rem;
    }


    /* Search */
    .vehicle-search-area .input-group {
        display: flex;
        flex-wrap: wrap;
    }

    .vehicle-search-area .input-group-prepend {
        display: none;
    }

    .vehicle-search-area .form-control {
        width: 100%;
        flex: 0 0 100%;

        margin-bottom: .5rem;

        border-radius: 6px !important;
    }

    .vehicle-search-area .input-group-append {
        width: 100%;
    }

    .vehicle-search-area .input-group-append .btn {
        width: 100%;
        border-radius: 6px !important;
    }


    /* Summary */
    .vehicle-filter-summary .card-body {
        padding: .7rem .8rem !important;
    }

    .vehicle-summary-items {
        width: 100%;

        display: grid;
        grid-template-columns: 1fr;

        gap: 0;
    }

    .vehicle-summary-item {
        justify-content: space-between;

        padding: .5rem 0;

        border-bottom: 1px solid var(--line, #e3e7ee);
    }

    .vehicle-summary-item:last-child {
        border-bottom: 0;
    }

    .vehicle-summary-actions {
        width: 100%;

        display: flex;
        align-items: stretch;
        flex-direction: column;

        margin-top: .55rem;
    }

    .vehicle-summary-actions .badge,
    .vehicle-summary-actions .btn {
        width: 100%;
        text-align: center;
    }
}
</style>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Card -->
        <div class="card card-primary card-outline">

            <!-- Card Header -->
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-car mr-2"></i>
                    ተሽከርካሪዎች
                </h3>
            </div>

            <!-- Card Body -->
            <div class="card-body">

              <!-- Filters -->
<form method="GET"
      action="<?= htmlspecialchars($basePath) ?>"
      class="vehicle-filter-wrapper"
      role="search"
      id="vehicle-filter-form">

    <!-- Category toggle -->
    <div class="btn-group btn-group-toggle vehicle-category-filter"
         data-toggle="buttons">

        <!-- All -->
        <label class="btn btn-outline-primary <?= $selectedCategory === '' ? 'active' : '' ?>">

            <input type="radio"
                   name="category"
                   value=""
                   class="category-filter-radio"
                   <?= $selectedCategory === '' ? 'checked' : '' ?>>

            <i class="fas fa-list"></i>

            <span>አጠቃላይ</span>

        </label>


        <!-- Categories -->
        <?php foreach ($categories as $value => $cat): ?>

            <label class="btn btn-outline-primary <?= $selectedCategory === $value ? 'active' : '' ?>">

                <input type="radio"
                       name="category"
                       value="<?= ViewHelper::e($value) ?>"
                       class="category-filter-radio"
                       <?= $selectedCategory === $value ? 'checked' : '' ?>>

                <i class="fas <?= $cat['icon'] ?>"></i>

                <span>
                    <?= ViewHelper::e($cat['label']) ?>
                </span>

            </label>

        <?php endforeach; ?>

    </div>


    <!-- =====================================================
         BRANCH SELECT
         ===================================================== -->

    <?php if ($selectedCategory !== '' && !empty($categoryBranches)): ?>

        <div class="form-group vehicle-branch-filter">

            <label for="category-branch-select">
                ተቋም ይምረጡ
            </label>

            <select
                id="category-branch-select"
                name="branch_id"
                class="form-control form-control-sm category-branch-select"
            >

                <option value="">
                    ሁሉንም አሳይ
                </option>

                <?php foreach ($categoryBranches as $branch): ?>

                    <option
                        value="<?= ViewHelper::e($branch['id']) ?>"
                        <?= (string) $selectedBranch === (string) $branch['id']
                            ? 'selected'
                            : '' ?>
                    >
                        <?= ViewHelper::e($branch['name']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         SEARCH
         ===================================================== -->

    <div class="vehicle-search-area">

        <div class="input-group input-group-sm">

            <div class="input-group-prepend">

                <span class="input-group-text">

                    <i class="fas fa-search text-muted"></i>

                </span>

            </div>


            <label
                for="vehicle-search"
                class="sr-only"
            >
                <?= \__('search') ?>
            </label>


            <input
                id="vehicle-search"
                type="text"
                name="search"
                class="form-control"
                placeholder="በሰሌዳ፣ ሞዴል፣ ቻሲስ፣ ሞተር ቁጥር፣ ብራንድ ወይም የመኪና ዓይነት ይፈልጉ..."
                value="<?= htmlspecialchars($search) ?>"
                autocomplete="off"
            >


            <?php

            /*
             * Preserve branch_id across a plain search submission ONLY
             * when there is no visible branch <select> already carrying
             * that same name.
             */

            ?>

            <?php if (
                !empty($selectedBranch)
                && !(
                    $selectedCategory !== ''
                    && !empty($categoryBranches)
                )
            ): ?>

                <input
                    type="hidden"
                    name="branch_id"
                    value="<?= htmlspecialchars($selectedBranch) ?>"
                >

            <?php endif; ?>


            <div class="input-group-append">

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="fas fa-search mr-1"></i>

                    <?= \__('search') ?>

                </button>

            </div>

        </div>


        <!-- Search result -->
        <?php if ($search !== ''): ?>

            <div class="vehicle-search-result">

                <a
                    href="<?= htmlspecialchars($clearSearchUrl) ?>"
                    class="btn btn-outline-secondary btn-sm"
                    aria-label="ፍለጋን አጽዳ"
                >

                    <i class="fas fa-times mr-1"></i>

                    አጽዳ

                </a>


                <span
                    class="result-count"
                    aria-live="polite"
                >

                    "<?= htmlspecialchars($search) ?>"

                    ውጤት:

                    <strong>
                        <?= count($vehicles ?? []) ?>
                    </strong>

                    ተገኝቷል

                </span>

            </div>

        <?php endif; ?>

    </div>

</form>


<!-- =====================================================
     FILTER SUMMARY
     ===================================================== -->

<div class="card card-outline card-secondary
            vehicle-filter-summary mb-3">

    <div class="card-body">

        <div class="vehicle-summary-content">


            <!-- Summary information -->
            <div class="vehicle-summary-items">


                <!-- Category -->
                <div class="vehicle-summary-item">

                    <span class="vehicle-summary-label">
                        ምድብ:
                    </span>

                    <strong class="vehicle-summary-value">

                        <?= ViewHelper::e(
                            $categoryLabels[$selectedCategory] ?? 'ሁሉም'
                        ) ?>

                    </strong>

                </div>


                <!-- Selected branch -->
                <div class="vehicle-summary-item">

                    <span class="vehicle-summary-label">
                        የተመረጠው ተቋም:
                    </span>

                    <strong class="vehicle-summary-value">

                        <?= $selectedBranchName !== null
                            ? ViewHelper::e($selectedBranchName)
                            : 'ሁሉም'
                        ?>

                    </strong>

                </div>


                <!-- Total -->
                <div class="vehicle-summary-item">

                    <span class="vehicle-summary-label">
                        ብዛት:
                    </span>

                    <strong class="vehicle-summary-value">
                        <?= (int) $totalCount ?>
                    </strong>

                </div>

            </div>


            <!-- Summary actions -->
            <div class="vehicle-summary-actions">

                <?php if ($isFilterActive): ?>

                    <span class="badge badge-success p-2">

                        <i class="fas fa-check-circle mr-1"></i>

                        ማጣሪያ ተግባራዊ ሆኗል

                    </span>


                    <a
                        href="<?= htmlspecialchars($basePath) ?>"
                        class="btn btn-sm btn-outline-secondary"
                    >

                        <i class="fas fa-times mr-1"></i>

                        አጽዳ

                    </a>

                <?php else: ?>

                    <span class="badge badge-secondary p-2">

                        እየታዩ ነው

                    </span>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

                <!-- Table -->
                <div class="table-responsive">

                    <table
                        id="example1"
                        data-empty-msg="ምንም የተመዘገበ ተሽከርካሪ /ማሽነሪ የለም።"
                        class="table table-bordered table-hover dtr-inline small mb-0"
                        style="color: #000;"
                        aria-describedby="example2_info"
                    >

                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>የሰሌዳ ቁጥር</th>
                                <th>ብራንድ</th>
                                <th>ዓይነት</th>
                                <th>ሞዴል</th>
                                <th>የመስሪያ ቤት</th>
                                <th>ዞን</th>
                                <th>የተመረተበት ዓ.ም</th>
                                <th>የተገዛበት ዓ.ም</th>
                                <th>ሁኔታ</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                                <?php
                                    // Continue row numbering across pages if pagination vars are available
                                    $currentPage = $currentPage ?? 1;
                                    $perPage     = $perPage ?? count($vehicles);
                                    $rowOffset   = ($currentPage - 1) * $perPage;
                                ?>

                                <?php foreach ($vehicles as $index => $vehicle): ?>

                                    <tr id="row-<?= ViewHelper::e($vehicle['uuid']) ?>">
                                        <td><?= $rowOffset + $index + 1 ?></td>

                                        <td class="font-weight-bold">
                                            <?= ViewHelper::e($vehicle['plate_number']) ?>
                                        </td>

                                        <td>
                                            <?= ViewHelper::e($vehicle['brand_name']) ?>
                                        </td>

                                        <td>
                                            <?= ViewHelper::e($vehicle['type_name']) ?>
                                        </td>

                                        <td>
                                            <?= ViewHelper::e($vehicle['model']) ?>
                                        </td>

                                        <td>
                                            <?= ViewHelper::e($vehicle['branch_name']) ?>
                                        </td>

                                        <td>
                                            <?= ViewHelper::e($vehicle['zone_name']) ?>
                                        </td>

                                        <td>
                                            <?= ViewHelper::e($vehicle['manufactured_year']) ?>
                                        </td>

                                        <td>
                                            <?= ViewHelper::e($vehicle['purchase_year']) ?>
                                        </td>

                                        <td>
    <?php
    $status = $vehicle['vehicle_status'];

    $statusMap = [
        'active'    => 'በአገልግሎት ላይ',
        'lost'      => 'የጠፋ',
        'destroyed' => 'የወደመ',
        'disposed'  => 'የተወገደ',
    ];

    $badgeMap = [
        'active'    => 'success',
        'lost'      => 'warning',
        'destroyed' => 'danger',
        'disposed'  => 'secondary',
    ];

    $statusText = $statusMap[$status] ?? $status;
    $badgeClass = $badgeMap[$status] ?? 'secondary';
    ?>

    <span class="badge badge-<?= $badgeClass ?>">
        <?= ViewHelper::e($statusText) ?>
    </span>
</td>

                                     <td class="text-center align-middle">
                                        <div class="btn-group btn-group-sm shadow-sm" role="group">
                                            <a
                                                href="<?= ViewHelper::e($_ENV['BASE_URL']) ?>/vehicles-show/<?= ViewHelper::e($vehicle['uuid']) ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="ዝርዝር ይመልከቱ"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </a>
                                           <button class="btn btn-outline-warning btn-sm edit-vehicle"
        data-id="<?= ViewHelper::e($vehicle['uuid']) ?>"
        data-name="<?= ViewHelper::e($vehicle['plate_number']) ?>"
        title="አስተካክል">
    <i class="fas fa-edit"></i>
</button>
              <button class="btn btn-outline-danger btn-sm delete-vehicle" 
                      data-id=<?= ViewHelper::e($vehicle['uuid']) ?>
                      data-name="<?= ViewHelper::e($vehicle['plate_number']) ?>"
                      title="ሰርዝ">

                 <i class="fas fa-trash-alt me-1"></i>
              </button>
  </div>
            </td>

                                    </tr>

                                <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <!-- Pagination -->
                <?php include 'partials/pagination.php'; ?>

            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->

    </div>
</section>
<?php include 'partials/edit-vehicle-modal.php'; ?>

<script nonce="<?= $GLOBALS['nonce'] ?? '' ?>">
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    document
        .querySelectorAll('.category-filter-radio')
        .forEach(function (radio) {
            radio.addEventListener('change', function () {

                // Clear any leftover branch_id from the previous
                // category before submitting — otherwise a stale
                // branch id from category A gets applied to category
                // B's unrelated branch list.
                var branchSelect = document.querySelector('.category-branch-select');
                if (branchSelect) {
                    branchSelect.value = '';
                }

                this.form.submit();
            });
        });

    var categoryBranchSelect = document.querySelector('.category-branch-select');
    if (categoryBranchSelect) {
        categoryBranchSelect.addEventListener('change', function () {
            this.form.submit();
        });
    }
});
</script>