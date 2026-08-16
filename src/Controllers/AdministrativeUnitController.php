<?php

namespace App\Controllers;
use App\Models\User;
use App\Models\AdministrativeUnitModel;
use App\Models\FunctionalOrgModel;
use App\Helpers\AuthHelper;
use App\Helpers\Csrf;
use Ramsey\Uuid\Uuid;

class AdministrativeUnitController extends BaseController
{
    public function zoneIndex()
    {
        AuthHelper::checkRole(['system_admin', 'admin']);

        $userId = $_SESSION['user']['id'] ?? null;
        $currentLang = $_SESSION['lang'] ?? 'am';
        $branchType = ['zone','regio'];
        $zonelevel = 1;
        $zones = [];

        if (!$userId) {
            $_SESSION['error'] = \__('invalid_login');
            header("Location: " . $_ENV['BASE_URL'] . "/login");
            exit();
        }

        $model = new FunctionalOrgModel($this->db);
        $zones = $model->getAll($zonelevel, $branchType);

        $this->render('register-zone', [
            'branches' => $zones,
            'currentLang' => $currentLang
        ]);
    }
     public function zoneRegistration()
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
                $_SESSION['error'] = 'እባክዎ የዞኑን/ከ/አስተዳደሩን ስም በትክክል ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/register-zone");
                exit();
            }

            if (empty($branch_type)) {
                $_SESSION['error'] = 'እባክዎ የዞኑን/ከተማ አስተዳደሩን ዓይነት ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/register-zone");
                exit();
            }
// Only allow valid bureau types
    $allowedTypes = ['zone', 'regio'];

    if (!in_array($branch_type, $allowedTypes, true)) {
        $_SESSION['error'] = 'የተመረጠው የዞን/ከተማ አስተዳደር ዓይነት ትክክል አይደለም!';
        header("Location: " . $_ENV['BASE_URL'] . "/register-zone");
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

            $model = new AdministrativeUnitModel($this->db);

            try {

                // 4. ሞዴሉን መጥራት
                // ይህ ድርጅቱን እና Main Officeን በአንድ ላይ ይመዘግባል
                $branchId = $model->create($data);

                if ($branchId) {

                    // Log organization creation
                    \App\Helpers\AuditHelper::log(
                        'zone_created',
                        'zone',
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
                        . "/register-zone"
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
                    . "/register-zone"
                );

                exit();
            }
        }
    }
 public function zoneEdit()
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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);

        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method.'
        ]);

        exit();
    }

    Csrf::verifyOrRedirect('login');

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
            'message' => 'እባክዎ የዞኑ/ክተማ አስተዳደሩ ዓይነት ይምረጡ።'
        ]);

        exit();
    }

    $allowedTypes = ['zone', 'regio'];

    if (!in_array($branchType, $allowedTypes, true)) {
        http_response_code(400);

        echo json_encode([
            'status' => 'error',
            'message' => 'የዞኑ/ክተማ አስተዳደሩዓይነት ትክክል አይደለም።'
        ]);

        exit();
    }

    $data = [
        'uuid'        => $uuid,
        'name'        => $name,
        'branch_type' => $branchType,
        'updated_by'  => $userId
    ];

    $model = new AdministrativeUnitModel($this->db);

    try {
    $updated = $model->updateZone($data);

    if ($updated['status'] === 'success') {

        \App\Helpers\AuditHelper::log(
            action:     'zone_updated',
            entityType: 'zone',
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
            'message' => 'መረጃው በትክክል ተስተካክሏል።'
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
        'message' => 'የዞኑ/ክተማ አስተዳደሩ መረጃ አልተገኘም።'
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
public function woredaIndex()
    {
        AuthHelper::checkRole(['system_admin', 'admin']);

        $userId = $_SESSION['user']['id'] ?? null;
        $currentLang = $_SESSION['lang'] ?? 'am';
        $woredas = [];
        $zones = [];
        $zones_types = ['zone','regio'];
        $zonelevel = 1;

        if (!$userId) {
            $_SESSION['error'] = \__('invalid_login');
            header("Location: " . $_ENV['BASE_URL'] . "/login");
            exit();
        }

     
        $zonemodel = new FunctionalOrgModel($this->db);
        $zones = $zonemodel->getAll($zonelevel, $zones_types);
 // Pagination for the woreda listing table
    $perPage     = 20;
    $currentPage = max(1, (int) ($_GET['page'] ?? 1));
    $search      = trim($_GET['search'] ?? '');
    $offset      = ($currentPage - 1) * $perPage;
    $woredamodel = new AdministrativeUnitModel($this->db);
    $totalCount = $woredamodel->countWoredasWithZone(null, $search ?: null);
    $totalPages = (int) max(1, ceil($totalCount / $perPage));

    if ($currentPage > $totalPages) {
        $currentPage = $totalPages;
        $offset = ($currentPage - 1) * $perPage;
    }
   
     $woredas = $woredamodel->getWoredasWithZone(null, $search ?: null, $perPage, $offset);

   
        $this->render('register-woreda', [
            'branches' => $woredas,
            'zones' => $zones,
            'currentLang' => $currentLang,
            'currentPage' => $currentPage,
            'totalPages'  => $totalPages,
            'search'      => $search,
        ]);
    }


 public function woredaRegistration()
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
                $_SESSION['error'] = 'እባክዎ የዞኑን/ከ/አስተዳደሩን ስም በትክክል ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/register-woreda");
                exit();
            }

            if (empty($branch_type)) {
                $_SESSION['error'] = 'እባክዎ የዞኑን/ከተማ አስተዳደሩን ዓይነት ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/register-woreda");
                exit();
            }
$zone = isset($_POST['zone'])
    ? trim($_POST['zone'])
    : '';

if (empty($zone)) {
    echo json_encode(['status' => 'error', 'message' => 'ዞን መምረጥ አስፈላጊ ነው።']);
    return;
}

$model = new FunctionalOrgModel($this->db); // or whichever model has findById()
$zoneBranch = $model->findById($zone);


// 1. Validate the zone/region selection itself
if (!in_array($zoneBranch['branch_type'], ['zone', 'regio'], true)) {
    echo json_encode(['status' => 'error', 'message' => 'የተመረጠው ዞን ወይም ከተማ አስተዳደር አይደለም።']);
    return;
}

// 2. Determine which child types are valid based on what was selected
$allowedTypes = $zoneBranch['branch_type'] === 'regio'
    ? ['kifle_ketema']              
    : ['ketema_woreda', 'woreda'];  // zone-level parent → normal woreda / city-woreda

if (!in_array($branch_type, $allowedTypes, true)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'ያልተፈቀደ የወረዳ/ ክ/ከተማ ዓይነት፦ ' . implode(' ወይም ', $allowedTypes) . ' ብቻ ይፈቀዳል።'
    ]);
    return;
}



            // 3. UUID ማመንጨት (ለ Organization)
            $uuid = Uuid::uuid7()->toString();

            $data = [
                'uuid' => $uuid,
                'branch_name' => $branch_name,
                'branch_type' => $branch_type,
                'organization_id' => $organizationId,
                'admin_parent_id' => $zoneBranch['id'],
                'registered_by' => $userId
            ];

            $model = new AdministrativeUnitModel($this->db);

            try {

                // 4. ሞዴሉን መጥራት
                // ይህ ድርጅቱን እና Main Officeን በአንድ ላይ ይመዘግባል
                $branchId = $model->createWoreda($data);

                if ($branchId) {

                    // Log organization creation
                    \App\Helpers\AuditHelper::log(
                        'woreda_created',
                        'woreda',
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
                        . "/register-woreda"
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
                    . "/register-woreda"
                );

                exit();
            }
        }
    }

 public function woredaEdit()
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
                $_SESSION['error'] = 'እባክዎ የዞኑን/ከ/አስተዳደሩን ስም/መለያ በትክክል ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/register-woreda");
                exit();
            }

            if (empty($branch_type)) {
                $_SESSION['error'] = 'እባክዎ የዞኑን/ከተማ አስተዳደሩን ዓይነት ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/register-woreda");
                exit();
            }
