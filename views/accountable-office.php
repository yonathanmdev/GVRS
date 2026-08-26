<?php
use App\Helpers\ViewHelper;  
$is_accountable_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Card -->
    <div class="card card-default">
      <div class="card-header">
        <div class="card-tools">
           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#branchModal">
            <i class="fas fa-plus mr-1"></i>
            <span class="d-none d-sm-inline-block">መምሪያ/ተጠሪ ተቋም መዝግብ</span>
          </button>
          
        </div>

      </div>

      <div class="card-body">
<form method="GET" action="<?= rtrim($_ENV['BASE_URL'], '/') . '/accountable-office' ?>" class="form-inline mb-3">
        <input
            type="text"
            name="search"
            class="form-control form-control-sm mr-2"
            style="min-width: 260px;"
            placeholder="በስም ወይም በእናት መስሪያ ቤት ስም ይፈልጉ"
            value="<?= htmlspecialchars($search ?? '') ?>"
          >
          <button type="submit" class="btn btn-primary btn-sm mr-2">
            <i class="fas fa-search mr-1"></i> <?= \__('search') ?>
          </button>
          <?php if (!empty($search)): ?>
            <a href="<?= rtrim($_ENV['BASE_URL'], '/') . '/accountable-office' ?>" class="btn btn-outline-secondary btn-sm">
              አጽዳ
            </a>
          <?php endif; ?>
        </form>

        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም መ/ቤት የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>ስም</th>
        <th>እናት መስሪያ ቤት</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($branches)): ?>
        <?php foreach ($branches as $index => $row): ?>
          <tr id="row-<?=  ViewHelper::e($row['uuid']) ?>">
            <td><?= $index + 1 ?></td>
            <td><?=  ViewHelper::e($row['office_name']) ?></td>
             <td><?=  ViewHelper::e($row['bureau_name']) ?></td>
          <td class="text-center align-middle">
  <div class="btn-group btn-group-sm shadow-sm" role="group">
               <button 
    class="btn btn-outline-secondary btn-sm edit-branch" 
    data-id="<?= $row['uuid'] ?>" 
    data-name="<?= ViewHelper::e($row['office_name']) ?>" 
    data-type="<?= ViewHelper::e($row['branch_type']) ?>" 
    data-parent-name="<?= ViewHelper::e($row['bureau_name']) ?>" 
    data-parent-uuid="<?= ViewHelper::e($row['bureau_uuid']) ?>" 
    title="አስተካክል"
>
    <i class="fas fa-edit"></i>
</button>
              <button class="btn btn-outline-danger btn-sm delete-branch" 
                      data-id="<?= $row['uuid'] ?>" 
                      data-name="<?= ViewHelper::e($row['office_name']) ?>"
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
  <?php $basePath = rtrim($_ENV['BASE_URL'], '/') . '/accountable-office'; ?>
<?php include 'partials/pagination.php'; ?>
      </div>

    </div>
    <!-- /.card -->

  </div>
</section>
<?php include 'partials/edit-accountable-office-modal.php'; ?>

<div class="modal fade" id="branchModal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <form id="orgForm"
            method="POST"
            action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-accountable-office"
            enctype="multipart/form-data">

        <?= \App\Helpers\Csrf::field(); ?>

        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-1"></i> መምሪያ/ተጠሪ ተቋም ቤት
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
              እናት መስሪያ ቤት
              </small>
            </label>

            <select
              class="form-control form-control-sm"
              id="parent"
              name="parent"
              required
            >

              <option value="" disabled selected>
                ይምረጡ
              </option>

              <?php foreach ($parents as $parent): ?>

                <option
                  value="<?= ViewHelper::e($parent['uuid']) ?>"
                  data-type="<?= ViewHelper::e($parent['branch_type']) ?>"
                >
                  <?= ViewHelper::e($parent['name']) ?>
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
               <?php foreach ($branchTypes as $type): ?>
    <?php if ($type['type_in_eng'] === 'bureau') continue; ?>
    <option value="<?= ViewHelper::e($type['type_in_eng']) ?>">
        <?= ViewHelper::e($type['type_in_am']) ?>
    </option>
<?php endforeach; ?>

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
