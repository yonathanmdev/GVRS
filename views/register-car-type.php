<?php $is_car_page = true; ?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <!-- Card -->
    <div class="card card-default">
      <div class="card-header">
        <div class="card-tools">
          <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#carTypeModal">
            <i class="fas fa-plus mr-1"></i>
            <span class="d-none d-sm-inline-block">አዲስ የመኪና/ማሽን አይነት መመዝገብ</span>
          </button>
        </div>
      </div>

      <div class="card-body">
        <!-- Cars List Table -->
        <table id="example1" data-empty-msg="ምንም የተመዘገበ መረጃ የለም።" class="table table-bordered table-hover dataTable dtr-inline small" style="color: #000;">
          <thead class="thead-light">
            <tr>
              <th style="width: 40px;">#</th>
              <th>ብራንድ</th>
              <th>የዓይነት ስም</th>
              <th>የአገልግሎት አይነት</th>
              <th>መመዘኛ</th>
              <th>ምድብ</th>
              <th style="width: 100px;" class="text-center">ተግባር (Action)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($cars)): ?>
              <?php foreach ($cars as $index => $row): ?>
                <tr id="row-<?= htmlspecialchars($row['uuid'] ?? '') ?>">
                  <td><?= $index + 1 ?></td>
                  <td><?= htmlspecialchars($row['brand_name'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($row['type_name'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($row['service_name'] ?? '-') ?></td>
                  <td><span class="badge badge-info"><?= htmlspecialchars($row['measurement'] ?? '-') ?></span></td>
                  <td>
                    <span class="badge <?= ($row['catagory'] ?? '') === 'vehicle' ? 'badge-primary' : 'badge-warning' ?>">
                      <?= ($row['catagory'] ?? '') === 'vehicle' ? 'ተሽከርካሪ' : 'ማሽን' ?>
                    </span>
                  </td>
                  <td class="text-center align-middle">
                    <div class="btn-group btn-group-sm shadow-sm" role="group">
                      <!-- Edit Button -->
                      <button type="button" 
                              class="btn btn-outline-secondary btn-sm edit-car-type" 
                              data-uuid="<?= htmlspecialchars($row['uuid'] ?? '') ?>" 
                              data-brand-id="<?= htmlspecialchars($row['brand_id'] ?? '') ?>" 
                              data-type-id="<?= htmlspecialchars($row['type_name_id'] ?? '') ?>" 
                              data-service-id="<?= htmlspecialchars($row['service_type_id'] ?? '') ?>" 
                              data-measurement="<?= htmlspecialchars($row['measurement'] ?? '') ?>" 
                              data-catagory="<?= htmlspecialchars($row['catagory'] ?? '') ?>" 
                              title="አስተካክል">
                        <i class="fas fa-edit"></i>
                      </button>

                      <!-- Delete Button -->
                      <button type="button" 
                              class="btn btn-outline-danger btn-sm delete-car-type" 
                              data-uuid="<?= htmlspecialchars($row['uuid'] ?? '') ?>" 
                              data-name="<?= htmlspecialchars(($row['brand_name'] ?? '') . ' - ' . ($row['type_name'] ?? '')) ?>" 
                              title="ሰርዝ">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted">ምንም የተመዘገበ መረጃ የለም።</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- Add New Car Type Modal -->
<div class="modal fade" id="carTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">

      <form id="carTypeForm" method="POST" action="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'] ?? '', '/') . '/register-car-type-process') ?>">
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

          <!-- Brand Selection -->
          <div class="form-group mb-2">
            <label for="brand_id" class="mb-1">
              <small class="font-weight-bold">የመኪና ብራንድ (Brand)</small>
            </label>
            <select name="brand_id" id="brand_id" class="form-control form-control-sm" required>
              <option value="">-- ብራንድ ይምረጡ --</option>
              <?php if (!empty($brands)): ?>
                <?php foreach ($brands as $brand): ?>
                  <option value="<?= htmlspecialchars($brand['id']) ?>">
                    <?= htmlspecialchars($brand['brand_name'] ?? $brand['name'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Type Name Selection -->
          <div class="form-group mb-2">
            <label for="type_name" class="mb-1">
              <small class="font-weight-bold">የዓይነት ስም (Type Name)</small>
            </label>
            <select name="type_name" id="type_name" class="form-control form-control-sm" required>
              <option value="">-- የዓይነት ስም ይምረጡ --</option>
              <?php if (!empty($typeLists)): ?>
                <?php foreach ($typeLists as $type): ?>
                  <option value="<?= htmlspecialchars($type['id']) ?>">
                    <?= htmlspecialchars($type['cartype'] ?? $type['id'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Service Type Selection -->
          <div class="form-group mb-2">
            <label for="service_type" class="mb-1">
              <small class="font-weight-bold">የአገልግሎት አይነት (Service Type)</small>
            </label>
            <select name="service_type" id="service_type" class="form-control form-control-sm" required>
              <option value="">-- የአገልግሎት አይነት ይምረጡ --</option>
              <?php if (!empty($serviceTypes)): ?>
                <?php foreach ($serviceTypes as $service): ?>
                  <option value="<?= htmlspecialchars($service['id']) ?>">
                    <?= htmlspecialchars($service['service_name'] ?? $service['name'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Measurement ENUM -->
          <div class="form-group mb-2">
            <label for="measurement" class="mb-1">
              <small class="font-weight-bold">መመዘኛ (Measurement)</small>
            </label>
            <select name="measurement" id="measurement" class="form-control form-control-sm" required>
              <option value="">-- መመዘኛ ይምረጡ --</option>
              <option value="በሰው">በሰው</option>
              <option value="በሊትር">በሊትር</option>
              <option value="በፈረስ ጉልበት">በፈረስ ጉልበት</option>
              <option value="በኩንታል">በኩንታል</option>
            </select>
          </div>

          <!-- Category ENUM -->
          <div class="form-group mb-2">
            <label for="catagory" class="mb-1">
              <small class="font-weight-bold">ምድብ (Category)</small>
            </label>
            <select name="catagory" id="catagory" class="form-control form-control-sm" required>
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

<!-- Edit Car Type Modal -->
<div class="modal fade" id="editCarTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">

      <form id="editCarTypeForm" method="POST" action="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'] ?? '', '/') . '/update-car-type-process') ?>">
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

          <!-- Brand Selection -->
          <div class="form-group mb-2">
            <label for="edit_brand_id" class="mb-1">
              <small class="font-weight-bold">የመኪና ብራንድ (Brand)</small>
            </label>
            <select name="brand_id" id="edit_brand_id" class="form-control form-control-sm" required>
              <option value="">-- ብራንድ ይምረጡ --</option>
              <?php if (!empty($brands)): ?>
                <?php foreach ($brands as $brand): ?>
                  <option value="<?= htmlspecialchars($brand['id']) ?>">
                    <?= htmlspecialchars($brand['brand_name'] ?? $brand['name'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Type Name Selection -->
          <div class="form-group mb-2">
            <label for="edit_type_name" class="mb-1">
              <small class="font-weight-bold">የዓይነት ስም (Type Name)</small>
            </label>
            <select name="type_name" id="edit_type_name" class="form-control form-control-sm" required>
              <option value="">-- የዓይነት ስም ይምረጡ --</option>
              <?php if (!empty($typeLists)): ?>
                <?php foreach ($typeLists as $type): ?>
                  <option value="<?= htmlspecialchars($type['id']) ?>">
                    <?= htmlspecialchars($type['cartype'] ?? $type['id'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Service Type Selection -->
          <div class="form-group mb-2">
            <label for="edit_service_type" class="mb-1">
              <small class="font-weight-bold">የአገልግሎት አይነት (Service Type)</small>
            </label>
            <select name="service_type" id="edit_service_type" class="form-control form-control-sm" required>
              <option value="">-- የአገልግሎት አይነት ይምረጡ --</option>
              <?php if (!empty($serviceTypes)): ?>
                <?php foreach ($serviceTypes as $service): ?>
                  <option value="<?= htmlspecialchars($service['id']) ?>">
                    <?= htmlspecialchars($service['service_name'] ?? $service['name'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Measurement ENUM -->
          <div class="form-group mb-2">
            <label for="edit_measurement" class="mb-1">
              <small class="font-weight-bold">መመዘኛ (Measurement)</small>
            </label>
            <select name="measurement" id="edit_measurement" class="form-control form-control-sm" required>
              <option value="">-- መመዘኛ ይምረጡ --</option>
              <option value="በሰው">በሰው</option>
              <option value="በሊትር">በሊትር</option>
              <option value="በፈረስ ጉልበት">በፈረስ ጉልበት</option>
              <option value="በኩንታል">በኩንታል</option>
            </select>
          </div>

          <!-- Category ENUM -->
          <div class="form-group mb-2">
            <label for="edit_catagory" class="mb-1">
              <small class="font-weight-bold">ምድብ (Category)</small>
            </label>
            <select name="catagory" id="edit_catagory" class="form-control form-control-sm" required>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCarTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content">

      <form id="deleteCarTypeForm" method="POST" action="<?= htmlspecialchars(rtrim($_ENV['BASE_URL'] ?? '', '/') . '/delete-car-type-process') ?>">
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
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">ሰርዝን ሰርዝ (Cancel)</button>
          <button type="submit" class="btn btn-danger btn-sm">አዎ ሰርዝ</button>
        </div>

      </form>

    </div>
  </div>
</div>

<script nonce="<?php echo $GLOBALS['nonce'] ?? ''; ?>">
document.addEventListener('DOMContentLoaded', function () {

    // 1. Updated Pure JavaScript Modal Controller
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

        // ሞዳሉ ከመደበቁ በፊት Focus የያዘውን ኤለመንት ማላቀቅ (ስህተቱን የሚቀርፈው መስመር)
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

    // 2. Global Event Delegation
    document.addEventListener('click', function (e) {

        // EDIT BUTTON CLICK
        const editBtn = e.target.closest('.edit-car-type');
        if (editBtn) {
            e.preventDefault();

            document.getElementById('edit_uuid').value = editBtn.getAttribute('data-uuid') || '';
            document.getElementById('edit_brand_id').value = editBtn.getAttribute('data-brand-id') || '';
            document.getElementById('edit_type_name').value = editBtn.getAttribute('data-type-id') || '';
            document.getElementById('edit_service_type').value = editBtn.getAttribute('data-service-id') || '';
            document.getElementById('edit_measurement').value = editBtn.getAttribute('data-measurement') || '';
            document.getElementById('edit_catagory').value = editBtn.getAttribute('data-catagory') || '';

            openModal('editCarTypeModal');
            return;
        }

        // DELETE BUTTON CLICK
        const deleteBtn = e.target.closest('.delete-car-type');
        if (deleteBtn) {
            e.preventDefault();

            document.getElementById('delete_uuid').value = deleteBtn.getAttribute('data-uuid') || '';
            document.getElementById('delete_item_name').textContent = deleteBtn.getAttribute('data-name') || '';

            openModal('deleteCarTypeModal');
            return;
        }

        // CLOSE MODAL BUTTONS (data-dismiss="modal" ወይም X)
        const dismissBtn = e.target.closest('[data-dismiss="modal"]');
        if (dismissBtn) {
            e.preventDefault();
            const modal = dismissBtn.closest('.modal');
            if (modal) {
                closeModal(modal);
            }
            return;
        }

        // CLICK OUTSIDE MODAL TO CLOSE
        if (e.target.classList.contains('modal') && e.target.classList.contains('show')) {
            closeModal(e.target);
        }
    });
});
</script>