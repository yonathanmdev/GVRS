<?php
namespace App\Controllers;
use App\Models\User;
use App\Models\CarModel;
use App\Helpers\AuthHelper;
use App\Helpers\Csrf;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;
use Ramsey\Uuid\Uuid;

class CarBrandRegistration extends BaseController
{
    
    public function carregIndex()
    {
        AuthHelper::checkRole(['system_admin', 'admin']);

        $userId = $_SESSION['user']['id'] ?? null;
        $currentLang = $_SESSION['lang'] ?? 'am';
        //$branchType = ['bureau', 'authority','commission','institution','enterprise','memriya','tsfet_bet'];
       // $level = 1;
      //  $bureaus = [];

        if (!$userId) {
            $_SESSION['error'] = \__('invalid_login');
            header("Location: " . $_ENV['BASE_URL'] . "/login");
            exit();
        }

        $model = new CarModel($this->db);
        $cars = $model->getAll();

        $this->render('register-car-brand', [
            'cars' => $cars,
             'currentLang' => $currentLang
            
        ]);
    }


    
    public function carBrandRegistration()
    {
        AuthHelper::checkRole(['system_admin', 'admin']);

        $userId = $_SESSION['user']['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            Csrf::verifyOrRedirect('login');
            if (!$userId) {
                $_SESSION['error'] = \__('invalid_login');
                header("Location: " . $_ENV['BASE_URL'] . "/login");
                exit();
            }

            // 1. ዳታውን መቀበል
            $car_brand_name = isset($_POST['car_brand_name'])
                ? trim($_POST['car_brand_name'])
                : '';

            // 2. Validation
            if (empty($car_brand_name)) {
                $_SESSION['error'] = 'እባክዎ የመኪናዉ ብራንድ ስም በትክክል ያስገቡ!';
                header("Location: " . $_ENV['BASE_URL'] . "/register-car-brand");
                exit();
            }

            // 3. UUID ማመንጨት
            $uuid = Uuid::uuid7()->toString();

            $data = [
                'uuid' => $uuid,
                'car_brand_name' => $car_brand_name,
                'registered_by' => $userId
            ];

            $model = new CarModel($this->db);

            try {
                // 4. ሞዴሉን መጥራት (የተፈጠረውን ብራንድ UUID ይመልሳል)
                $brandUuid = $model->create($data);

                if ($brandUuid) {

                    // Log car brand creation (የኦዲት ሎግ ማስተካከያ)
                    \App\Helpers\AuditHelper::log(
                        'car_brand_created',
                        'car_brand',
                        $brandUuid,
                        null,
                        [
                            'name' => $car_brand_name,
                            'registered_by' => $userId
                        ]
                    );

                    $_SESSION['success'] = \__('operation_sucess');

                    header(
                        "Location: "
                        . $_ENV['BASE_URL']
                        . "/register-car-brand"
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

                    $_SESSION['error'] = "ይህ ብራንድ ቀደም ብሎ ተመዝግቧል!";

                } else {

                    error_log(
                        "Registration Error: "
                        . $e->getMessage()
                    );

                    $_SESSION['error'] = \__('operation_failed');
                }

                header(
                    "Location: "
                    . $_ENV['BASE_URL']
                    . "/register-car-brand"
                );

                exit();
            }
        }
    }

    
 public function carBrandEdit()
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

    $name = isset($_POST['car_car_name'])
        ? trim($_POST['car_car_name'])
        : '';

    

    if (empty($uuid)) {
        http_response_code(400);

        echo json_encode([
            'status' => 'error',
            'message' => 'የመኪናዉ መለያ አልተገኘም።'
        ]);

        exit();
    }

    if (empty($name)) {
        http_response_code(400);

        echo json_encode([
            'status' => 'error',
            'message' => 'እባክዎ የመኪናዉ ስም ያስገቡ።'
        ]);

        exit();
    }

     

   

    $data = [
        'uuid'        => $uuid,
        'name'        => $name,
         
        'updated_by'  => $userId
    ];

    $model = new CarModel($this->db);

   try {
    $updated = $model->updatecar($data);

    if ($updated['status'] === 'success') {

        \App\Helpers\AuditHelper::log(
            action:     'car_brand_updated',
            entityType: 'car_brand',
            entityId:   $updated['id'],
            oldValues:  null,
            newValues:  [
                'name'       => $name,
                 
                'updated_by' => $userId,
            ]
        );

        echo json_encode([
            'status'  => 'success',
            'message' => 'የመኪናዉ መረጃ በትክክል ተስተካክሏል።'
        ]);
        exit();
    }

    // Distinguish "not found" from "blocked by incompatible children"
   

 

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



/*
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

*/

public function deleteCarBrand(): void
{
    AuthHelper::checkRole(['system_admin', 'admin']);
    header('Content-Type: application/json');

    $data   = json_decode(file_get_contents('php://input'), true);
    $id     = (string) ($data['id']   ?? '');
 
    $userId = $_SESSION['user']['id'] ?? '';
    $userRole = $_SESSION['user']['role'] ?? '';
     

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
        $model   = new carModel($this->db);
        $action  = 'car_deleted';
        $metaKey = 'affected_cars';

        $result = $model->softDelete($id, $userId, $reason, $source);

        $branchCount = $result['branchCount'] ?? 0;
        $userCount   = $result['userCount'] ?? 0;
        $oldRecord   = $result['oldRecord'] ?? null;


        \App\Helpers\AuditHelper::log(
            action:     $action,
            entityType: 'car_brand',
            entityId:   $result['id'],
            oldValues:  $oldRecord,
            newValues:  ['status' => 'deleted value to 1 '],
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
        error_log("Delete Error (car_brand): " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'ስህተት ተፈጥሯል፤ እባክዎ በድጋሚ ይሞክሩ።']);
    }
}

 
public function index()
{
    $carTypeModel = new carModel($this->db);

    // 1. ለሰንጠረዡ (Table) የተመዘገቡ የመኪና/ማሽን አይነቶች መረጃ ማምጣት
    $cars         = $carTypeModel->getRegisteredCarTypes();

    // 2. ለ Pop-up Modal Dropdowns የሚያስፈልጉ መረጃዎች
    $brands       = $carTypeModel->getActiveBrands();
    $typeLists    = $carTypeModel->getCarTypeLists();
    $serviceTypes = $carTypeModel->getCarServices();

    $currentLang  = $_SESSION['lang'] ?? 'am';

    // 3. ሁሉንም አስፈላጊ Variables ወደ View ማስተላለፍ
    $this->render('register-car-type', [
        'cars'         => $cars,
        'brands'       => $brands,
        'typeLists'    => $typeLists,
        'serviceTypes' => $serviceTypes,
        'currentLang'  => $currentLang
    ]);
}
    

    /**
     * የመዝገባ ሂደቱን ማስተናገጃ Method
     */
    public function store()
{
    $carTypeModel = new carModel($this->db);

    // 1. Request Method ማረጋገጥ
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . ($_ENV['BASE_URL'] ?? '/') . '/car-types');
        exit;
    }

