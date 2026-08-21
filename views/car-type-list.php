<?php $is_car_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card-header">
        <div class="card-tools">
          <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCarTypeListModal">
            <i class="fas fa-plus mr-1"></i>
            <span class="d-none d-sm-inline-block">አዲስ የመኪና/ማሽን አይነት መዝግብ</span>
          </button>
        </div>
      </div>

      <div class="card-body">
        <table id="example1" data-empty-msg="ምንም የተመዘገበ መረጃ የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;">
          <thead class="thead-light">
            <tr>
              <th style="width: 40px;">#</th>
              <th>የዓይነት ስም (Car Type)</th>
              <th>ምድብ (Category)</th>
              <th style="width: 100px;" class="text-center">ተግባር (Action)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($typeLists)): ?>
              <?php foreach ($typeLists as $index => $row): ?>
                <tr id="row-<?= htmlspecialchars($row['uuid'] ?? '') ?>">
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($row['cartype'] ?? '-') ?></td>
                  <td>
                    <span class="badge <?= ($row['carcatagory'] ?? '') === 'vehicle' ? 'badge-primary' : 'badge-warning' ?>">
                      <?= ($row['carcatagory'] ?? '') === 'vehicle' ? 'ተሽከርካሪ (Vehicle)' : 'ማሽን (Machine)' ?>
                    </span>
                  </td>
                  <td class="text-center align-middle">
                    <div class="btn-group btn-group-sm shadow-sm" role="group">
                      <!-- Edit Button -->
                      <button type="button" 
                              class="btn btn-outline-secondary btn-sm edit-cartype-btn" 
                              data-uuid="<?= htmlspecialchars($row['uuid'] ?? '') ?>" 
                              data-cartype="<?= htmlspecialchars($row['cartype'] ?? '') ?>" 
                              data-carcatagory="<?= htmlspecialchars($row['carcatagory'] ?? '') ?>" 
                              title="አስተካክል">
                        <i class="fas fa-edit"></i>
                      </button>

                      <!-- Delete Button -->
                      <button type="button" 
                              class="btn btn-outline-danger btn-sm delete-cartype-btn" 
                              data-uuid="<?= htmlspecialchars($row['uuid'] ?? '') ?>" 
                              data-name="<?= htmlspecialchars($row['cartype'] ?? '') ?>" 
                              title="ሰርዝ">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center text-muted">ምንም የተመዘገበ መረጃ የለም።</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- Add New Modal -->
