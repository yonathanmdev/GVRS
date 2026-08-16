<div class="modal fade" id="editOrgModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form 
        id="editOrgForm" 
        method="POST" 
        action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/update-organization"
        enctype="multipart/form-data"
      >
<?= \App\Helpers\Csrf::field(); ?>
        <!-- Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-edit mr-1"></i> ተቋም ማስተካከያ
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <!-- Hidden fields -->
          <input type="hidden" id="edit_org_id"       name="id">
          <!-- Organization name -->
          <div class="form-group mb-2">
            <label for="edit_org_name" class="mb-1">
              <small class="font-weight-bold">የተቋሙ ስም</small>
            </label>
            <input 
              type="text" 
              id="edit_org_name" 
              name="org_name" 
              class="form-control form-control-sm" 
              required
            >
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
            ዝጋ
          </button>
          <button type="submit" class="btn btn-warning btn-sm">
            <i class="fas fa-save mr-1"></i> አስተካክል
          </button>
        </div>

      </form>
    </div>
  </div>
</div>