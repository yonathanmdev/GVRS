<?php
use App\Helpers\ViewHelper;  
$is_zone_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Card -->
    <div class="card card-default">
      <div class="card-header">
        <div class="card-tools">
           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#branchModal">
            <i class="fas fa-plus mr-1"></i>
            <span class="d-none d-sm-inline-block">ዞን / ከተማ አስተዳደር መዝግብ</span>
          </button>
          
        </div>

      </div>

      <div class="card-body">
        <!-- Example Table (optional) -->
      <table id="example1" data-empty-msg="ምንም ዞን/ከተማ አስተዳደር የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>ስም</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($branches)): ?>
        <?php foreach ($branches as $index => $row): ?>
          <tr id="row-<?=  ViewHelper::e($row['uuid']) ?>">
            <td><?= $index + 1 ?></td>
            <td><?=  ViewHelper::e($row['name']) ?></td>
          <td class="text-center align-middle">
  <div class="btn-group btn-group-sm shadow-sm" role="group">
               <button class="btn btn-outline-secondary btn-sm  edit-branch" 
                      data-id="<?= $row['uuid'] ?>" 
                      data-name="<?=  ViewHelper::e($row['name']) ?>" 
                       data-type="<?=  ViewHelper::e($row['branch_type']) ?>" 
                       title="አስተካክል"  >
                <i class="fas fa-edit"></i>
              </button> 
              <button class="btn btn-outline-danger btn-sm delete-branch" 
                      data-id="<?= $row['uuid'] ?>" 
                      data-name="<?= ViewHelper::e($row['name']) ?>"
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
      </div>

    </div>
    <!-- /.card -->

  </div>
</section>
<?php include 'partials/edit-zone-modal.php'; ?>


<!-- Modal (place OUTSIDE card) -->
<div class="modal fade" id="branchModal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <form id="orgForm" method="POST" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-zone-process" enctype="multipart/form-data">
        <?= \App\Helpers\Csrf::field(); ?>

        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-1"></i> ዞን / ከተማ አስተዳደር
          </h6>

          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">

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
              <option value="" disabled selected>ይምረጡ</option>
              <option value="zone">ዞን</option>
              <option value="regio">ከተማ አስተዳደር</option>
            </select>
          </div>

        </div>

        <!-- Footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
            ዝጋ
          </button>

          <button type="submit" class="btn btn-primary btn-sm">
            መዝግብ
          </button>
        </div>

      </form>

    </div>
  </div>
</div>
