<?php
namespace App\Controllers;
use App\Models\User;
use App\Models\FunctionalOrgModel;
use App\Helpers\AuthHelper;
use App\Helpers\Csrf;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;
use Ramsey\Uuid\Uuid;

class FunctionalOrgController extends BaseController
{
    public function bureauIndex()
    {
        AuthHelper::checkRole(['system_admin', 'admin']);

        $userId = $_SESSION['user']['id'] ?? null;
        $currentLang = $_SESSION['lang'] ?? 'am';
        $branchType = ['bureau', 'authority','commission','institution','enterprise','memriya','tsfet_bet'];
        $level = 1;
        $bureaus = [];

        if (!$userId) {
            $_SESSION['error'] = \__('invalid_login');
            header("Location: " . $_ENV['BASE_URL'] . "/login");
            exit();
        }

        $model = new FunctionalOrgModel($this->db);
        $bureaus = $model->getAll($level, $branchType);

        $this->render('register-bureau', [
            'branches' => $bureaus,
            'currentLang' => $currentLang
        ]);
    }

    public function breauRegistration()
    {
        AuthHelper::checkRole(['system_admin', 'admin']);

        $userId = $_SESSION['user']['id'] ?? null;
        $organizationId = $_SESSION['user']['organization_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            Csrf::verifyOrRedirect('login');
            if (!$userId) {
                $_SESSION['error'] = \__('invalid_login');
                header("Location: " . $_ENV['BASE_URL'] . "/login");
                exit();
            }
            // 1. ዳታውን መቀበል
            $branch_name = isset($_POST['branch_name'])
                ? trim($_POST['branch_name'])
                : '';

            $branch_type = isset($_POST['branch_type'])
                ? trim($_POST['branch_type'])
                : '';

            // 2. Validation
            if (empty($branch_name)) {
                $_SESSION['error'] = 'እባክዎ የቢሮውን ስም በትክክል ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/register-bureau");
                exit();
            }

            if (empty($branch_type)) {
                $_SESSION['error'] = 'እባክዎ የቢሮውን ዓይነት ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/register-bureau");
                exit();
            }
// Only allow valid bureau types
    $allowedTypes = ['bureau', 'authority','commission','institution','enterprise','memriya','tsfet_bet'];

    if (!in_array($branch_type, $allowedTypes, true)) {
        $_SESSION['error'] = 'የተመረጠው የቢሮ ዓይነት ትክክል አይደለም!';
        header("Location: " . $_ENV['BASE_URL'] . "/register-bureau");
        exit();
    }


            // 3. UUID ማመንጨት (ለ Organization)
            $uuid = Uuid::uuid7()->toString();

            $data = [
                'uuid' => $uuid,
                'branch_name' => $branch_name,
                'branch_type' => $branch_type,
                'organization_id' => $organizationId,
                'registered_by' => $userId
            ];

            $model = new FunctionalOrgModel($this->db);

            try {

                // 4. ሞዴሉን መጥራት
                // ይህ ድርጅቱን እና Main Officeን በአንድ ላይ ይመዘግባል
                $branchId = $model->create($data);

                if ($branchId) {

                    // Log organization creation
                    \App\Helpers\AuditHelper::log(
                        'bureau_created',
                        'bureau',
                        $branchId,
                        null,
                        [
                            'name' => $branch_name,
                            'type' => $branch_type,
                            'registered_by' => $userId
                        ]
                    );

                    $_SESSION['success'] = \__('operation_sucess');

                    header(
                        "Location: "
                        . $_ENV['BASE_URL']
                        . "/register-bureau"
                    );

                    exit();
                }

            } catch (\Exception $e) {

                // 5. ስህተቶችን መያዝ
                if ($e instanceof \PDOException && $e->getCode() == 23000) {

                    error_log(
                        "Duplicate/constraint error detail: "
                        . print_r($e->errorInfo, true)
                    );

                    $_SESSION['error'] =
                        "ይህ ድርጅት ቀደም ብሎ ተመዝግቧል!";

                } else {

                    error_log(
                        "Registration Error: "
                        . $e->getMessage()
                    );

                    $_SESSION['error'] =
                        \__('operation_failed');
                }

                header(
                    "Location: "
                    . $_ENV['BASE_URL']
                    . "/register-bureau"
                );

                exit();
            }
        }
    }
 public function bureauEdit()
{
    AuthHelper::checkRole(['system_admin', 'admin']);

    header('Content-Type: application/json; charset=utf-8');

    $userId = $_SESSION['user']['id'] ?? null;

    if (!$userId) {
        http_response_code(401);

        echo json_encode([
            'status' => 'error',
            'message' => \__('invalid_login')
        ]);

        exit();
    }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verify CSRF before processing any submitted data
    Csrf::verifyOrRedirect('login');

    // Process POST data here
    $data = $_POST;

} else {

    http_response_code(405);

    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);

