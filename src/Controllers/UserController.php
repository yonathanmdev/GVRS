<?php
namespace App\Controllers;
use App\Models\Organization;
use App\Models\Branch;
use App\Models\User;
use App\Helpers\AuthHelper;
use App\Helpers\Csrf;
use Ramsey\Uuid\Uuid;

// 1. BaseControllerን እንዲወርስ እናደርጋለን
class UserController extends BaseController {
 public function showRegisterForm() {
    AuthHelper::checkRole(['system_admin', 'admin']);

    $organizationId = $_SESSION['user']['organization_id'] ?? null;
    $id         = $_SESSION['user']['id'] ?? null;
    $uuid         = $_SESSION['user']['uuid'] ?? null;
    $role       = $_SESSION['user']['role'];
     $currentLang = $_SESSION['lang'] ?? 'am';
    $userModel   = new User($this->db);
    // --- pagination + search params ---
    $page    = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 50;
    $offset  = ($page - 1) * $perPage;
    $search  = trim($_GET['search'] ?? '');

    $users         = [];
    $organizations = [];
    $totalUsers    = 0;

  if ($role === 'system_admin') {
    $filters = array_filter(['search' => $search]);

    $totalUsers    = $userModel->countAll($filters);
    $users         = $userModel->getAll($filters, $perPage, $offset);
    $organizations = (new Organization($this->db))->getAll();
}elseif ($role === 'admin') {
    $filters = array_filter([
        'search'          => $search,
        'organization_id' => $_SESSION['user']['organization_id'] ?? null,
    ]);

    $totalUsers = $userModel->countAll($filters);
    $users      = $userModel->getAll($filters, $perPage, $offset);
}

    $this->render('register-user', [
        'organizations' => $organizations,
        'users'         => $users,
        'currentPage'   => $page,
        'perPage'       => $perPage,
        'totalUsers'    => $totalUsers,
        'totalPages'    => (int)ceil($totalUsers / $perPage),
        'search'        => $search,
        'currentLang'   => $currentLang
    ]);
}

