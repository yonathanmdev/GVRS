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
        $this->render('register-vehicle', [
            'bureaus' => $bureaus,
            'zones' =>$zones,
            'memriya' => $memriya,
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
}