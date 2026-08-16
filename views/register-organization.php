<?php
use App\Helpers\ViewHelper; 
$is_organization_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">

    <!-- Card -->
    <div class="card card-default">
     <div class="card card-primary card-outline">
      <div class="card-header">
<div class="card-header bg-white d-flex align-items-center">

  <div class="ml-auto">
    <button 
      type="button" 
      class="btn btn-primary btn-sm"
      data-toggle="modal" 
      data-target="#orgModal"
    >
      <i class="fas fa-plus mr-1"></i>
     <?= \__('add_organization') ?>
    </button>
  </div>

</div>

      </div>

      <div class="card-body">
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም ተቋም የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th><?= \__('organization_name') ?></th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($organizations)): ?>
        <?php foreach ($organizations as $index => $row): ?>
         <tr id="row-<?= $row['id'] ?>">
            <td><?= $index + 1 ?></td>
            <td><?=  ViewHelper::e($row['name']) ?></td>
           <td class="text-center align-middle">
  <div class="btn-group btn-group-sm shadow-sm" role="group">
               <button class="btn btn-outline-secondary btn-sm edit-org" 
                      data-id="<?= $row['uuid'] ?>" 
                      data-name="<?=  ViewHelper::e($row['name']) ?>"
                       title="አስተካክል"  >
                <i class="fas fa-edit"></i>
              </button> 
              <button class="btn btn-outline-danger btn-sm delete-org"
            data-uuid="<?= $row['uuid'] ?>"
            data-name="<?=  ViewHelper::e($row['name']) ?>">
             <i class="fas fa-trash-alt me-1"></i>
        </button>
  </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
      </div>

    </div>
    <!-- /.card -->

  </div>
</section>
<?php include 'partials/edit-organization-modal.php'; ?>

<!-- Modal (place OUTSIDE card) -->
<div class="modal fade" id="orgModal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <form id="orgForm" method="POST" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-organization-process" enctype="multipart/form-data">
  <?= \App\Helpers\Csrf::field(); ?>
        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-1"></i> <?= \__('add_organization') ?>
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <div class="form-group mb-2">
            <label for="org_name" class="mb-1"><small class="font-weight-bold"><?= \__('organization_name') ?></small></label>
            <input 
              type="text" 
              id="org_name" 
              class="form-control form-control-sm" 
              name="org_name" 
              placeholder="<?= \__('organization_name') ?>" 
              required
            >
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
            <?= \__('cancel') ?>
          </button>
          <button type="submit" class="btn btn-primary btn-sm">
            <?= \__('save') ?>
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

