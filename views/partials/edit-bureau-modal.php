<?php 
use App\Helpers\ViewHelper;  
?>
<div class="modal fade" id="editBranchModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="editBranchForm">
<?= \App\Helpers\Csrf::field(); ?>
        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i>ቢሮ አስተካክል
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
               <option value="" disabled selected>ይምረጡ</option>
               <?php foreach ($branchTypes as $type): ?>
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
            class="btn btn-warning btn-sm"
          >
            አስተካክል
          </button>

        </div>

      </form>

    </div>
  </div>
</div>