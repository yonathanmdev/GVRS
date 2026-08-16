<?php
namespace App\Controllers;
use App\Models\Organization;
use App\Models\Branch;
use App\Models\User;
use App\Helpers\AuthHelper;
use App\Helpers\Csrf;
use Ramsey\Uuid\Uuid;
// 1. BaseControllerን እንዲወርስ እናደርጋለን
class OrgController extends BaseController {
   public function showRegisterForm() {
     AuthHelper::checkRole(['system_admin']);
     $currentLang = $_SESSION['lang'] ?? 'am';
    $organizations =[];
    if ($_SESSION['user']['role'] === 'system_admin') {
        $organizations = (new Organization($this->db))->getAll();
        $this->render('register-organization', [
        'organizations' => $organizations,
        'currentLang' => $currentLang
        
    ]);
    }
   }

    public function handleRegistration() {
        AuthHelper::checkRole(['system_admin']);
        $registeredBy = $_SESSION['user']['id'] ?? null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       Csrf::verifyOrRedirect('login');
        // 1. ዳታውን መቀበል
        $orgName = isset($_POST['org_name']) ? trim($_POST['org_name']) : '';
        // 2. Validation
        if (empty($orgName)) {
            $_SESSION['error'] =  \__('orgname_required');
            header("Location: " . $_ENV['BASE_URL'] . "/register-organization");
            exit();
        }

        if (!$registeredBy) {
            $_SESSION['error'] = \__('invalid_login');
            header("Location: " . $_ENV['BASE_URL'] . "/login");
            exit();
        }

        // 3. UUID ማመንጨት (ለ Organization)
        $uuid = Uuid::uuid7()->toString();
        $data =['uuid' => $uuid,
                'org_name' => $orgName,
                'registered_by' => $registeredBy
                ];
        $orgModel = new Organization($this->db);

        try {
            // 4. ሞዴሉን መጥራት (ይህ ድርጅቱን እና Main Officeን በአንድ ላይ ይመዘግባል)
            $orgId = $orgModel->create($data);

            if ($orgId) {
                // Log organization creation
                \App\Helpers\AuditHelper::log('organization_created', 'organization', $orgId, null, [
                    'name' => $orgName,
                    'registered_by' => $registeredBy
                ]);

                $_SESSION['success'] = \__('operation_sucess');
                header("Location: " . $_ENV['BASE_URL'] . "/register-organization");
                exit();
            }
            
        } catch (\Exception $e) {
            // 5. ስህተቶችን መያዝ
            if ($e instanceof \PDOException && $e->getCode() == 23000) {
    error_log("Duplicate/constraint error detail: " . print_r($e->errorInfo, true));
    $_SESSION['error'] = "ይህ ድርጅት ቀደም ብሎ ተመዝግቧል!";
} else {
    error_log("Registration Error: " . $e->getMessage());
    $_SESSION['error'] = \__('operation_failed');
}
            header("Location: " . $_ENV['BASE_URL'] . "/register-organization");
            exit();
        }
    }
}
    
    
public function handleEditOrganization() {
    AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        Csrf::verifyOrRedirect('login');
        $orgId            = isset($_POST['id'])                 ? trim($_POST['id'])                 : '';
        $orgName          = isset($_POST['org_name'])           ? trim($_POST['org_name'])           : '';
        $userId = $_SESSION['user']['id'] ?? null;
        if(!$userId) {
           echo json_encode(['status' => 'error', 'message' => \__('invalid_login')]);
            exit(); 
        }
        if (empty($orgId) || empty($orgName)) {
            echo json_encode(['status' => 'error', 'message' => 'እባክዎ የተቋሙን መለያ እና ስም በትክክል ያስገቡ!']);
            exit();
        }
        $orgModel = new Organization($this->db);

        try {
            $oldData = $orgModel->findById($orgId);

            if (!$oldData) {
                echo json_encode(['status' => 'error', 'message' => 'ማስተካከያው አልተሳካም።']);
                exit();
            }
            $intorgId = $oldData['id'];
            $data = [
                'uuid' => $orgId,
                'org_name' => $orgName,
                'updated_by' => $userId
            ];
            $result = $orgModel->updateOrganization($data);

            if ($result) {
                \App\Helpers\AuditHelper::log(
                    'organization_updated', 'organization', $intorgId,
                    $oldData,
                    ['name' => $orgName]
                );
                echo json_encode(['status' => 'success', 'message' => 'ተቋሙ በተሳካ ሁኔታ ተሻሽሏል!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ማስተካከያው አልተሳካም፤ ምንም የተቀየረ መረጃ የለም።']);
            }
            exit();

        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                echo json_encode(['status' => 'error', 'message' => 'ይህ ድርጅት ቀደም ብሎ ተመዝግቧል!']);
            } else {
                error_log("Org Update Error: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'የዳታቤዝ ስህተት አጋጥሟል!']);
            }
            exit();
        }
    }
}

 // Handle delete — returns JSON