   public function handleRegistration() {
    AuthHelper::checkRole(['system_admin', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        Csrf::verifyOrRedirect('login');
        // 1. Receive inputs
        $firstName       = isset($_POST['firstname'])       ? trim($_POST['firstname'])       : '';
        $fatherName      = isset($_POST['fathername'])      ? trim($_POST['fathername'])      : '';
        $gFatherName     = isset($_POST['grandfathername']) ? trim($_POST['grandfathername']) : '';
        $gender          = isset($_POST['gender'])          ? trim($_POST['gender'])          : '';
        $email           = isset($_POST['email'])           ? trim($_POST['email'])           : '';
        $password        = $_POST['password'] ?? '';
        $txtpassword        = $_POST['password'] ?? '';
        $phone           = isset($_POST['phone'])           ? trim($_POST['phone'])           : '';       $full_name = $firstName . ' ' . $fatherName . ' ' . $gFatherName;
       $role = isset($_POST['role']) ? trim($_POST['role']) : '';
$actingRole = $_SESSION['user']['role'] ?? null;

if ($actingRole === 'admin') {
    // Already the internal id — no lookup needed, and never trust POST for this
    $org_id = $_SESSION['user']['organization_id'] ?? null;

    if (empty($org_id)) {
        $_SESSION['error'] = "የድርጅት መረጃ አልተገኘም፤ እባክዎ እንደገና ይግቡ።";
        header("Location: " . $_ENV['BASE_URL'] . "/register-user");
        exit();
    }

} else {
    // system_admin: org uuid comes from the dropdown, resolve to internal id
    $orgSelector = isset($_POST['organization']) ? trim($_POST['organization']) : null;

    if (empty($orgSelector)) {
        $_SESSION['error'] = "እባክዎ ድርጅት ይምረጡ!";
        header("Location: " . $_ENV['BASE_URL'] . "/register-user");
        exit();
    }

    $orgModel     = new Organization($this->db);
    $organization = $orgModel->findById($orgSelector); // uuid lookup, not findById

    if (!$organization) {
        $_SESSION['error'] = "የተመረጠው ድርጅት አልተገኘም!";
        header("Location: " . $_ENV['BASE_URL'] . "/register-user");
        exit();
    }

    $org_id = $organization['id'];
}
       
                $allowedRoles = ['admin', 'mgmt', 'officer'];
            if (!in_array($role, $allowedRoles)) {
                $_SESSION['error'] = "የተፈቀደ Role አልተመረጠም!";
                header("Location: " . $_ENV['BASE_URL'] . "/register-user");
                exit();
            }

        

        $registeredBy = $_SESSION['user']['id'] ?? null;

        // 2. Basic validation
        if (empty($firstName) || empty($password) || empty($role)
            || empty($fatherName) || empty($gFatherName) || empty($phone) || empty($gender)) {
            $_SESSION['error'] = "እባክዎ ሁሉንም አስፈላጊ መረጃዎች በትክክል ያስገቡ!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-user");
            exit();
        }
      if (!empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "እባክዎ ትክክለኛ ኢሜይል ያስገቡ!";
        header("Location: " . $_ENV['BASE_URL'] . "/register-user");
        exit();
    }
}
$allwedGender = ['ወንድ', 'ሴት'];
if (!in_array($gender, $allwedGender)) {
    $_SESSION['error'] = "እባክዎ ጽታ በትክክል ያስገቡ!";
    header("Location: " . $_ENV['BASE_URL'] . "/register-user");
    exit();
}

        // 3. UUID and password hash
        $uuid           = Uuid::uuid7()->toString();
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $data = [
            'uuid'           => $uuid,
            'org_id'   => $org_id,
            'firstName'      => $firstName,
            'fatherName'     => $fatherName,
            'gFatherName'    => $gFatherName,
            'gender'         => $gender,
            'phone'          => $phone,
            'email'          => $email,
            'password'       => $hashedPassword,
            'txtpassword'    => $txtpassword,
            'role'           => $role,
            'registeredBy'   => $registeredBy
        ];
        $userModel = new User($this->db);

        try {
            $result = $userModel->create($data);

if ($result !== false) {
    $userId   = $result['id'];
    $userName = $result['username'];

    $phoneNumber = '251' . ltrim(trim($phone), '0');
    $message = "Dear {$full_name}, Your username is {$userName},"
             . "and your temporary password is:- {$txtpassword}. "
             . "Please log in and change your password.";

    \App\Helpers\SmsHelper::send($phoneNumber, $message);

    \App\Helpers\AuditHelper::log(
        action: 'user_created',
        entityType: 'user',
        entityId: $userId,     // internal id, not uuid — matches your AuditHelper convention
        oldValues: null,
        newValues: [
            'org_id'            => $org_id,
            'first_name'        => $firstName,
            'father_name'       => $fatherName,
            'grand_father_name' => $gFatherName,
            'phone'             => $phone,
            'email'             => $email,
            'gender'            => $gender,
            'role'              => $role,
            'registered_by'     => $registeredBy,
        ],
        metadata: ['uuid' => $uuid]  // keep uuid available in metadata if you still want it traceable
    );

    $_SESSION['success'] = "ተጠቃሚው በተሳካ ሁኔታ ተመዝግቧል!";
    header("Location: " . $_ENV['BASE_URL'] . "/register-user");
    exit();
} else {
    $_SESSION['error'] = "ምዝገባው አልተሳካም፤ እባክዎ እንደገና ይሞክሩ።";
    header("Location: " . $_ENV['BASE_URL'] . "/register-user");
    exit();
}

        } catch (\PDOException $e) {
    if ($e->getCode() === '23000') {
        if (str_contains($e->getMessage(), 'email')) {
            $_SESSION['error'] = "ይህ ኢሜይል ቀደም ብሎ ተመዝግቧል!";
        } elseif (str_contains($e->getMessage(), 'phone')) {
            $_SESSION['error'] = "ይህ ስልክ ቁጥር ቀደም ብሎ ተመዝግቧል!";
        } else {
            error_log("Registration constraint violation: " . $e->getMessage());
            $_SESSION['error'] = "የተባዛ መረጃ ተገኝቷል፤ እባክዎ መረጃውን ያረጋግጡ።";
        }
    } else {
        error_log("Registration Error: " . $e->getMessage());
        $_SESSION['error'] = "የቴክኒክ ስህተት አጋጥሟል፤ እባክዎ ቆይተው ይሞክሩ።";
    }
    header("Location: " . $_ENV['BASE_URL'] . "/register-user");
    exit();
}
    }

   }