    // 2. CSRF Token ማረጋገጥ
    $token = $_POST['csrf_token'] ?? $_POST['_token'] ?? '';
   /*  if (!\App\Helpers\Csrf::verify($token)) {
        $_SESSION['error'] = 'Invalid CSRF token!';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
        exit;
    } */

    // 3. የተጠቃሚው Session መኖሩን ማረጋገጥ
    if (!isset($_SESSION['user']['id'])) {
        $_SESSION['error'] = 'እባክዎ መጀመሪያ ወደ ሲስተሙ ይግቡ።';
        header('Location: ' . ($_ENV['BASE_URL'] ?? '/') . '/login');
        exit;
    }

    // 4. Inputs Sanitization & Validation
    $brandId     = filter_input(INPUT_POST, 'brand_id', FILTER_VALIDATE_INT);
    $typeName    = filter_input(INPUT_POST, 'type_name', FILTER_VALIDATE_INT);
    $serviceType = filter_input(INPUT_POST, 'service_type', FILTER_VALIDATE_INT);
    $measurement = trim($_POST['measurement'] ?? '');
    $catagory    = trim($_POST['catagory'] ?? '');

    // ENUM Values Validation
    $allowedMeasurements = ['በሰው', 'በሊትር', 'በፈረስ ጉልበት', 'በኩንታል'];
    $allowedCategories   = ['vehicle', 'machine'];

