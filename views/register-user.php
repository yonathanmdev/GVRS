<?php
use App\Helpers\ViewHelper;
 $is_register_user_page = true; ?>
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
      data-target="#userModal"
    >
      <i class="fas fa-user-plus mr-2"></i>
     <?= \__('add_user') ?>
    </button>
  </div>

</div>

      </div>

<div class="card-body">

        <!-- Search bar -->
<form method="GET" action="<?= rtrim($_ENV['BASE_URL'], '/') . '/register-user' ?>" class="form-inline mb-3">
        <input
            type="text"
            name="search"
            class="form-control form-control-sm mr-2"
            style="min-width: 260px;"
            placeholder="በስም፣ በUsername ወይም በስልክ ቁጥር ይፈልጉ"
            value="<?= htmlspecialchars($search ?? '') ?>"
          >
          <button type="submit" class="btn btn-primary btn-sm mr-2">
            <i class="fas fa-search mr-1"></i> <?= \__('search') ?>
          </button>
          <?php if (!empty($search)): ?>
            <a href="<?= rtrim($_ENV['BASE_URL'], '/') . '/register-user' ?>" class="btn btn-outline-secondary btn-sm">
              አጽዳ
            </a>
          <?php endif; ?>
        </form>

      <table id="example1" data-empty-msg="ምንም ተቆጣጣሪ የለም።" class="table table-bordered table-hover dtr-inline small" style="color: #000;" aria-describedby="example2_info">
    <thead class="thead-light">
      <tr>
        <th>#</th>
        <th>ስም </th>
          <th>Username</th>
          <th>መ/ቤት</th>
        <th>Role</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($users)): ?>
        <?php foreach ($users as $index => $row): ?>
        <?php $currentUuid = $_SESSION['user']['uuid'] ?? null;
              $currentRole = $_SESSION['user']['role'] ?? null;

              $isSelf     = ($row['uuid'] === $currentUuid);
              $isSameRole = ($row['role'] === $currentRole);
        ?>
          <tr id="row-<?= ViewHelper::e($row['id']) ?>">
            <td><?= (($currentPage - 1) * $perPage) + $index + 1 ?></td>
            <td><?= ViewHelper::e($row['first_name'].' '.$row['father_name'].' '.$row['grand_father_name']) ?></td>
            <td><?=ViewHelper::e($row['username']) ?></td>
            <td><?= ViewHelper::e($row['organization_name']) ?></td>
            <td>
<?php
$role = $row['role'];

$roleMap = [
    'system_admin' => 'System Admin',
    'admin' => 'Admin',
];

echo ViewHelper::e($roleMap[$role] ?? 'ባለሙያ');
?>
</td>
            <td>
<?php
$statusMap = [
    '1'   => ['label' => 'Active',              'class' => 'badge-success'],
    '2' => ['label' => 'Suspended',            'class' => 'badge-danger'],
    '0'         => ['label' => 'የይለፍ ቃል ያልቀየሩ',    'class' => 'badge-warning'], // default password not yet changed
];
$statusInfo = $statusMap[$row['is_active'] ?? ''] ?? ['label' => ViewHelper::e($row['status']), 'class' => 'badge-light'];
?>
<span class="badge <?= $statusInfo['class'] ?>"><?= $statusInfo['label'] ?></span>           </td>
            <?php
$isBlockedRole = (!$isSelf && $isSameRole); // peer with the same role, but not me
?>

<td>

  <?php if (!$isBlockedRole): ?>
    <button class="btn btn-outline-secondary btn-sm edit-user"
            data-id="<?= ViewHelper::e($row['uuid']) ?>" title="አስተካክል">
      <i class="fas fa-edit"></i>
    </button>
  <?php endif; ?>

  <form method="POST" action="<?= $_ENV['BASE_URL'] ?>/reset-password" class="reset-password-form" style="display:inline;">
    <?= \App\Helpers\Csrf::field(); ?>
    <input type="hidden" name="id" value="<?= ViewHelper::e($row['uuid']) ?>">
    <button type="submit" class="btn btn-outline-warning btn-sm reset-password-btn"
            data-name="<?= ViewHelper::e($row['first_name'] . ' ' . $row['father_name']) ?>"
            title="Reset Password">
      <i class="fas fa-sync-alt"></i>
    </button>
  </form>

  <?php if (!$isSelf && !$isSameRole): ?>
    <button class="btn btn-outline-danger btn-sm delete-user"
            data-id="<?= ViewHelper::e($row['uuid']) ?>"
            data-name="<?= ViewHelper::e($row['first_name'] . ' ' . $row['father_name'] . ' ' . $row['grand_father_name']) ?>"
            title="ሰርዝ">
      <i class="fas fa-trash-alt me-1"></i>
    </button>
  <?php endif; ?>

</td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
<?php $basePath = rtrim($_ENV['BASE_URL'], '/') . '/register-user'; ?>
<?php include 'partials/pagination.php'; ?>
      </div>
    </div>
    <!-- /.card -->

  </div>
