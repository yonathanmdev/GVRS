<?php
namespace App\Controllers;
use App\Models\User;
use App\Models\FunctionalOrgModel;
use App\Models\AdministrativeUnitModel;
use App\Helpers\AuthHelper;
use App\Helpers\Csrf;
use App\Models\VehicleModel;
use Ramsey\Uuid\Uuid;

class VehicleController extends BaseController
{
    public function index()
    {
        AuthHelper::checkRole(['system_admin', 'officer']);

        $userId = $_SESSION['user']['id'] ?? null;
        $currentLang = $_SESSION['lang'] ?? 'am';
        $branchType = ['bureau', 'authority','commission','institution','enterprise','memriya','tsfet_bet'];
        $bureaulevel = 1;
        $bureaus = [];
        $zones = [];
        $zones_types = ['zone','regio'];
        $zonelevel = 1;
        $memriya = [];
        $memriya_types = ['memriya'];
        $memriyalevel = 2;

        if (!$userId) {
            $_SESSION['error'] = \__('invalid_login');
            header("Location: " . $_ENV['BASE_URL'] . "/login");
            exit();
        }
        
        $model = new FunctionalOrgModel($this->db);
        $bureaus = $model->getAll($bureaulevel, $branchType);      
        $zones = $model->getAll($zonelevel, $zones_types);
        $memriya = $model->getAll($memriyalevel, $memriya_types);
        $brands = (new VehicleModel($this->db))->getDistinctBrands();
        $this->render('register-vehicle', [
            'bureaus' => $bureaus,
            'zones' =>$zones,
            'memriya' => $memriya,
            'brands' => $brands,
            'currentLang' => $currentLang
        ]);
    }