    exit();
}

    $uuid = isset($_POST['id'])
        ? trim($_POST['id'])
        : '';

    $name = isset($_POST['branch_name'])
        ? trim($_POST['branch_name'])
        : '';

    $branchType = isset($_POST['branch_type'])
        ? trim($_POST['branch_type'])
        : '';

    if (empty($uuid)) {
        http_response_code(400);

        echo json_encode([
            'status' => 'error',
            'message' => 'የቢሮው መለያ አልተገኘም።'
        ]);

        exit();
    }

    if (empty($name)) {
        http_response_code(400);

        echo json_encode([
            'status' => 'error',
            'message' => 'እባክዎ የቢሮውን ስም ያስገቡ።'
        ]);

        exit();
    }

    if (empty($branchType)) {
        http_response_code(400);

        echo json_encode([
            'status' => 'error',
            'message' => 'እባክዎ የቢሮውን ዓይነት ይምረጡ።'
        ]);

        exit();
    }

    $allowedTypes = ['bureau', 'authority','commission','institution','enterprise','memriya','tsfet_bet'];

    if (!in_array($branchType, $allowedTypes, true)) {
        http_response_code(400);

        echo json_encode([
            'status' => 'error',
            'message' => 'የቢሮው ዓይነት ትክክል አይደለም።'
        ]);

        exit();
    }

    $data = [
        'uuid'        => $uuid,
        'name'        => $name,
        'branch_type' => $branchType,
        'updated_by'  => $userId
    ];

    $model = new FunctionalOrgModel($this->db);

   try {
    $updated = $model->updateBureau($data);

    if ($updated['status'] === 'success') {

        \App\Helpers\AuditHelper::log(
            action:     'bureau_updated',
            entityType: 'bureau',
            entityId:   $updated['id'],
            oldValues:  null,
            newValues:  [
                'name'       => $name,
                'type'       => $branchType,
                'updated_by' => $userId,
            ]
        );

        echo json_encode([
            'status'  => 'success',
            'message' => 'የቢሮው መረጃ በትክክል ተስተካክሏል።'
        ]);
        exit();
    }

    // Distinguish "not found" from "blocked by incompatible children"
    if (!empty($updated['incompatible_child_types'])) {
        http_response_code(409); // conflict — more accurate than 404 here
        echo json_encode([
            'status'  => 'error',
            'message' => $updated['message'],
            'incompatible_child_types' => $updated['incompatible_child_types'],
        ]);
        exit();
    }

    http_response_code(404);
    echo json_encode([
        'status'  => 'error',
        'message' => 'የቢሮው አስተዳደሩ መረጃ አልተገኘም።'
    ]);
    exit();

} catch (\Exception $e) {
    error_log("Update Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።'
    ]);
    exit();
}
}




public function accountableOfficeIndex()
    {
        AuthHelper::checkRole(['system_admin', 'admin']);

$userId = $_SESSION['user']['id'] ?? null;
$currentLang = $_SESSION['lang'] ?? 'am';
$offices = [];
$bureaus = [];
$bureaus_types = ['bureau', 'authority', 'enterprise', 'institution', 'commission'];
$bureauLevel = 1;
$officeLevel = 2; // accountable offices sit one level below their bureau

if (!$userId) {
    $_SESSION['error'] = \__('invalid_login');
    header("Location: " . $_ENV['BASE_URL'] . "/login");
    exit();
}

$bureausmodel = new FunctionalOrgModel($this->db);
$bureaus = $bureausmodel->getAll($bureauLevel, $bureaus_types);

// Pagination for the woreda listing table
$perPage     = 20;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$search      = trim($_GET['search'] ?? '');
$offset      = ($currentPage - 1) * $perPage;

$totalCount = $bureausmodel->countOfficesWithBureau($officeLevel, null, $search ?: null);
$totalPages = (int) max(1, ceil($totalCount / $perPage));

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
    $offset = ($currentPage - 1) * $perPage;
}

