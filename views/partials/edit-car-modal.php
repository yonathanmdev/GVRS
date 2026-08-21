<div class="modal fade" id="editBranchModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="editBranchForm">
<?= \App\Helpers\Csrf::field(); ?>
        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i>ብራንድ አስተካክል
          </h6>

          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">

          <input 
            type="hidden" 
            id="edit_car_id" 
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
              id="edit_car_name" 
              name="car_car_name" 
              class="form-control form-control-sm" 
              required
            >
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