public function getUserById()
{
     AuthHelper::checkRole(['system_admin', 'admin']);
    header('Content-Type: application/json');

    $id = $_GET['id'] ?? null;

    if (!$id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid ID'
        ]);
        return;
    }

    $userModel = new User($this->db);
    $user = $userModel->findById($id);

    if ($user) {
        echo json_encode([
            'status' => 'success',
            'data' => $user
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not found'
        ]);
    }
}
public function handleUpdateUser()
{
    AuthHelper::checkRole(['system_admin', 'admin']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . $_ENV['BASE_URL'] . "/register-user");
        exit();
    }
    Csrf::verifyOrRedirect('login');

    try {
        // 1. መረጃዎችን መቀበል
        $id = $_POST['id'] ?? null; // በ Form ውስጥ <input type="hidden" name="id"> መኖሩን አረጋግጥ
        
        $data = [
            'first_name'        => trim($_POST['edit_firstname']),
            'father_name'       => trim($_POST['edit_fathername']),
            'grand_father_name' => trim($_POST['edit_grandfathername']),
            'phone'             => trim($_POST['edit_phone']),
            'email'             => trim($_POST['edit_email']),
            'gender'            => trim($_POST['edit_gender'])
        ];

        // 2. Validation (መሰረታዊ ማረጋገጫ)
        if (empty($id) || in_array("", $data)) {
            $_SESSION['error'] = "እባክዎ ሁሉንም አስፈላጊ መረጃዎች በትክክል ያስገቡ!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-user");
            exit();
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "ትክክለኛ ኢሜይል ያስገቡ!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-user");
            exit();
        }
$allwedGender = ['ወንድ', 'ሴት'];
if (!in_array($data['gender'], $allwedGender)) {
    $_SESSION['error'] = "እባክዎ ጽታ በትክክል ያስገቡ!";
    header("Location: " . $_ENV['BASE_URL'] . "/register-user");
    exit();
}
        // 3. Update ለማድረግ መሞከር
        $userModel = new User($this->db);
        
        // Get old data for logging
        $oldData = $userModel->findById($id);
       if (!$oldData) {
    $_SESSION['error'] = "ተጠቃሚው አልተገኘም!";
    header("Location: " . $_ENV['BASE_URL'] . "/register-user");
    exit();
}
        $currentUuid = $_SESSION['user']['uuid'] ?? null;
        $currentRole = $_SESSION['user']['role'] ?? null;

        $isSelf     = ($oldData['uuid'] === $currentUuid);
        $isSameRole = ($oldData['role'] === $currentRole);

        if (!$isSelf && $isSameRole) {
            $_SESSION['error'] = "ተመሳሳይ Role ያላቸውን ተጠቃሚዎች ማስተካከል አይችሉም!";
            header("Location: " . $_ENV['BASE_URL'] . "/register-user");
            exit();
        }
        $intid = $oldData['id'];
        $isUpdated = $userModel->updateUser($id, $data);

        if ($isUpdated) {
            // Log the user update
            \App\Helpers\AuditHelper::log('user_updated', 'user', $intid, $oldData, $data);
            
            $_SESSION['success'] = "መረጃው በተሳካ ሁኔታ ተቀይሯል!";
        } else {
            // እዚህ ጋር ዳታቤዙ ላይ ምንም ለውጥ ካልተደረገ (ለምሳሌ መረጃው ያው ከሆነ)
            $_SESSION['info'] = "ምንም የተቀየረ አዲስ መረጃ የለም።";
        }

    } catch (\Exception $e) {
        error_log("Update Error: " . $e->getMessage());
        $_SESSION['error'] = "የቴክኒክ ስህተት ተፈጥሯል።";
    }

    header("Location: " . $_ENV['BASE_URL'] . "/register-user");
    exit();
}

