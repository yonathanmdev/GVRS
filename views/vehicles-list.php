<?php

use App\Helpers\ViewHelper;

$is_vehicles_list_page = true;

$search         = $search ?? '';
$selectedBranch = $selectedBranch ?? '';

$basePath = rtrim($_ENV['BASE_URL'], '/') . '/vehicles-list';

// Active filters (used for pagination links, etc.)
$qs = array_filter([
    'search'    => $search,
    'branch_id' => $selectedBranch,
], fn($v) => $v !== null && $v !== '');

$basePathWithQuery = $basePath . (!empty($qs) ? '?' . http_build_query($qs) : '');

// URL that clears only the search term, keeping branch_id (and any other active filter)
$clearSearchQs  = array_filter(['branch_id' => $selectedBranch], fn($v) => $v !== null && $v !== '');
$clearSearchUrl = $basePath . (!empty($clearSearchQs) ? '?' . http_build_query($clearSearchQs) : '');

?>

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

                <!-- Search -->
                <form method="GET"
                      action="<?= htmlspecialchars($basePath) ?>"
                      class="mb-3"
                      role="search">

                    <div class="input-group input-group-sm">

                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>

                        <label for="vehicle-search" class="sr-only"><?= \__('search') ?></label>
                        <input
                            id="vehicle-search"
                            type="text"
                            name="search"
                            class="form-control"
                            style="max-width: 520px;"
                            placeholder="በሰሌዳ፣ ሞዴል፣ ቻሲስ፣ ሞተር ቁጥር፣ ብራንድ ወይም የመኪና ዓይነት ይፈልጉ..."
                            value="<?= htmlspecialchars($search) ?>"
                            autocomplete="off"
                        >

                        <?php if (!empty($selectedBranch)): ?>
                            <input type="hidden" name="branch_id" value="<?= htmlspecialchars($selectedBranch) ?>">
                        <?php endif; ?>

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i>
                                <?= \__('search') ?>
                            </button>
                        </div>

                    </div>

                    <?php if ($search !== ''): ?>
                        <div class="mt-2 d-flex align-items-center flex-wrap">
                            <a href="<?= htmlspecialchars($clearSearchUrl) ?>"
                               class="btn btn-outline-secondary btn-sm"
                               aria-label="ፍለጋን አጽዳ">
                                <i class="fas fa-times mr-1"></i> አጽዳ
                            </a>
                            <span class="text-muted small ml-2" aria-live="polite">
                                "<?= htmlspecialchars($search) ?>" ውጤት:
                                <strong><?= count($vehicles ?? []) ?></strong> ተገኝቷል
                            </span>
                        </div>
                    <?php endif; ?>

                </form>

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