    public function institutions(): void
    {
        AuthHelper::checkRole(['system_admin', 'officer']);
        header('Content-Type: application/json');
 
        $bureauUuid = trim($_GET['bureau_id'] ?? '');
 
        if ($bureauUuid === '') {
            http_response_code(422);
            echo json_encode(['error' => 'bureau_id required']);
            return;
        }
 
        $orgModel = new FunctionalOrgModel($this->db);
        $bureau = $orgModel->findById($bureauUuid);
 
        if ($bureau === null) {
            http_response_code(404);
            echo json_encode(['error' => 'bureau_not_found']);
            return;
        }
 
        $rows = $orgModel->getaccountableOfficesWithBureau(2, $bureau['id'], null, 100, 0);
 
        $options = array_map(function ($row) {
            return [
                'id'   => $row['uuid'],
                'name' => $row['office_name'],
            ];
        }, $rows);
 
        echo json_encode($options);
    }
public function woredas(): void
{
    AuthHelper::checkRole(['system_admin', 'officer']);
    header('Content-Type: application/json');

    $zoneUuid = trim($_GET['zone_id'] ?? '');

    if ($zoneUuid === '') {
        http_response_code(422);
        echo json_encode(['error' => 'zone_id required']);
        return;
    }

    // Zones live in the admin hierarchy - resolving via AdministrativeUnitModel
    // (or a shared BranchModel::findByUuid() if you move it there) keeps the
    // model boundaries consistent with FunctionalOrgModel handling bureaus/institutions.
    $adminModel = new AdministrativeUnitModel($this->db);
    $zone = $adminModel->findById($zoneUuid);

    if ($zone === null) {
        http_response_code(404);
        echo json_encode(['error' => 'zone_not_found']);
        return;
    }

    // No $level param here - getWoredasWithZone() only takes
    // (zoneId, search, limit, offset). branch_type + zone_id already scope it fully.
    $rows = $adminModel->getWoredasWithZone($zone['id'], null, 100, 0);

    $options = array_map(function ($row) {
        return [
            'id'   => $row['uuid'],
            'name' => $row['woreda_name'],
        ];
    }, $rows);

    echo json_encode($options);
}
// --- add to create(): preload the brand list alongside bureaus/zones/memriya ---
//
// $carTypeModel = new CarTypeModel($this->db);
// $carBrands = $carTypeModel->getDistinctBrands();
// ...then pass $carBrands into the view alongside $bureaus, $memriya, $zones


// GET /vehicles-car-types?brand=<brand>
public function carTypesByBrand(): void
{
    AuthHelper::checkRole(['system_admin', 'officer']);
    header('Content-Type: application/json');

    $brand = trim($_GET['brand'] ?? '');

    if ($brand === '') {
        http_response_code(422);
        echo json_encode(['error' => 'brand required']);
        return;
    }

    $carTypeModel = new VehicleModel($this->db);
    $rows = $carTypeModel->getTypesByBrand($brand);

    // Return service_name/measurement alongside id/name so the frontend
    // can populate the read-only fields directly on selection, no
    // second AJAX call needed.
    $options = array_map(function ($row) {
        return [
            'id'            => $row['car_type_id'],
            'name'          => $row['type_name'],
            'service_name'  => $row['service_name'],
            'measurement'   => $row['measurement'],
        ];
    }, $rows);

    echo json_encode($options);
}
 public function store(): void
{
    AuthHelper::checkRole(['system_admin', 'officer']);
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method.'
        ]);
        exit();
    }

    // Verify CSRF before processing any submitted data
    Csrf::verifyOrRedirect('login');

    $userId = $_SESSION['user']['id'] ?? null;
    if (!$userId) {
        $_SESSION['error'] = \__('invalid_login');
        header("Location: " . $_ENV['BASE_URL'] . "/login");
        exit();
    }

    // sample-only: minimal validation, just enough to test the flow
    $branchId          = $_POST['branch_id'] ?? null;
    $zone_id           = $_POST['zone_id'] ?? null;
    $plate_number      = trim($_POST['plate_number'] ?? '');
    $vehicle_type      = trim($_POST['vehicle_type'] ?? '');
    $vehicleModelName  = trim($_POST['model'] ?? ''); // renamed so it can't collide
    $chassis_number    = trim($_POST['chassis_number'] ?? '');
    $engine_number     = trim($_POST['engine_number'] ?? '');
    $capacity          = trim($_POST['capacity'] ?? '');
    $manufactured_year = trim($_POST['manufactured_year'] ?? '');
    $estimated_price   = trim($_POST['estimated_price'] ?? '');
    $purchase_year     = trim($_POST['purchase_year'] ?? '');
    $vehicle_status    = trim($_POST['vehicle_status'] ?? '');

    // --- Required field validation ---
    $requiredFields = [
        'plate_number'      => $plate_number,
        'vehicle_type'      => $vehicle_type,
        'capacity'          => $capacity,
        'manufactured_year' => $manufactured_year,
        'estimated_price'   => $estimated_price,
        'purchase_year'     => $purchase_year,
        'vehicle_status'    => $vehicle_status,
    ];

    $errors = [];
    foreach ($requiredFields as $field => $value) {
        if ($value === '') {
            $errors[$field] = \__('field_required');
        }
    }

    // --- Year validation (only if the year fields are present) ---
    $minYear = 1950;
    $maxYear = (int) date('Y');

    if ($manufactured_year !== '') {
        $mfgYear = (int) $manufactured_year;
        if ($mfgYear < $minYear || $mfgYear > $maxYear) {
            $errors['manufactured_year'] = \__('invalid_manufactured_year');
        }
    }

    if ($purchase_year !== '') {
        $purYear = (int) $purchase_year;
        if ($purYear < $minYear || $purYear > $maxYear) {
            $errors['purchase_year'] = \__('invalid_purchase_year');
        }
    }

    // Only compare the two years if both individually passed their own checks
    if (!isset($errors['manufactured_year']) && !isset($errors['purchase_year'])
        && $manufactured_year !== '' && $purchase_year !== ''
        && (int) $purchase_year < (int) $manufactured_year
    ) {
        $errors['purchase_year'] = \__('purchase_year_before_manufactured');
    }

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => \__('validation_failed'),
            'errors'  => $errors
        ]);
        return;
    }

    $orgModel  = new FunctionalOrgModel($this->db);
    $brachData = $orgModel->findById($branchId);

    $adminModel = new AdministrativeUnitModel($this->db);
    $adminData  = $adminModel->findById($zone_id);

    if ($brachData === null) {
        http_response_code(404);
        echo json_encode(['error' => 'branch_not_found']);
        return;
    }

    $data = [
        'uuid'              => Uuid::uuid7()->toString(),
        'branch_id'         => $brachData['id'],
        'zone_id'           => $adminData['id'] ?? null,
        'plate_number'      => $plate_number,
        'vehicle_type'      => $vehicle_type,
        'model'             => $vehicleModelName,
        'chassis_number'    => $chassis_number,
        'engine_number'     => $engine_number,
        'capacity'          => $capacity,
        'manufactured_year' => $manufactured_year,
        'estimated_price'   => $estimated_price,
        'purchase_year'     => $purchase_year,
        'vehicle_status'    => $vehicle_status,
        'registered_by'     => $userId
    ];

   try {
    $vehicleModel = new VehicleModel($this->db);
    $created = $vehicleModel->create($data);

    if ($created) {
        echo json_encode(['success' => true, 'message' => 'በትክክል ተመዝግቧል']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => \__('registration_failed')]);
    }
} catch (\RuntimeException $e) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => \__($e->getMessage())]);
}
}
public function list(): void
{
    AuthHelper::checkRole(['system_admin', 'mgmt','officer']);

    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = 50;
    $offset = ($page - 1) * $limit;

    $search   = trim($_GET['search'] ?? '');
    $branchId = !empty($_GET['branch_id']) ? (int) $_GET['branch_id'] : null;

    $vehicleModel = new VehicleModel($this->db);
    
    $vehicles     = $vehicleModel->getRegisteredVehicles($branchId, $limit, $offset, $search);
    $totalCount   = $vehicleModel->countRegisteredVehicles($branchId, $search);
    $totalPages   = (int) ceil($totalCount / $limit);
    $brands       = $vehicleModel->getDistinctBrands();

    //$branchModel = new FunctionalOrgModel($this->db);
    //$branches    = $branchModel->getAllForFilter();

    $this->render('vehicles-list', [
        'vehicles'       => $vehicles,
        'brands'         => $brands,
        'selectedBranch' => $branchId,
        'search'         => $search,
        'page'           => $page,
        'totalPages'     => $totalPages,
        'totalCount'     => $totalCount,
        'currentLang'    => $_SESSION['lang'] ?? 'am'
    ]);
}
public function editData(): void
{
    AuthHelper::checkRole(['system_admin', 'officer']);
    header('Content-Type: application/json');

    $uuid = (string) ($_GET['id'] ?? '');

    if (empty($uuid)) {
        echo json_encode(['success' => false, 'message' => 'መለያ አልገባም']);
        return;
    }

    try {
        
        $model   = new VehicleModel($this->db);
        $vehicle = $model->findByUuidWithDetails($uuid);

        if (!$vehicle) {
            echo json_encode(['success' => false, 'message' => 'ተሽከርካሪው አልተገኘም።']);
            return;
        }

        // Human-readable "currently registered under" label, built from real joins.
        $officeParts = array_filter([
            $vehicle['branch_name'] ?? null,
            $vehicle['zone_name']   ?? null,
        ]);
        $officeLabel = $officeParts ? implode(' / ', $officeParts) : null;

        echo json_encode([
            'success'      => true,
            'vehicle'      => [
                'uuid'              => $vehicle['uuid'],
                'plate_number'      => $vehicle['plate_number'],
                'model'             => $vehicle['model'],
                'chassis_number'    => $vehicle['chassis_number'],
                'engine_number'     => $vehicle['engine_number'],
                'capacity'          => $vehicle['capacity'],
                'estimated_price'   => $vehicle['estimated_price'],
                'manufactured_year' => $vehicle['manufactured_year'],
                'purchase_year'     => $vehicle['purchase_year'],
                'vehicle_status'    => $vehicle['vehicle_status'],
                'branch_id'         => $vehicle['branch_id']     ?? null,
                'vehicle_type_id'   => $vehicle['vehicle_type']  ?? null,
                'brand_id'          => $vehicle['brand_id']      ?? null,
                'brand_name'        => $vehicle['brand_name']    ?? null,
                'type_name'         => $vehicle['type_name']     ?? null,
                'branch_name'       => $vehicle['branch_name']   ?? null,
                'zone_name'         => $vehicle['zone_name']     ?? null,
                // TODO: add once source table/column is confirmed
                'measurement'       => $vehicle['measurement']   ?? null,
                'service_name'      => $vehicle['service_name']  ?? null,

            ],
            'office_label' => $officeLabel,
        ]);

    } catch (\Exception $e) {
        error_log("Vehicle EditData Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'መረጃ ማምጣት አልተቻለም።']);
    }
}

 public function update(): void
    {
        header('Content-Type: application/json; charset=utf-8');
 AuthHelper::checkRole(['system_admin', 'officer']);
 $userId = $_SESSION['user']['id'] ?? null;
       if($_SERVER['REQUEST_METHOD'] === 'POST') {
        Csrf::verifyOrRedirect('login');
        $uuid = trim($_POST['id'] ?? '');
 
        if ($uuid === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'የተሽከርካሪው ID አልተላከም',
            ]);
            return;
        }
 
        // --- Required field validation (server-side mirror of the JS) ---
        $required = [
            'brand_id'          => 'ብራንድ መምረጥ አለብዎት።',
            'vehicle_type_id'   => 'የተሽከርካሪው/ማሽነሪው ዓይነት መምረጥ አለብዎት።',
            'plate_number'      => 'ሰሌዳ ቁጥር ማስገባት አለብዎት።',
            'capacity'          => 'የመጫን አቅም ማስገባት አለብዎት።',
            'estimated_price'   => 'ግምታዊ ዋጋ ማስገባት አለብዎት።',
            'manufactured_year' => 'የተመረተበትን ዓ.ም ማስገባት አለብዎት።',
            'purchase_year'     => 'የተገዛበትን ዓ.ም ማስገባት አለብዎት።',
            'vehicle_status'    => 'የአሁኑን ሁኔታ መምረጥ አለብዎት።',
        ];
 
        foreach ($required as $field => $message) {
            if (!isset($_POST[$field]) || trim((string) $_POST[$field]) === '') {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => $message,
                ]);
                return;
            }
        }
 
        $manufacturedYear = (int) $_POST['manufactured_year'];
        $purchaseYear     = (int) $_POST['purchase_year'];
        $currentYear      = (int) date('Y'); // GC, matches the form's max="" attribute
 
        if ($manufacturedYear < 1950 || $manufacturedYear > $currentYear) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'የተመረተበት ዓ.ም ትክክል አይደለም።',
            ]);
            return;
        }
 
        if ($purchaseYear < 1950 || $purchaseYear > $currentYear) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'የተገዛበት ዓ.ም ትክክል አይደለም።',
            ]);
            return;
        }
 
        if ($purchaseYear < $manufacturedYear) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'የተገዛበት ዓ.ም ከተመረተበት ዓ.ም በፊት ሊሆን አይችልም።',
            ]);
            return;
        }
 
        $estimatedPrice = filter_var(
            $_POST['estimated_price'],
            FILTER_VALIDATE_FLOAT
        );
 
        if ($estimatedPrice === false || $estimatedPrice < 0) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'ግምታዊ ዋጋ ትክክል አይደለም።',
            ]);
            return;
        }
 
        try {
            $vehicleModel = new VehicleModel($this->db);
            $existing = $vehicleModel->findByUuid($uuid);
 
            if (!$existing) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'ተሽከርካሪው አልተገኘም',
                ]);
                return;
            }
            $newValues = [
                'uuid'              => $uuid,
                'vehicle_type'   => (int) $_POST['vehicle_type_id'],
                'plate_number'      => trim($_POST['plate_number']),
                'model'             => trim($_POST['model'] ?? ''),
                'chassis_number'    => trim($_POST['chassis_number'] ?? ''),
                'engine_number'     => trim($_POST['engine_number'] ?? ''),
                'capacity'          => trim($_POST['capacity']),
                'estimated_price'   => $estimatedPrice,
                'manufactured_year' => $manufacturedYear,
                'purchase_year'     => $purchaseYear,
                'vehicle_status'    => trim($_POST['vehicle_status']),
                'updated_by'        => $userId,

            ];
 
            $updated = $vehicleModel->updateByUuid($newValues);
 
            if (!$updated) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'ማዘመን አልተቻለም',
                ]);
                return;
            }
 
            \App\Helpers\AuditHelper::log(
                action: 'vehicle_updated',
                entityType: 'vehicle',
                entityId: $existing['id'], // internal BIGINT id, not uuid
                oldValues: $existing,
                newValues: $newValues,
                metadata: ['uuid' => $uuid]
            );
 
            echo json_encode([
                'success' => true,
                'message' => 'መረጃው በትክክል ተዘምኗል',
                'vehicle' => array_merge(['uuid' => $uuid], $newValues),
            ]);
        } catch (\Throwable $e) {
            error_log('[VehicleController::update] ' . $e->getMessage());
 
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'ማዘመን ላይ ስህተት ተፈጥሯል',
            ]);
        }
    }
    }