public function resetPassword(): void
{
    // Must be POST, not GET — this is a state-changing action
    AuthHelper::checkRole(['system_admin', 'admin']);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method not allowed');
    }
  Csrf::verifyOrRedirect('login');
    $userId = filter_input(INPUT_POST, 'id');
    if (!$userId) {
        $_SESSION['error'] = 'Invalid User ID.';
        header('Location: ' . $_ENV['BASE_URL'] . '/register-user');
        exit();
    }

    $userModel = new User($this->db);
    $selectedUser = $userModel->findById($userId);
$currentUuid = $_SESSION['user']['uuid'] ?? null;
    $currentRole = $_SESSION['user']['role'] ?? null;

    $isSelf     = ($selectedUser['uuid'] === $currentUuid);
    $isSameRole = ($selectedUser['role'] === $currentRole);

    if ($isSelf) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'የራስዎን መለያ መሰረዝ አይችሉም!']);
        exit();
    }

    if ($isSameRole) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'ተመሳሳይ Role ያላቸውን ተጠቃሚዎች መሰረዝ አይችሉም!']);
        exit();
    }
    if (!$selectedUser) {
        $_SESSION['error'] = 'User not found.';
        header('Location: ' . $_ENV['BASE_URL'] . '/register-user');
        exit();
    }

    if ($selectedUser['is_active'] === '2') {
        $_SESSION['error'] = 'User status is locked, cannot reset password.';
        header('Location: ' . $_ENV['BASE_URL'] . '/register-user');
        exit();
    }

    $newPassword = (string) random_int(100000, 999999);
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $success = $userModel->resetPassword($userId, $hashedPassword, $newPassword);

    if (!$success) {
        $_SESSION['error'] = 'Error updating record.';
        header('Location: ' . $_ENV['BASE_URL'] . '/register-user');
        exit();
    }

if (!empty($selectedUser['phone'])) {
    $phoneNumber = '251' . ltrim(trim($selectedUser['phone']), '0');
    $message = "User {$selectedUser['username']}, Your password has been reset. "
             . "Your new temporary password is:- {$newPassword}. "
             . "Please log in and change your password.";

    \App\Helpers\SmsHelper::send($phoneNumber, $message);
} else {
    error_log("Reset-password: no phone on file for user {$selectedUser['uuid']} — SMS not sent.");
}
    // Email sending — same pattern, via its own helper
    // \App\Helpers\EmailHelper::send($user['uemail'], $message);

    \App\Helpers\AuditHelper::log(
        action: 'password_reset_by_admin',
        entityType: 'user',
        entityId: (string) $userId,
        oldValues: null,
        newValues: null,
        metadata: ['reset_by' => $_SESSION['user']['id'] ?? null]
    );

    $_SESSION['success'] = 'Password reset successfully and default password sent via SMS.';
    header('Location: ' . $_ENV['BASE_URL'] . '/register-user');
    exit();
}