$offices = $bureausmodel->getaccountableOfficesWithBureau($officeLevel, null, $search ?: null, $perPage, $offset);
   
        $this->render('accountable-office', [
            'branches' => $offices,
            'parents' => $bureaus,
            'currentLang' => $currentLang,
            'currentPage' => $currentPage,
            'totalPages'  => $totalPages,
            'search'      => $search,
        ]);
    }


 public function accountableOfficeRegistration()
    {
        AuthHelper::checkRole(['system_admin', 'admin']);

        $userId = $_SESSION['user']['id'] ?? null;
        $organizationId = $_SESSION['user']['organization_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            Csrf::verifyOrRedirect('login');
            if (!$userId) {
                $_SESSION['error'] = \__('invalid_login');
                header("Location: " . $_ENV['BASE_URL'] . "/login");
                exit();
            }
            // 1. ዳታውን መቀበል
            $branch_name = isset($_POST['branch_name'])
                ? trim($_POST['branch_name'])
                : '';

            $branch_type = isset($_POST['branch_type'])
                ? trim($_POST['branch_type'])
                : '';

            // 2. Validation
            if (empty($branch_name)) {
                $_SESSION['error'] = 'እባክዎ የመ/ቤቱን ስም በትክክል ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/accountable-office");
                exit();
            }

            if (empty($branch_type)) {
                $_SESSION['error'] = 'እባክዎ የመ/ቤቱን ዓይነት ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/accountable-office");
                exit();
            }
$parent = isset($_POST['parent'])
    ? trim($_POST['parent'])
    : '';

if (empty($parent)) {
    echo json_encode(['status' => 'error', 'message' => 'ዞን መምረጥ አስፈላጊ ነው።']);
    return;
}

$model = new FunctionalOrgModel($this->db); // or whichever model has findById()
$parentBranch = $model->findById($parent);


// 1. Validate the zone/region selection itself
if (!in_array($parentBranch['branch_type'], ['bureau', 'authority', 'enterprise', 'institution', 'commission'], true)) {
    echo json_encode(['status' => 'error', 'message' => 'የተመረጠው ዞን ወይም ከተማ አስተዳደር አይደለም።']);
    return;
}   




            // 3. UUID ማመንጨት (ለ Organization)
            $uuid = Uuid::uuid7()->toString();

            $data = [
                'uuid' => $uuid,
                'branch_name' => $branch_name,
                'branch_type' => $branch_type,
                'organization_id' => $organizationId,
                'functional_parent_id' => $parentBranch['id'],
                'registered_by' => $userId
            ];

            $model = new FunctionalOrgModel($this->db);

            try {

                // 4. ሞዴሉን መጥራት
                // ይህ ድርጅቱን እና Main Officeን በአንድ ላይ ይመዘግባል
                $branchId = $model->createAccountableOffices($data);

                if ($branchId) {

                    // Log organization creation
                    \App\Helpers\AuditHelper::log(
                        'accountableoffice_created',
                        'accountableoffice',
                        $branchId,
                        null,
                        [
                            'name' => $branch_name,
                            'type' => $branch_type,
                            'registered_by' => $userId
                        ]
                    );

                    $_SESSION['success'] = \__('operation_sucess');

                    header(
                        "Location: "
                        . $_ENV['BASE_URL']
                        . "/accountable-office"
                    );

                    exit();
                }

            } catch (\Exception $e) {

                // 5. ስህተቶችን መያዝ
                if ($e instanceof \PDOException && $e->getCode() == 23000) {

                    error_log(
                        "Duplicate/constraint error detail: "
                        . print_r($e->errorInfo, true)
                    );

                    $_SESSION['error'] =
                        "ቀደም ተመዝግቧል!";

                } else {

                    error_log(
                        "Registration Error: "
                        . $e->getMessage()
                    );

                    $_SESSION['error'] =
                        \__('operation_failed');
                }

                header(
                    "Location: "
                    . $_ENV['BASE_URL']
                    . "/accountable-office"
                );

                exit();
            }
        }
    }

 public function accountableOfficeEdit()
    {
        AuthHelper::checkRole(['system_admin', 'admin']);

        $userId = $_SESSION['user']['id'] ?? null;
        $organizationId = $_SESSION['user']['organization_id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            Csrf::verifyOrRedirect('login');
            if (!$userId) {
                $_SESSION['error'] = \__('invalid_login');
                header("Location: " . $_ENV['BASE_URL'] . "/login");
                exit();
            }
            // 1. ዳታውን መቀበል

            $id = isset($_POST['id'])
                ? trim($_POST['id'])
                : '';

            $branch_name = isset($_POST['branch_name'])
                ? trim($_POST['branch_name'])
                : '';

            $branch_type = isset($_POST['branch_type'])
                ? trim($_POST['branch_type'])
                : '';

            // 2. Validation
 
            if (empty($branch_name) || empty($id)) {
                $_SESSION['error'] = 'እባክዎ መ/ቤት ስም/ስም በትክክል ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/accountable-office");
                exit();
            }

            if (empty($branch_type)) {
                $_SESSION['error'] = 'እባክዎ የዞኑን/ከተማ አስተዳደሩን ዓይነት ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/accountable-office");
                exit();
            }
$parent = isset($_POST['parent'])
    ? trim($_POST['parent'])
    : '';

if (empty($parent)) {
    echo json_encode(['status' => 'error', 'message' => 'እናት መ/ቤት መምረጥ አስፈላጊ ነው።']);
    return;
}

$model = new FunctionalOrgModel($this->db); // or whichever model has findById()
$parentBranch = $model->findById($parent);


// 1. Validate the zone/region selection itself
if (!in_array($parentBranch['branch_type'], ['bureau', 'authority','commission','institution','enterprise'], true)) {
    echo json_encode(['status' => 'error', 'message' => 'የተመረጠው እናት መ/ቤት አይደለም።']);
    return;
}


            $data = [
                'uuid' => $id,
                'name' => $branch_name,
                'branch_type' => $branch_type,
                'functional_parent_id' => $parentBranch['id'],
                'updated_by' => $userId
            ];

            $model = new FunctionalOrgModel($this->db);

            try {

                // 4. ሞዴሉን መጥራት
                // ይህ ድርጅቱን እና Main Officeን በአንድ ላይ ይመዘግባል
                $branchId = $model->updateAccountableOffice($data);

                if ($branchId) {

                    // Log organization creation
                    \App\Helpers\AuditHelper::log(
                        'woreda_updated',
                        'woreda',
                        $branchId,
                        null,
                        [
                            'name' => $branch_name,
                            'type' => $branch_type,
                            'updated_by' => $userId
                        ]
                    );

                    $_SESSION['success'] = \__('operation_sucess');

                    header(
                        "Location: "
                        . $_ENV['BASE_URL']
                        . "/accountable-office"
                    );

                    exit();
                }

            } catch (\Exception $e) {

                // 5. ስህተቶችን መያዝ
                if ($e instanceof \PDOException && $e->getCode() == 23000) {

                    error_log(
                        "Duplicate/constraint error detail: "
                        . print_r($e->errorInfo, true)
                    );

                    $_SESSION['error'] =
                        "ቀደም ተመዝግቧል!";

                } else {

                    error_log(
                        "Registration Error: "
                        . $e->getMessage()
                    );

                    $_SESSION['error'] =
                        \__('operation_failed');
                }

                header(
                    "Location: "
                    . $_ENV['BASE_URL']
                    . "/accountable-office"
                );

                exit();
            }
        }
    }