<div class="modal fade" id="addCarTypeListModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <form id="addCarTypeForm" method="POST" action="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'] ?? '', '/') . '/register-car-type-list-process') ?>">
        <?= \App\Helpers\Csrf::field(); ?>

        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-car mr-1"></i> አዲስ የመኪና/ማሽን አይነት መመዝገቢያ
          </h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <!-- Car Type Input -->
          <div class="form-group mb-2">
            <label for="cartype" class="mb-1"><small class="font-weight-bold">የዓይነት ስም (Car Type Name)</small></label>
            <input type="text" name="cartype" id="cartype" class="form-control form-control-sm" placeholder="ምሳሌ፡ ሲንግል ካፕ, አይሱዙ ራፍ, ክሬን መኪና" required>
          </div>

          <!-- Category Selection -->
          <div class="form-group mb-2">
            <label for="carcatagory" class="mb-1"><small class="font-weight-bold">ምድብ (Category)</small></label>
            <select name="carcatagory" id="carcatagory" class="form-control form-control-sm" required>
              <option value="">-- ምድብ ይምረጡ --</option>
              <option value="vehicle">ተሽከርካሪ (Vehicle)</option>
              <option value="machine">ማሽን (Machine)</option>
            </select>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">ዝጋ</button>
          <button type="submit" class="btn btn-primary btn-sm">መዝግብ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCarTypeListModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <form id="editCarTypeForm" method="POST" action="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'] ?? '', '/') . '/update-car-type-list-process') ?>">
        <?= \App\Helpers\Csrf::field(); ?>
        <input type="hidden" name="uuid" id="edit_uuid">

        <div class="modal-header bg-light">
          <h6 class="modal-title font-weight-bold text-dark">
            <i class="fas fa-edit mr-1 text-primary"></i> መረጃ ማስተካከያ
          </h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <!-- Car Type Input -->
          <div class="form-group mb-2">
            <label for="edit_cartype" class="mb-1"><small class="font-weight-bold">የዓይነት ስም (Car Type Name)</small></label>
            <input type="text" name="cartype" id="edit_cartype" class="form-control form-control-sm" required>
          </div>

          <!-- Category Selection -->
          <div class="form-group mb-2">
            <label for="edit_carcatagory" class="mb-1"><small class="font-weight-bold">ምድብ (Category)</small></label>
            <select name="carcatagory" id="edit_carcatagory" class="form-control form-control-sm" required>
              <option value="">-- ምድብ ይምረጡ --</option>
              <option value="vehicle">ተሽከርካሪ (Vehicle)</option>
              <option value="machine">ማሽን (Machine)</option>
            </select>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">ዝጋ</button>
          <button type="submit" class="btn btn-success btn-sm">አስተካክል (Save Changes)</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteCarTypeListModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content">
      <form id="deleteCarTypeForm" method="POST" action="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'] ?? '', '/') . '/delete-car-type-list-process') ?>">
        <?= \App\Helpers\Csrf::field(); ?>
        <input type="hidden" name="uuid" id="delete_uuid">

        <div class="modal-header bg-danger text-white py-2">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-exclamation-triangle mr-1"></i> ስረዛ ማረጋገጫ
          </h6>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body text-center py-3">
          <p class="mb-1 text-dark">እርግጠኛ ነዎት ይህንን መረጃ መሰረዝ ይፈልጋሉ?</p>
          <strong id="delete_item_name" class="text-danger"></strong>
        </div>

        <div class="modal-footer justify-content-between py-1">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">ተመለስ (Cancel)</button>
          <button type="submit" class="btn btn-danger btn-sm">አዎ ሰርዝ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script nonce="<?php echo $GLOBALS['nonce'] ?? ''; ?>">
document.addEventListener('DOMContentLoaded', function () {

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        modal.removeAttribute('aria-hidden');
        modal.setAttribute('aria-modal', 'true');
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');

        if (!document.querySelector('.modal-backdrop')) {
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'js-modal-backdrop';
            document.body.appendChild(backdrop);
        }
    }

    function closeModal(modal) {
        if (typeof modal === 'string') {
            modal = document.getElementById(modal);
        }
        if (!modal) return;

        if (document.activeElement && modal.contains(document.activeElement)) {
            document.activeElement.blur();
        }

        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');

        const backdrop = document.getElementById('js-modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }

    document.addEventListener('click', function (e) {
        // Edit Button Trigger
        const editBtn = e.target.closest('.edit-cartype-btn');
        if (editBtn) {
            e.preventDefault();
            document.getElementById('edit_uuid').value = editBtn.getAttribute('data-uuid') || '';
            document.getElementById('edit_cartype').value = editBtn.getAttribute('data-cartype') || '';
            document.getElementById('edit_carcatagory').value = editBtn.getAttribute('data-carcatagory') || '';

            openModal('editCarTypeListModal');
            return;
        }

        // Delete Button Trigger
        const deleteBtn = e.target.closest('.delete-cartype-btn');
        if (deleteBtn) {
            e.preventDefault();
            document.getElementById('delete_uuid').value = deleteBtn.getAttribute('data-uuid') || '';
            document.getElementById('delete_item_name').textContent = deleteBtn.getAttribute('data-name') || '';

            openModal('deleteCarTypeListModal');
            return;
        }

        // Close Modal Trigger
        const dismissBtn = e.target.closest('[data-dismiss="modal"]');
        if (dismissBtn) {
            e.preventDefault();
            const modal = dismissBtn.closest('.modal');
            if (modal) {
                closeModal(modal);
            }
            return;
        }

        // Click Backdrop to Close
        if (e.target.classList.contains('modal') && e.target.classList.contains('show')) {
            closeModal(e.target);
        }
    });
});
</script>