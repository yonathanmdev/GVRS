<?php use App\Helpers\ViewHelper;  ?>
<div class="modal fade" id="editBranchModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="editBranchForm" method="POST" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/update-woreda-process"
            enctype="multipart/form-data">
        <?= \App\Helpers\Csrf::field(); ?>

        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i>ወረዳ /ክ/ከተማ አስተካክል
          </h6>

          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">

          <input 
            type="hidden" 
            id="edit_branch_id" 
            name="id"
          >

          <div class="form-group mb-2">
            <label 
              for="edit_branch_name" 
              class="mb-1"
            >
              <small class="font-weight-bold">
                ስም
              </small>
            </label>

            <input 
              type="text" 
              id="edit_branch_name" 
              name="branch_name" 
              class="form-control form-control-sm" 
              required
            >
          </div>

          <!-- Zone -->
          <div class="form-group mb-2">
            <label 
              for="edit_zone" 
              class="mb-1"
            >
              <small class="font-weight-bold">
                ዞን / ከ/አስተዳደር
              </small>
            </label>

            <select 
              class="form-control form-control-sm" 
              id="edit_zone" 
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
            <label 
              for="edit_branch_type" 
              class="mb-1"
            >
              <small class="font-weight-bold">
                ዓይነት
              </small>
            </label>

            <select 
              class="form-control form-control-sm" 
              id="edit_branch_type" 
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
            class="btn btn-warning btn-sm"
          >
            አስተካክል
          </button>

        </div>

      </form>

    </div>
  </div>
</div>