public function delete(): void
{
    AuthHelper::checkRole(['system_admin', 'admin']);
    header('Content-Type: application/json');

    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (string) ($data['id']   ?? '');
    $type   = (string) ($data['type'] ?? '');
    $userId = $_SESSION['user']['id'] ?? '';
    $userRole = $_SESSION['user']['role'] ?? '';
    $organizationId = $_SESSION['user']['organization_id'] ?? null;

    $reason   = trim($data['reason'] ?? '');
    $source   = 'INDIVIDUAL';
    $password = $data['confirm_password'] ?? '';

    // 1. Basic input validation
    if (!$id || !$reason || !$password) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ሁሉም መስኮች አስፈላጊ ናቸው።'
        ]);
        return;
    }

    if (empty($userId)) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        return;
    }

   // 2. Authorization: system_admin (any org), OR admin scoped to their own organization
$isSystemAdmin = $userRole === 'system_admin';
$isOrgAdmin    = $userRole === 'admin' && !empty($organizationId);

if (!$isSystemAdmin && !$isOrgAdmin) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'ያልተፈቀደ ድርጊት።'
    ]);
    return;
}
    // 3. Verify password
    $userModel = new User($this->db);
    if (!$userModel->verifyPassword($userId, $password)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'ፓስዋርዱ ትክክል አይደለም።'
        ]);
        return;
    }

    try {
        $model   = new FunctionalOrgModel($this->db);
        $action  = 'branch_deleted';
        $metaKey = 'affected_branches';

        $result = $model->softDelete($id, $userId, $reason, $source);

        $branchCount = $result['branchCount'] ?? 0;
        $userCount   = $result['userCount'] ?? 0;
        $oldRecord   = $result['oldRecord'] ?? null;


        \App\Helpers\AuditHelper::log(
            action:     $action,
            entityType: $type ?: 'branches',
            entityId:   $result['id'],
            oldValues:  $oldRecord,
            newValues:  ['status' => 'inactive'],
            metadata:   [
                $metaKey          => $branchCount,
                'affected_users'  => $userCount,
                'deletion_source' => $source,
            ]
        );

        echo json_encode([
            'status'          => 'success',
            'affected_branches' => $branchCount,
            'affected_users'    => $userCount,
        ]);

    } catch (\InvalidArgumentException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } catch (\Exception $e) {
        error_log("Delete Error ({$type}): " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።']);
    }
}
}