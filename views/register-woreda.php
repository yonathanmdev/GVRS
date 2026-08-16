<?php
use App\Helpers\ViewHelper;  
$is_woreda_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Card -->
    <div class="card card-default">
      <div class="card-header">
        <div class="card-tools">
           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#branchModal">
            <i class="fas fa-plus mr-1"></i>
            <span class="d-none d-sm-inline-block">ወረዳ / ክ/ከተማ መዝግብ</span>
          </button>
          
        </div>

      </div>

      <div class="card-body">
<form method="GET" action="<?= rtrim($_ENV['BASE_URL'], '/') . '/register-woreda' ?>" class="form-inline mb-3">
        <input
            type="text"
            name="search"
            class="form-control form-control-sm mr-2"
            style="min-width: 260px;"
            placeholder="በወረዳ / ክ/ከተማ  ወይም ዞን /ከ/አስተዳደር ይፈልጉ"
            value="<?= ViewHelper::e($search ?? '') ?>"
          >
          <button type="submit" class="btn btn-primary btn-sm mr-2">
            <i class="fas fa-search mr-1"></i> <?= \__('search') ?>
          </button>
          <?php if (!empty($search)): ?>
            <a href="<?= rtrim($_ENV['BASE_URL'], '/') . '/register-woreda' ?>" class="btn btn-outline-secondary btn-sm">
              አጽዳ
            </a>
          <?php endif; ?>
        </form>

        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም ወረዳ / ክ/ከተማ የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>ስም</th>
        <th>የሚገኝበት ዞን /ከተማ አስተዳደር</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($branches)): ?>
        <?php foreach ($branches as $index => $row): ?>
          <tr id="row-<?=  ViewHelper::e($row['uuid']) ?>">
            <td><?= $index + 1 ?></td>
            <td><?=  ViewHelper::e($row['woreda_name']) ?></td>
             <td><?=  ViewHelper::e($row['zone_name']) ?></td>
          <td class="text-center align-middle">
  <div class="btn-group btn-group-sm shadow-sm" role="group">
               <button 
    class="btn btn-outline-secondary btn-sm edit-branch" 
    data-id="<?= $row['uuid'] ?>" 
    data-name="<?= ViewHelper::e($row['woreda_name']) ?>" 
    data-type="<?= ViewHelper::e($row['branch_type']) ?>" 
    data-zone-name="<?= ViewHelper::e($row['zone_name']) ?>" 
    data-zone-uuid="<?= ViewHelper::e($row['zone_uuid']) ?>" 
    title="አስተካክል"
>
    <i class="fas fa-edit"></i>
</button>
              <button class="btn btn-outline-danger btn-sm delete-branch" 
                      data-id="<?= $row['uuid'] ?>" 
                      data-name="<?= ViewHelper::e($row['woreda_name']) ?>"
                      data-type="<?= ViewHelper::e($row['branch_type']) ?>" 
                      title="ሰርዝ">

                 <i class="fas fa-trash-alt me-1"></i>
              </button>
  </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <?php $basePath = rtrim($_ENV['BASE_URL'], '/') . '/register-woreda'; ?>
<?php include 'partials/pagination.php'; ?>
      </div>

    </div>
    <!-- /.card -->

  </div>
</section>
<?php include 'partials/edit-woreda-modal.php'; ?>

<div class="modal fade" id="branchModal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <form id="orgForm"
            method="POST"
            action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-woreda-process"
            enctype="multipart/form-data">

        <?= \App\Helpers\Csrf::field(); ?>

        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-1"></i> ወረዳ / ክ/ከተማ
          </h6>

          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">

          <!-- Branch Name -->
          <div class="form-group mb-2">
            <label for="branch_name" class="mb-1">
              <small class="font-weight-bold">
                ስም
              </small>
            </label>

            <input
              type="text"
              id="branch_name"
              class="form-control form-control-sm"
              name="branch_name"
              placeholder="ስም ያስገቡ"
              required
            >
          </div>


          <!-- Zone -->
          <div class="form-group mb-2">
            <label for="zone" class="mb-1">
              <small class="font-weight-bold">
                ዞን / ከ/አስተዳደር
              </small>
            </label>

            <select
              class="form-control form-control-sm"
              id="zone"
              name="zone"
              required
            >

              <option value="" disabled selected>
                ይምረጡ
              </option>

              <?php foreach ($zones as $zone): ?>

                <option
                  value="<?= ViewHelper::e($zone['uuid']) ?>"
                  data-type="<?= ViewHelper::e($zone['branch_type']) ?>"
                >
                  <?= ViewHelper::e($zone['name']) ?>
                </option>

              <?php endforeach; ?>

            </select>
          </div>


          <!-- Branch Type -->
          <div class="form-group mb-2">

            <label for="branch_type" class="mb-1">
              <small class="font-weight-bold">
                ዓይነት
              </small>
            </label>

            <select
              class="form-control form-control-sm"
              id="branch_type"
              name="branch_type"
              required
            >

              <option value="" disabled selected>
                ይምረጡ
              </option>

            </select>

          </div>

        </div>


        <!-- Footer -->
        <div class="modal-footer justify-content-between">

          <button
            type="button"
            class="btn btn-default btn-sm"
            data-dismiss="modal"
          >
            ዝጋ
          </button>

          <button
            type="submit"
            class="btn btn-primary btn-sm"
          >
            መዝግብ
          </button>

        </div>

      </form>

    </div>
  </div>
</div>
<script nonce="<?php echo $GLOBALS['nonce']; ?>">
    document.addEventListener('DOMContentLoaded', function () {

    const zoneSelect = document.getElementById('zone');
    const branchTypeSelect = document.getElementById('branch_type');

    if (!zoneSelect || !branchTypeSelect) {
        return;
    }

    zoneSelect.addEventListener('change', function () {

        const selectedOption =
            this.options[this.selectedIndex];

        const zoneType =
            selectedOption.getAttribute('data-type');

        // Clear existing options
        branchTypeSelect.innerHTML = `
            <option value="" disabled selected>
                ይምረጡ
            </option>
        `;

        if (zoneType === 'regio') {

            branchTypeSelect.innerHTML += `
                <option value="kifle_ketema">
                    ክ/ከተማ
                </option>
            `;

        } else if (zoneType === 'zone') {

            branchTypeSelect.innerHTML += `
                <option value="woreda">
                    ወረዳ
                </option>

                <option value="ketema_woreda">
                    ከተማ ወረዳ
                </option>
            `;
        }

    });

});
</script>