public function delete(): void
{
    AuthHelper::checkRole(['system_admin', 'org_admin']);
    header('Content-Type: application/json');

    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (string) ($data['id']   ?? '');
    $type   = (string) ($data['type'] ?? 'org'); 
    $adminId = $_SESSION['user']['id'] ?? '';

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

        if (!$organizationId || !$branchId) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'ያልተፈቀደ ድርጊት።'
            ]);
            return;
        }

       

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
        $model = null;
        if($_SESSION['user']['role'] === 'system_admin') { 
           $model = new Organization($this->db);
            $action = 'organization_deleted';
            $metaKey = 'affected_branches'; // ለድርጅት ቅርንጫፎች ይባላሉ
        }

        $result = $model->softDelete($id, $adminId, $reason, $source);

        if ($result['status'] === 'success') {
    // መጀመሪያ መረጃዎቹን ከሪሰልት እናውጣ
    $branchCount = $result['branchCount'] ?? 0;
    $userCount   = $result['userCount'] ?? 0;


    $metadata = [
        $metaKey          => $branchCount,
        'affected_users'  => $userCount,
        'deletion_source' => $source
    ];

    \App\Helpers\AuditHelper::log(
        action:     $action,
        entityType: $type,
        entityId:   $id,
        oldValues:  $result['oldRecord'],
        newValues:  ['status' => 'inactive'],
        metadata:   $metadata
    );

    unset($result['oldRecord'], $result['branchCount'], $result['userCount']);
}

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Delete Error ({$type}): " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።']);
    }
}
public function showDeletedLists() {
     AuthHelper::checkRole(['system_admin', 'org_admin']);
    $deletedOrgs =[];
    if ($_SESSION['user']['role'] === 'system_admin') {
        $deletedOrgs = (new Organization($this->db))->findAllDeleted();
        $this->render('organization-deleted-lists', [
        'title' => 'የተሰረዙ ድርጅቶች',
        'deletedOrgs' => $deletedOrgs
    ]);
    }
  
}

 // ============================================================
    // RESTORE
    // ============================================================
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
    $type   = (string) ($data['type'] ?? 'branch'); // 'org' ወይም 'branch' መሆኑን ከ JS እንቀበላለን
    $userId = (string) ($_SESSION['user']['id'] ?? '');

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        return;
    }

    try {
        // 1. በ 'type' ላይ ተመስርቶ ሞዴሉን መምረጥ
        if ($_SESSION['user']['role'] === 'system_admin') {
            $model = new \App\Models\Organization($this->db);
            $action = 'organization_restored';
            $metaBranchesKey = 'restored_branches';
        } 

        $result = $model->restore($id, $userId);

        if ($result['status'] === 'success') {
            // 2. ኦዲት ሎግ መመዝገብ
            \App\Helpers\AuditHelper::log(
                action:     $action,
                entityType: $type,
                entityId:   $id,
                oldValues:  $result['oldRecord'],
                newValues:  ['status' => 'active'],
                metadata:   [
                    $metaBranchesKey => $result['restoredBranches'] ?? $result['restoredSubBranches'] ?? 0,
                    'restored_users'  => $result['restoredUsers'] ?? 0,
                    'restore_type'    => 'cascade_restore'
                ]
            );

            // ለተጠቃሚው የማይፈለጉ መረጃዎችን እናጥፋ
            unset($result['oldRecord'], $result['restoredBranches'], $result['restoredSubBranches'], $result['restoredUsers']);
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
    $type     = (string) ($data['type']             ?? 'branch');
    $adminId  = (string) ($_SESSION['user']['id']   ?? '');
    $password = (string) ($data['confirm_password'] ?? '');

    if (empty($id) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'መለያ ወይም ሚስጥራዊ ቁጥር አልገባም']);
        return;
    }

    // ============================================================
    // 1. Verify admin password
    // ============================================================
    $userModel = new \App\Models\User($this->db);
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
        if ($_SESSION['user']['role'] === 'system_admin') {
            $model      = new Organization($this->db);
            $action     = 'organization_purged';
            $entityType = 'organization';
            $metaKey    = 'purged_branches';
            $oldRecord  = $model->findById($id);
        } 
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
                    $metaKey          => $result['branchCount'] ?? 0,
                    'purged_users'    => $result['userCount']   ?? 0,
                    'archive_id'      => $result['archiveId']   ?? null, // ← trace to archive
                    'deletion_type'   => 'permanent_purge',
                    'confirmed_by'    => $adminId
                ]
            );

            unset(
                $result['oldRecord'],
                $result['archiveId'],   // ← don't expose to frontend
                $result['branchCount'],
                $result['userCount']
            );
        }

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Purge Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'መሰረዝ አልተቻለም።']);
    }
}
public function archiveList(): void
{
    AuthHelper::checkRole(['system_admin']);
    $model      = new Organization($this->db);
    $archivedOrgs = $model->findAllArchived();
    $this->render('archived-organizations', [
        'title' => 'Archived Organizations',
        'archivedOrgs' => $archivedOrgs
    ]);
    
}