$zone = isset($_POST['zone'])
    ? trim($_POST['zone'])
    : '';

if (empty($zone)) {
    echo json_encode(['status' => 'error', 'message' => 'ዞን መምረጥ አስፈላጊ ነው።']);
    return;
}

$model = new FunctionalOrgModel($this->db); // or whichever model has findById()
$zoneBranch = $model->findById($zone);


// 1. Validate the zone/region selection itself
if (!in_array($zoneBranch['branch_type'], ['zone', 'regio'], true)) {
    echo json_encode(['status' => 'error', 'message' => 'የተመረጠው ዞን ወይም ከተማ አስተዳደር አይደለም።']);
    return;
}

// 2. Determine which child types are valid based on what was selected
$allowedTypes = $zoneBranch['branch_type'] === 'regio'
    ? ['kifle_ketema']              
    : ['ketema_woreda', 'woreda'];  // zone-level parent → normal woreda / city-woreda

if (!in_array($branch_type, $allowedTypes, true)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'ያልተፈቀደ የወረዳ/ ክ/ከተማ ዓይነት፦ ' . implode(' ወይም ', $allowedTypes) . ' ብቻ ይፈቀዳል።'
    ]);
    return;
}

            $data = [
                'uuid' => $id,
                'name' => $branch_name,
                'branch_type' => $branch_type,
                'admin_parent_id' => $zoneBranch['id'],
                'updated_by' => $userId
            ];

            $model = new AdministrativeUnitModel($this->db);

            try {

                // 4. ሞዴሉን መጥራት
                // ይህ ድርጅቱን እና Main Officeን በአንድ ላይ ይመዘግባል
                $branchId = $model->updateWoreda($data);

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
                        . "/register-woreda"
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
                    . "/register-woreda"
                );

                exit();
            }
        }
    }

}