public function purge(): void
{
    AuthHelper::checkRole(['system_admin', 'officer']);
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    $csrf = (string) ($data['csrf_token'] ?? '');

    if (!\App\Helpers\Csrf::verify($csrf)) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'ያልተፈቀደ ጥያቄ (Invalid request token)']);
        return;
    }
    $id       = (string) ($data['id']               ?? '');
    $adminId  = (string) ($_SESSION['user']['id']   ?? '');
    $password = (string) ($data['confirm_password'] ?? '');
    $reason   = trim((string) ($data['reason']       ?? ''));

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
        // 2. Load target record
        // ============================================================
        $archiveId  = Uuid::uuid7()->toString();
        $model      = new VehicleModel($this->db);
        $action     = 'vehicle_purged';
        $entityType = 'vehicle';
        $metaKey    = 'purged_vehicles';
        $oldRecord  = $model->findByUuid($id);

        if (!$oldRecord) {
            echo json_encode(['status' => 'error', 'message' => 'መረጃው አልተገኘም።']);
            return;
        }

        // ============================================================
        // 3. Purge — model handles archive + hard delete internally
        // ============================================================
        $result = $model->purge($id, $archiveId, (int) $adminId, $reason);

        // ============================================================
        // 4. Audit log — includes archiveId + reason so you can trace back
        // ============================================================
        if ($result['status'] === 'success') {
            \App\Helpers\AuditHelper::log(
                action:     $action,
                entityType: $entityType,
                entityId:   $id,
                oldValues:  $oldRecord,
                newValues:  null,
                metadata:   [
                    $metaKey        => 1,
                    'archive_id'    => $result['archiveId'] ?? null,
                    'deletion_type' => 'permanent_purge',
                    'reason'        => $reason,
                    'confirmed_by'  => $adminId,
                ]
            );
        }

        echo json_encode($result);

    } catch (\Exception $e) {
        error_log("Purge Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'መሰረዝ አልተቻለም።']);
    }
}
}