    if (
        !$brandId || 
        !$typeName || 
        !$serviceType || 
        !in_array($measurement, $allowedMeasurements, true) || 
        !in_array($catagory, $allowedCategories, true)
    ) {
        $_SESSION['error'] = 'እባክዎ ሁሉንም አስፈላጊ መረጃዎች በትክክል ይሙሉ!';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
        exit;
    }

    try {
        $registeredBy = (int) $_SESSION['user']['id'];

        // --- 5. Duplication Check (መረጃው ቀድሞ መኖሩን ማረጋገጥ) ---
        if ($carTypeModel->isDuplicate($brandId, $typeName, $serviceType, $measurement, $catagory)) {
            $_SESSION['error'] = 'ይህ የመኪና/ማሽን አይነት ቀደም ሲል የተመዘገበ ስለሆነ ድጋሚ መመዝገብ አይቻልም!';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
            exit;
        }

        // መረጃውን መመዝገብ
        $isSaved = $carTypeModel->createcartype([
            'brand_id'     => $brandId,
            'type_name'    => $typeName,
            'service_type' => $serviceType,
            'measurement'  => $measurement,
            'catagory'     => $catagory,
            'registerd_by' => $registeredBy
        ]);

        if ($isSaved) {
            $_SESSION['success'] = 'የመኪና/ማሽን አይነቱ በተሳካ ሁኔታ ተመዝግቧል!';
        } else {
            $_SESSION['error'] = 'መረጃውን መመዝገብ አልተቻለም። እባክዎ እንደገና ይሞክሩ።';
        }

    } catch (\PDOException $e) {
        error_log('CarType Registration Error: ' . $e->getMessage());
        $_SESSION['error'] = 'የሲስተም ስህተት አጋጥሟል። እባክዎ ትንሽ ቆይተው እንደገና ይሞክሩ።';
    }

    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? $_ENV['BASE_URL']));
    exit;
}

// Update Process
public function updateProcess()
{
    \App\Helpers\Csrf::verify();

    $uuid        = filter_input(INPUT_POST, 'uuid', FILTER_SANITIZE_SPECIAL_CHARS);
    $brandId     = filter_input(INPUT_POST, 'brand_id', FILTER_VALIDATE_INT);
    $typeName    = filter_input(INPUT_POST, 'type_name', FILTER_VALIDATE_INT);
    $serviceType = filter_input(INPUT_POST, 'service_type', FILTER_VALIDATE_INT);
    $measurement = filter_input(INPUT_POST, 'measurement', FILTER_SANITIZE_SPECIAL_CHARS);
    $catagory    = filter_input(INPUT_POST, 'catagory', FILTER_SANITIZE_SPECIAL_CHARS);
    $userId      = $_SESSION['user']['id'] ?? 0;

    if ($uuid && $brandId && $typeName && $serviceType && $measurement && $catagory) {
        $carModel = new carModel($this->db);
        $updated  = $carModel->updateCarType([
            'uuid'         => $uuid,
            'brand_id'     => $brandId,
            'type_name'    => $typeName,
            'service_type' => $serviceType,
            'measurement'  => $measurement,
            'catagory'     => $catagory,
            'updated_by'   => $userId
        ]);

        if ($updated) {
            $_SESSION['success'] = "መረጃው በትክክል ተስተካክሏል!";
        } else {
            $_SESSION['error'] = "መረጃውን ማስተካከል አልተቻለም።";
        }
    }

    header('Location: ' . rtrim($_ENV['BASE_URL'] ?? '', '/') . '/register-car-type');
    exit;
}

// Soft Delete Process
public function deleteProcess()
{
    \App\Helpers\Csrf::verify();

    $uuid   = filter_input(INPUT_POST, 'uuid', FILTER_SANITIZE_SPECIAL_CHARS);
    $userId = $_SESSION['user']['id'] ?? 0;

    if ($uuid) {
        $carModel = new carModel($this->db);
        $deleted  = $carModel->deleteCarType($uuid, $userId);

        if ($deleted) {
            $_SESSION['success'] = "መረጃው በትክክል ተሰርዟል!";
        } else {
            $_SESSION['error'] = "መረጃውን መሰረዝ አልተቻለም።";
        }
    }

    header('Location: ' . rtrim($_ENV['BASE_URL'] ?? '', '/') . '/register-car-type');
    exit;
}
}