public function delete(): void
{
    AuthHelper::checkRole(['system_admin', 'admin']);
    header('Content-Type: application/json');
    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (string) ($data['id']   ?? '');
    $adminId = $_SESSION['user']['id'] ?? '';
    $type = 'user'; // ለ Audit Log
   
    $reason = trim($data['reason']      ?? '');
        $source = 'INDIVIDUAL';
        $password = $data['confirm_password'] ?? '';

        // Validate input
        if (!$id || !$reason || !$password || !$source) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ሁሉም መስኮች አስፈላጊ ናቸው።'
            ]);
            return;
        }

        $user           = $_SESSION['user'] ?? [];
        $organizationId = $user['organization_id'] ?? null;
        $branchId = $user['branch_id'] ?? null;

       

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        return;
    }

    if (empty($adminId)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }
 // Verify password
        $userModel = new User($this->db);
        if (!$userModel->verifyPassword($user['id'], $password)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ፓስዋርዱ ትክክል አይደለም።'
            ]);
            return;
        }

    try {
            $model = new User($this->db);
            $action = 'user_deleted';
            $metaKey = 'affected_records'; // ለድርጅት ቅርንጫፎች ይባላሉ
        $result = $model->softDelete($id, $adminId, $reason, $source);

        if ($result['status'] === 'success') {
   

    $metadata = [
        $metaKey          =>$result['branchCount'] ?? 0,
        'affected_users'  => $result['userCount'] ?? 0,
        'deletion_source' => $source
    ];

    \App\Helpers\AuditHelper::log(
        action:     $action,
        entityType: $type,
        entityId:   $id,
        newValues:  ['status' => 'inactive'],
        metadata:   $metadata
    );

    unset($result['branchCount'], $result['userCount']);
}

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Delete Error ({$type}): " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።']);
    }
}
public function showDeletedLists() {
     AuthHelper::checkRole(['system_admin', 'admin']);

        $myBranchId = $_SESSION['user']['branch_id'] ?? null;
        $userModel = (new User($this->db));
            $users = $userModel->findAllDeleted($myBranchId);
        $this->render('deleted-users', [
        'title' => 'የተሰረዙ ተቆጣጣሪዎች',
        'users' => $users
    ]);
   
}
 public function restore(): void
{
    AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        return;
    }

    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (string) ($data['id']   ?? '');
    $userId = (string) ($_SESSION['user']['id'] ?? '');

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        return;
    }

    try {
   
            // org_admin ወይም system_admin ቅርንጫፍ ሲመልሱ
            $model = new User($this->db);
            $action = 'user_restored';
            $metaKey = 'restored_user';
        

        $result = $model->restore($id, $userId);

        if ($result['status'] === 'success') {
            // 2. ኦዲት ሎግ መመዝገብ
            \App\Helpers\AuditHelper::log(
                action:     $action,
                entityType: 'user',
                entityId:   $id,
                newValues:  ['status' => 'active'],
                metadata:   [
                    $metaKey => 1, 'restored_users'  => 1,
                    'restore_type'    => 'individual_restore'
                ]
            );

           
            }

        echo json_encode($result);

    } catch (\Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'ስህተት፡ ' . $e->getMessage()]);
    }
}

    // ============================================================
    // PURGE (permanent delete)
    // ============================================================
  public function purge(): void
{
    AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    $data     = json_decode(file_get_contents('php://input'), true);
    $id       = (string) ($data['id']               ?? '');
    $adminId  = (string) ($_SESSION['user']['id']   ?? '');
    $password = (string) ($data['confirm_password'] ?? '');

    if (empty($id) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'መለያ ወይም ሚስጥራዊ ቁጥር አልገባም']);
        return;
    }

    // ============================================================
    // 1. Verify admin password
    // ============================================================
    $userModel = new User($this->db);
    if (!$userModel->verifyPassword($adminId, $password)) {
        echo json_encode(['status' => 'error', 'message' => 'የእርስዎ ሚስጥራዊ ቁጥር (Password) ትክክል አይደለም።']);
        return;
    }

    try {
        // ============================================================
        // 2. Pick model based on role
        // ============================================================
        $oldRecord = null;
        $archiveId = Uuid::uuid7()->toString();
       
            $model      = new User($this->db);
            $action     = 'user_purged';
            $entityType = 'user';
            $metaKey    = 'purged_users';
            $oldRecord  = $model->findById($id);

        if (!$oldRecord) {
            echo json_encode(['status' => 'error', 'message' => 'መረጃው አልተገኘም።']);
            return;
        }

        // ============================================================
        // 3. Purge — model handles archive + hard delete internally
        // ============================================================
        $result = $model->purge($id, $archiveId);

        // ============================================================
        // 4. Audit log — includes archiveId so you can trace back
        // ============================================================
        if ($result['status'] === 'success') {
            \App\Helpers\AuditHelper::log(
                action:     $action,
                entityType: $entityType,
                entityId:   $id,
                oldValues:  $oldRecord,
                newValues:  null,
                metadata:   [
                    $metaKey          => 1,
                    'purged_users'    => 1,
                    'archive_id'      => $result['archiveId']   ?? null, // ← trace to archive
                    'deletion_type'   => 'permanent_purge',
                    'confirmed_by'    => $adminId
                ]
            );

            unset(
                $result['oldRecord'],
                $result['archiveId'],   // ← don't expose to frontend
                $result['userCount']
            );
        }

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Purge Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'መሰረዝ አልተቻለም።']);
    }
}
}