// ============================================================
// RESTORE FROM ARCHIVE — system_admin only
// ============================================================
public function restoreFromArchive(): void
{
    AuthHelper::checkRole(['system_admin']);
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        return;
    }

    $data       = json_decode(file_get_contents('php://input'), true);
    $originalId = (string) ($data['original_id']      ?? '');
    $adminId    = (string) ($_SESSION['user']['id']    ?? '');
    $password   = (string) ($data['confirm_password']  ?? '');
    $type       = (string) ($data['type']              ?? 'org'); // 'org' or 'branch'

    if (empty($originalId) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'መለያ ወይም ሚስጥራዊ ቁጥር አልገባም']);
        return;
    }

    // ============================================================
    // 1. Verify admin password
    // ============================================================
    $userModel = new \App\Models\User($this->db);
    if (!$userModel->verifyPassword($adminId, $password)) {
        echo json_encode(['status' => 'error', 'message' => 'የእርስዎ ሚስጥራዊ ቁጥር ትክክል አይደለም።']);
        return;
    }

    // ============================================================
    // 2. Pick model + audit metadata based on type
    // ============================================================
    if ($type === 'org') {
        $model      = new Organization($this->db);
        $action     = 'organization_restored_from_archive';
        $entityType = 'organization';
        $nameKey    = 'orgName';      // ← key returned from model
    } 

    // ============================================================
    // 3. Restore — adminId passed, model never touches session
    // ============================================================
    $result = $model->restoreFromArchive($originalId, $adminId);

    // ============================================================
    // 4. Audit log
    // ============================================================
    if ($result['status'] === 'success') {
        \App\Helpers\AuditHelper::log(
            action:     $action,
            entityType: $entityType,
            entityId:   $originalId,
            oldValues:  null,
            newValues:  null,
            metadata:   [
                'archive_id'        => $result['archiveId']        ?? null,
                'name'              => $result[$nameKey]           ?? null,
                'restored_branches' => $result['branchCount']      ?? 0,
                'restored_users'    => $result['userCount']        ?? 0,
                'restored_by'       => $adminId
            ]
        );

        unset(
            $result['archiveId'],
            $result[$nameKey],
            $result['branchCount'],
            $result['userCount']
        );
    }

    echo json_encode($result);
}
}