</section>
<?php include 'partials/edit-user-modal.php'; ?>
<!-- Modal (place OUTSIDE card) -->
<div class="modal fade" id="userModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form id="userForm" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/register-process" method="POST">
        <?= \App\Helpers\Csrf::field(); ?>

        <!-- 1. Modal Header -->
        <div class="modal-header">
          <h6 class="modal-title font-weight-bold">
            <i class="fas fa-plus mr-2"></i> አዲስ ተቆጣጣሪ መዝግብ
          </h6>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">

          <div class="row">

            <div class="col-md-6">
              <div class="form-group mb-2">
                <label for="firstname" class="mb-1">
                  <small class="font-weight-bold">ስም</small>
                </label>
                <input type="text" class="form-control form-control-sm"
                       placeholder="ስም ያስገቡ" name="firstname" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group mb-2">
                <label for="fathername" class="mb-1">
                  <small class="font-weight-bold">የአባት ስም</small>
                </label>
                <input type="text" class="form-control form-control-sm"
                       placeholder="የአባት ስም ያስገቡ" name="fathername" required>
              </div>
            </div>

        

            <div class="col-md-6">
              <div class="form-group mb-2">
                <label for="grandfathername" class="mb-1">
                  <small class="font-weight-bold">የአያት ስም</small>
                </label>
                <input type="text" class="form-control form-control-sm"
                       placeholder="የአያት ስም ያስገቡ" name="grandfathername" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group mb-2">
                <label for="gender" class="mb-1">
                  <small class="font-weight-bold">ጾታ</small>
                </label>
                <select class="form-control form-control-sm" id="gender" name="gender" required>
                  <option value="" disabled selected>ይምረጡ</option>
                  <option value="ወንድ">ወንድ</option>
                  <option value="ሴት">ሴት</option>
                </select>
              </div>
            </div>

         

            <div class="col-md-6">
              <div class="form-group mb-2">
                <label for="phone" class="mb-1">
                  <small class="font-weight-bold">ስልክ ቁጥር</small>
                </label>
                <input type="text" class="form-control form-control-sm"
                       placeholder="ስልክ ቁጥር ያስገቡ" name="phone" required>
              </div>
            </div>

            <?php
            $sessionRole  = $_SESSION['user']['role'] ?? null;
            $sessionOrgId = $_SESSION['user']['organization_id'] ?? null;
            ?>

            <!-- Role -->
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label for="roleSelector" class="mb-1">
                  <small class="font-weight-bold">Role</small>
                </label>

                <?php if ($sessionRole === 'system_admin'): ?>

                  <select class="form-control" id="roleSelector" name="role">
                    <option value="admin" selected>Admin</option>
                    <option value="mgmt">Management</option>
                    <option value="officer">ባለሙያ</option>
                  </select>

                <?php elseif ($sessionRole === 'admin'): ?>

                  <select class="form-control" id="roleSelector" name="role" required>
                    <option value="" disabled selected>ይምረጡ</option>
                    <option value="mgmt">Management</option>
                    <option value="officer">ባለሙያ</option>
                  </select>

                <?php endif; ?>

              </div>
            </div>

     
            <!-- Organization -->
            <?php if ($sessionRole === 'system_admin'): ?>

              <div class="col-md-6">
                <div class="form-group mb-2">
                  <label for="orgSelector" class="mb-1">
                    <small class="font-weight-bold">የተቁሙ ስም</small>
                  </label>

                  <select class="form-control" id="orgSelector" name="organization" required>
                    <option value="" disabled selected>ይምረጡ</option>

                    <?php foreach ($organizations as $row): ?>
                      <option value="<?= htmlspecialchars($row['uuid']) ?>">
                        <?= htmlspecialchars($row['name']) ?>
                      </option>
                    <?php endforeach; ?>

                  </select>
                </div>
              </div>

            <?php elseif ($sessionRole === 'admin'): ?>

              <input type="hidden"
                     name="organization"
                     value="<?= htmlspecialchars($sessionOrgId ?? '') ?>">

            <?php endif; ?>


            <div class="col-md-6">
              <div class="form-group mb-2">
                <label for="email" class="mb-1">
                  <small class="font-weight-bold">ኢሜይል</small>
                </label>
                <input type="email" class="form-control form-control-sm"
                       placeholder="ኢሜይል ያስገቡ" name="email">
              </div>
            </div>

          
            <div class="col-md-6">
              <div class="form-group mb-2">
                <label for="password" class="mb-1">
                  <small class="font-weight-bold">Password</small>
                </label>
                <input type="password" class="form-control form-control-sm"
                       placeholder="Password ያስገቡ" name="password" required>
              </div>
            </div>

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
<script nonce="<?= $GLOBALS['nonce'] ?>">
document.addEventListener('submit', function(e) {
    if (e.target.matches('.reset-password-form')) {
        const name = e.target.querySelector('.reset-password-btn').dataset.name;
        if (!confirm(`Reset password for ${name}?`)) {
            e.preventDefault();
        }
    }
});



</script>