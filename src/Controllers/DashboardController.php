<?php
namespace App\Controllers;
use App\Helpers\AuthHelper;
use App\Models\dashboardmodel;

class DashboardController extends BaseController {
    


public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        AuthHelper::checkRole(['system_admin','admin','mgmt','officer']);
        // ዳታውን ወደ ቪው ማስተላለፍ
        $currentLang = $_SESSION['lang'] ?? 'am';
        $data = [
            'title'                => 'GVRS - ዳሽቦርድ',
            'currentLang' => $currentLang
        ];

        $this->render('dashboard', $data);
    }

public function dashboard(): void
{
    AuthHelper::checkRole(['system_admin', 'admin', 'mgmt', 'officer']);
    
    $currentLang = $_SESSION['lang'] ?? 'am';
    $id = $_SESSION['user']['id'] ?? null;

    // ሞዴሉን በአንድ ጊዜ መጥራት
    $branchModel = new dashboardmodel($this->db); 
    
    $totalBureaus = $branchModel->countbureaus($id);
    $totalZonal   = $branchModel->countzonal($id);
    $totalVichel  = $branchModel->countvichel($id);
    
    // ሁሉንም መረጃዎች በአንድ የ $data አሬይ ውስጥ ማጠቃለል
    $data = [
        'title'        => 'GVRS - ዳሽቦርድ',
        'currentLang'  => $currentLang,
        'total_bureaus'=> $totalBureaus,
        'total_zonal'  => $totalZonal,
        'total_vichel' => $totalVichel
    ];

    // ሁሉንም በአንዴ ወደ ቪው መላክ
    $this->render('dashboard', $data);
}

public function reportView(): void
{
    AuthHelper::checkRole(['system_admin', 'admin', 'mgmt', 'officer']);
    
    $currentLang = $_SESSION['lang'] ?? 'am';
    
    $branchModel = new DashboardModel($this->db);

    $secondList = [];
    $thirdList  = [];
    $fourthList = []; 

    $reportType     = $_GET['report_type'] ?? '';
    $secondSelect   = $_GET['second_select'] ?? '';
    $thirdSelect    = $_GET['third_select'] ?? '';
    $fourthSelect   = $_GET['fourth_select'] ?? ''; 
    $zoneSubFilter  = $_GET['zone_sub_filter'] ?? ''; // 🌟 1. የዞን ንዑስ ማጣሪያውን እዚህ እንቀበላለን

    if ($reportType === 'regional') {
        $secondList = $branchModel->getRegionalBranches(); 
        $fourthList = $branchModel->getRegionalBranches2();
    }
    else if ($reportType === 'agency') {
        $secondList = $branchModel->getRegionalBranches(); 
    }
    else if ($reportType === 'department') {
        $secondList = $branchModel->getRegional2ndMemriya(null); 
        $thirdList = $branchModel->getAdministrativeBranches();
    }
    else if ($reportType === 'zone') {
        $secondList = $branchModel->getAdministrativeBranches(); 
    }
    else if ($reportType === 'wereda') {
        $secondList = $branchModel->getAdministrativeBranches(); 
    }
    else if ($reportType === 'calls') {
        $secondList = $branchModel->getbytype(null); 
    }

    if (!empty($secondSelect) && $secondSelect !== 'all') {
        if ($reportType === 'regional' || $reportType === 'agency') {
            $thirdList = $branchModel->getRegional2ndBranches($secondSelect);
        }
        else if ($reportType === 'department') {
            $thirdList = $branchModel->getAdministrativeBranches();
        }
        else if ($reportType === 'wereda') {
            $thirdList = $branchModel->getAdministrative2ndBranches($secondSelect);
        }
    }
    else if ($reportType === 'department' && $secondSelect === 'all') {
        $thirdList = $branchModel->getAdministrativeBranches();
    }

    // 🌟 2. ሪፖርቱን ከሞዴል ስንጠራ 'zone_sub_filter'ን አብረን እንልካለን
    $filters = [
        'report_type'     => $reportType,
        'second_select'   => $secondSelect,
        'third_select'    => $thirdSelect,
        'zone_sub_filter' => $zoneSubFilter
    ];
    
    $reportResult = $branchModel->getDynamicVehicleReport($filters); 

    $data = [
        'title'        => 'የሪፖርት ማጣሪያ ፎርም',
        'currentLang'  => $currentLang,
        'r_type'       => $reportType,       
        'secondSelect' => $secondSelect,    
        'thirdSelect'  => $thirdSelect,
        'zoneSubFilter'=> $zoneSubFilter, // ወደ ቪው (View) እንልካለን
        'secondList'   => $secondList,
        'thirdList'    => $thirdList,
        'fourthList'   => $fourthList,
        'car_types'    => $reportResult['car_types'] ?? [],     // 🌟 ከሞዴል የመጣው የကား አይነቶች
        'report_data'  => $reportResult['report_data'] ?? []    // 🌟 ከሞዴል የመጣው የሪፖርት ዴታ
    ];

    $this->render('report_view', $data);
}



public function report1controller(): void
{
    AuthHelper::checkRole(['system_admin', 'admin', 'mgmt', 'officer']);
    
    $currentLang =$_SESSION['lang'] ?? 'am';
    $branchModel = new dashboardmodel($this->db);

    $secondList  = [];$thirdList   = [];
    $fourthList  = [];$reportData  = [];

    // 1. መረጃዎች ከ URL (GET) መምጣቱን እንቀበላለን
    $reportType   =$_GET['report_type'] ?? '';
    $secondSelect =$_GET['second_select'] ?? '';
    $thirdSelect  =$_GET['third_select'] ?? '';
    $fourthSelect =$_GET['fourth_select'] ?? '';
  $zoneSubFilter  = $_GET['zone_sub_filter'] ?? ''; // 🌟 1. የዞን ንዑስ ማጣሪያውን እዚህ እንቀበላለን

    // 2. 1ኛው ሳጥን ሲመረጥ 2ተኛውን እና 4ተኛውን ዝርዝር እንቀበላለን
    if ($reportType === 'regional') {
        $secondList =$branchModel->getRegionalBranches();
        $fourthList =$branchModel->getRegionalBranches2();
    } 
    else if ($reportType === 'administrative' || $reportType === 'department') {
        $secondList =$branchModel->getAdministrativeBranches();  
        // 🌟 ዞኖች ወይም መምሪያዎች ሲመረጡ (ሁሉም ቢሆንም) ዞኖቹ በ 3ተኛው ሳጥን እንዲሞሉ
        $thirdList =$branchModel->getAdministrativeBranches();
    }

    // 3. 2ተኛው ሳጥን ተመርጦ ሲመጣ 3ተኛውን ዝርዝር እናመጣለን
    if (!empty($secondSelect) &&$secondSelect !== 'all') {
        if ($reportType === 'regional') {$thirdList = $branchModel->getRegional2ndBranches($secondSelect);
        } 
        else if ($reportType === 'administrative' || $reportType === 'department') {$thirdList = $branchModel->getAdministrative2ndBranches($secondSelect);
        }
    }

    // 4. ለ regional ካልሆነ በስተቀር 4ተኛው ሳጥን ራሱን ችሎ ከሞዴል ይሞላል
    if ($reportType !== 'regional') {
        $fourthList =$branchModel->getbytype(null);  
    }

    // =========================================================================
    // 5. 🎯 ለዚህኛው ሪፖርት የሚገባውን ዳታ በ getDynamicVehicleReport እንቀበላለን
    // =========================================================================
    $reportData =$branchModel->getDynamicVehicleReport([
        'report_type'   => $reportType,
        'second_select' => $secondSelect,
        'third_select'  => $thirdSelect,
        'fourth_select' => $fourthSelect,
        'zone_sub_filter' => $zoneSubFilter // 🌟 2. የዞን ንዑስ ማጣሪያውን ከ GET መረጃዎች ጋር እንቀበላለን
    ]);
    // =========================================================================

    // 🏷️ 6. የተመረጡትን ማጣሪያዎች ስም መለየት
    $reportTitle = 'የተሽከርካሪዎች ማጠቃለያ ሪፖርት';
    $filterPathParts = [];

    // ሀ) 1ኛው ማጣሪያ (Report Type)
    if ($reportType === 'regional') {$filterPathParts[] = 'የክልል ተጠሪ ሪፖርት';
    } elseif ($reportType === 'administrative' || $reportType === 'department') {$filterPathParts[] = 'የአስተዳደር ዞን/ወረዳ ሪፖርት';
    }

    // ለ) 2ኛው ማጣሪያ (Second Select)
    if (!empty($secondSelect) && $secondSelect !== 'all') {$targetSecondList = [];
        if ($reportType === 'regional') {
            $targetSecondList =$branchModel->getRegionalBranches();
        } elseif ($reportType === 'administrative' || $reportType === 'department') {
            $targetSecondList =$branchModel->getAdministrativeBranches();
        }

        foreach ($targetSecondList as$item) {
            $sId =$item['id'] ?? $item['branch_id'] ?? '';$sName = $item['name'] ?? $item['branch_name'] ?? '';
            if ($sId ==$secondSelect) {
                $filterPathParts[] =$sName;
                break;
            }
        }
    }

    // ሐ) 3ተኛው ማጣሪያ (Third Select - ዞን ሲመረጥ)
    if (!empty($thirdSelect) &&$thirdSelect !== 'all') {
        // `$thirdList` አሁን ሁልጊዜ የዞኖችን ዝርዝር ይዞ ስለሚመጣ ስሙን በቀላሉ እንለየዋለን
        foreach ($thirdList as$item) {
            $tId =$item['id'] ?? $item['branch_id'] ?? '';$tName = $item['name'] ?? $item['branch_name'] ?? '';
            if ($tId ==$thirdSelect) {
                $filterPathParts[] =$tName;
                break;
            }
        }
    }

    // መ) 4ተኛው ማጣሪያ (Fourth Select)
    if (!empty($fourthSelect) &&$fourthSelect !== 'all') {
        foreach ($fourthList as$item) {
            $fId =$item['id'] ?? $item['type_id'] ?? '';$fName = $item['name'] ?? $item['type_name'] ?? '';
            if ($fId ==$fourthSelect) {
                $filterPathParts[] =$fName;
                break;
            }
        }
    }

    // የተመረጡትን ክፍሎች በቅደም ተከተል ማቀናጀት
    if (!empty($filterPathParts)) {
        $reportTitle = 'የተሽከርካሪዎች ማጠቃለያ ሪፖርት - ' . implode(' > ', $filterPathParts);
    }

    // 7. የተሰበሰቡትን መረጃዎች ወደ ሪፖርት 1 ቪው እንልካለን
    $data = [
        'title'        => $reportTitle,
        'currentLang'  => $currentLang,
        'r_type'       => $reportType,
        'secondSelect' => $secondSelect,
        'thirdSelect'  => $thirdSelect,
        'fourthSelect' => $fourthSelect,
        'secondList'   => $secondList,
        'thirdList'    => $thirdList,
        'fourthList'   => $fourthList,
        'reportData'   => $reportData
    ];

    $this->renderPrintable('report1_view',$data);
}


public function report2controller(): void
{
    AuthHelper::checkRole(['system_admin', 'admin', 'mgmt', 'officer']);
    
    $currentLang = $_SESSION['lang'] ?? 'am';
    $branchModel = new dashboardmodel($this->db);

    $secondList  = [];
    $thirdList   = [];
    $fourthList  = [];
    $reportData  = [];

    // 1. መረጃዎች ከ URL (GET) መምጣቱን እንቀበላለን
    $reportType   = $_GET['report_type'] ?? '';
    $secondSelect = $_GET['second_select'] ?? '';
    $thirdSelect  = $_GET['third_select'] ?? '';
    $fourthSelect = $_GET['fourth_select'] ?? '';

    // 2. 1ኛው ሳጥን ሲመረጥ 2ተኛውን ዝርዝር እናመጣለን
    if ($reportType === 'regional') {
        $secondList = $branchModel->getRegionalBranches();
    } 
    else if ($reportType === 'administrative') {
        $secondList = $branchModel->getAdministrativeBranches(); 
    }

    // 3. 2ተኛው ሳጥን ተመርጦ ሲመጣ 3ተኛውን ዝርዝር እናመጣለን
    if (!empty($secondSelect)) {
        if ($reportType === 'regional') {
            $thirdList = $branchModel->getRegional2ndBranches($secondSelect);
        } 
        else if ($reportType === 'administrative') {
            $thirdList = $branchModel->getAdministrative2ndBranches($secondSelect);
        }
    }

    // 4. 4ተኛው ሳጥን (branch_type) ራሱን ችሎ ከሞዴል ይሞላል
    $fourthList = $branchModel->getbytype(null); 

    // =========================================================================
    // 5. 🎯 ለዚህኛው ሪፖርት (ሠ2) የአገልግሎት ዘመን የሚያሰላ አዲስ ፋንክሽን እንጠራለን
    // =========================================================================
    $reportData = $branchModel->getServiceYearReport([
        'report_type'   => $reportType,
        'second_select' => $secondSelect,
        'third_select'  => $thirdSelect,   // 🌟 ዞን ሲመረጥ የሚገባው 3ተኛው ማጣሪያ እዚህ ተጨምሯል
        'fourth_select' => $fourthSelect
    ]);
    // =========================================================================

    // 🏷️ 6. የተመረጡትን ማጣሪያዎች ስም እንደ ቅደም ተከተላቸው (ከላይ ወደ ታች) መለየት
    $reportTitle = 'የተሽከርካሪዎች አገልግሎት ዘመን ሪፖርት (ሠ2)';
    $filterPathParts = [];

    // ሀ) 1ኛው ማጣሪያ (Report Type)
    if ($reportType === 'regional') {
        $filterPathParts[] = 'የክልል ተጠሪ ሪፖርት';
    } elseif ($reportType === 'administrative') {
        $filterPathParts[] = 'የአስተዳደር ዞን/ወረዳ ሪፖርት';
    }

    // ለ) 2ኛው ማጣሪያ (Second Select)
    if (!empty($secondSelect)) {
        $targetSecondList = [];
        if ($reportType === 'regional') {
            $targetSecondList = $branchModel->getRegionalBranches();
        } elseif ($reportType === 'administrative') {
            $targetSecondList = $branchModel->getAdministrativeBranches();
        }

        foreach ($targetSecondList as $item) {
            $sId = $item['id'] ?? $item['branch_id'] ?? '';
            $sName = $item['name'] ?? $item['branch_name'] ?? '';
            if ($sId == $secondSelect) {
                $filterPathParts[] = $sName;
                break;
            }
        }
    }

    // ሐ) 3ተኛው ማጣሪያ (Third Select)
    if (!empty($thirdSelect)) {
        foreach ($thirdList as $item) {
            $tId = $item['id'] ?? $item['branch_id'] ?? '';
            $tName = $item['name'] ?? $item['branch_name'] ?? '';
            if ($tId == $thirdSelect) {
                $filterPathParts[] = $tName;
                break;
            }
        }
    }

    // መ) 4ተኛው ማጣሪያ (Fourth Select / Branch Type)
    if (!empty($fourthSelect)) {
        foreach ($fourthList as $item) {
            $fId = $item['id'] ?? $item['type_id'] ?? '';
            $fName = $item['name'] ?? $item['type_name'] ?? '';
            if ($fId == $fourthSelect) {
                $filterPathParts[] = $fName;
                break;
            }
        }
    }

    // የተመረጡትን ክፍሎች በቅደም ተከተል ማቀናጀት (በቀስት ምልክት " > " ተለያይተው እንዲወጡ)
    if (!empty($filterPathParts)) {
        $reportTitle = 'የተሽከርካሪዎች አገልግሎት ዘመን ሪፖርት (ሠ2) - ' . implode(' > ', $filterPathParts);
    }

    // 7. የተሰበሰቡትን መረጃዎች ወደ ሪፖርት 2 ቪው እንልካለን
    $data = [
        'title'       => $reportTitle,
        'currentLang' => $currentLang,
        'secondList'  => $secondList,
        'thirdList'   => $thirdList,
        'fourthList'  => $fourthList,
        'reportData'  => $reportData
    ];

    // ሪፖርት 2 ቪው ፋይልን (report2_view) እንጠራዋለን
    $this->renderPrintable('report2_view', $data);
}


public function bureausGraphView(): void
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // የተጠቃሚውን መብት እንፈትሻለን
    AuthHelper::checkRole(['system_admin', 'admin', 'mgmt', 'officer']);
    
    $currentLang = $_SESSION['lang'] ?? 'am';
    $branchModel = new DashboardModel($this->db);

    // አዲሱን የመኪና ብዛት እና የቢሮ መረጃ ከሞዴል እንጠራለን
    $reportData = $branchModel->bureausgraph();

    $data = [
        'title'       => 'የቢሮና ተጠሪ ተቋማት ቻርት ሪፖርት',
        'currentLang' => $currentLang,
        'reportData'  => $reportData // ዳታውን ወደ ቪው እናስተላልፋለን
    ];
    
    $this->render('bureau-analytics', $data);
}

public function zoneAnalyticsView(): void
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // የተጠቃሚውን መብት እንፈትሻለን
    AuthHelper::checkRole(['system_admin', 'admin', 'mgmt', 'officer']);
    
    $currentLang = $_SESSION['lang'] ?? 'am';
    $branchModel = new DashboardModel($this->db);

    // የአስተዳደራዊ መዋቅር የመኪና ብዛት መረጃን ከሞዴል እንጠራለን
    $reportData = $branchModel->zonegraph();

    $data = [
        'title'       => 'የአስተዳደራዊ መዋቅር ቻርት ሪፖርት',
        'currentLang' => $currentLang,
        'reportData'  => $reportData // ዳታውን ወደ ቪው እናስተላልፋለን
    ];
    
    $this->render('zone_analytics', $data);
}



public function vehicleStatusView(): void
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // የተጠቃሚውን መብት እንፈትሻለን
    AuthHelper::checkRole(['system_admin', 'admin', 'mgmt', 'officer']);
    
    $currentLang = $_SESSION['lang'] ?? 'am';
    $branchModel = new DashboardModel($this->db);

    // የተሽከርካሪ ሁኔታ መረጃውን ከሞዴል እንጠራለን
    $reportData = $branchModel->vehicleStatusGraph();

    $data = [
        'title'       => 'የተሽከርካሪ ሁኔታ ሪፖርት',
        'currentLang' => $currentLang,
        'reportData'  => $reportData // ዳታውን ወደ ቪው እናስተላልፋለን
    ];
    
    $this->render('vehicle-status-analytics', $data);
}


public function vehicleServiceView(): void
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // የተጠቃሚውን መብት እንፈትሻለን
    AuthHelper::checkRole(['system_admin', 'admin', 'mgmt', 'officer']);
    
    $currentLang = $_SESSION['lang'] ?? 'am';
    $branchModel = new DashboardModel($this->db);

    // የተሽከርካሪ ሁኔታ መረጃውን ከሞዴል እንጠራለን
    $reportData = $branchModel->vehiclesurviceGraph();

    $data = [
        'title'       => 'የተሽከርካሪ ሁኔታ ሪፖርት',
        'currentLang' => $currentLang,
        'reportData'  => $reportData // ዳታውን ወደ ቪው እናስተላልፋለን
    ];
    
    $this->render('vichelservice-all-analytics', $data);
}



public function vehicleAgeView()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // የተጠቃሚውን መብት እንፈትሻለን
    AuthHelper::checkRole(['system_admin', 'admin', 'mgmt', 'officer']);
    
    $currentLang = $_SESSION['lang'] ?? 'am';
    $branchModel = new DashboardModel($this->db);
   
    // መረጃውን ከሞዴል መቀበል
    $rawAgeData = $branchModel->vehicleAgeGraph()[0] ?? [];

    $reportData = [
        ['vichel_age' => '0 - 5 ዓመት', 'vehicle_count' => $rawAgeData['years_0_to_5'] ?? 0],
        ['vichel_age' => '6 - 15 ዓመት', 'vehicle_count' => $rawAgeData['years_6_to_15'] ?? 0],
        ['vichel_age' => '16 - 25 ዓመት', 'vehicle_count' => $rawAgeData['years_16_to_25'] ?? 0],
        ['vichel_age' => '26 - 35 ዓመት', 'vehicle_count' => $rawAgeData['years_26_to_35'] ?? 0],
        ['vichel_age' => '36 እና ከዚያ በላይ', 'vehicle_count' => $rawAgeData['years_36_plus'] ?? 0],
    ]; // እዚህ ላይ የነበረው ትርፍ ኮድ ተወግዶ ሰረዝ እና ማቀፊያ ተስተካክሏል

    $this->render('vichelage-all-analytics', [
        'currentLang' => $currentLang,
        'reportData' => $reportData
    ]);
}




public function dashboardSummaryReport()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // የተጠቃሚውን መብት እንፈትሻለን
    AuthHelper::checkRole(['system_admin', 'admin', 'mgmt', 'officer']);
    
    $currentLang = $_SESSION['lang'] ?? 'am';
    $branchModel = new DashboardModel($this->db);
    
    // 1. አጠቃላይ ማጠቃለያ (Summary) መረጃዎችን ከሞዴል መቀበል
    $summary = $branchModel->getDashboardSummary();

    // 2. የተሽከርካሪ አገልግሎት ዓይነት መረጃ ከሞዴል መቀበል
    $serviceData = $branchModel->getVehiclesByServiceType();

   // 3. የተሽከርካሪ ወቅታዊ ሁኔታ መረጃ ከሞዴል መቀበል
    $statusData = $branchModel->getVehiclesByStatus();
    
    // ለጃቫስክሪፕት እንዲመች መረጃውን ማዘጋጀት
    $statusLabels = [];
    $statusCounts = [];
    
    foreach ($statusData as $row) {
        $statusLabels[] = $row['CallerName'];
        $statusCounts[] = (int)$row['vehicle_count'];
    }

    // 4. የተሽከርካሪ ዕድሜ መረጃ (እርስዎ ባስለመዱት አሰራር የተዋቀረ)
    $rawAgeData = $branchModel->vehicleAgeGraph()[0] ?? [];
    $reportData = [
        ['vichel_age' => '0 - 5 ዓመት', 'vehicle_count' => $rawAgeData['years_0_to_5'] ?? 0],
        ['vichel_age' => '6 - 15 ዓመት', 'vehicle_count' => $rawAgeData['years_6_to_15'] ?? 0],
        ['vichel_age' => '16 - 25 ዓመት', 'vehicle_count' => $rawAgeData['years_16_to_25'] ?? 0],
        ['vichel_age' => '26 - 35 ዓመት', 'vehicle_count' => $rawAgeData['years_26_to_35'] ?? 0],
        ['vichel_age' => '36 እና ከዚያ በላይ', 'vehicle_count' => $rawAgeData['years_36_plus'] ?? 0],
    ];

    // 5. መኪናዎቹ በተመረቱበት ዓ.ም. መረጃ ከሞዴል መቀበል እና ማቀናጀት
    $rawTrendData = $branchModel->getMonthlyTrend();
    $trendData = [];
    foreach ($rawTrendData as $row) {
        $trendData[] = [
            'manufactured_year' => $row['manufactured_year'],
            'activity_count'    => (int)$row['activity_count']
        ];
    }
// 6.ምንም አይነት ተሽከርካሪ የሌላቸው ተቋማት
    $rawNoVehicleData = $branchModel->novichelregiongraph($id ?? null);
    $noVehicleData = [];
    if (!empty($rawNoVehicleData)) {
        foreach ($rawNoVehicleData as $row) {
            $noVehicleData[] = [
                'name'         => $row['Name'],
                'caller_name'  => $row['CallerName'],
                'vehicle_count'=> (int)$row['vehicle_count']
            ];
        }
    }



$rawTopVehiclesData = $branchModel->topvehiclesregiongraph($id ?? null);
$topVehiclesData = [];
if (!empty($rawTopVehiclesData)) {
    foreach ($rawTopVehiclesData as $row) {
        $topVehiclesData[] = [
            'name'         => $row['Name'],
            'caller_name'  => $row['CallerName'],
            'vehicle_count'=> (int)$row['vehicle_count']
        ];
    }
}

$rawPurchaseData = $branchModel->vehiclePurchaseYearGraph($id ?? null);
$purchaseYearData = [];
if (!empty($rawPurchaseData)) {
    foreach ($rawPurchaseData as $row) {
        $purchaseYearData[] = [
            'purchase_year' => $row['purchase_year'],
            'vehicle_count' => (int)$row['vehicle_count']
        ];
    }
}


$rawZoneWorkingData = $branchModel->zoneWorkingVehiclesGraph($id ?? null);
$zoneWorkingData = [];
if (!empty($rawZoneWorkingData)) {
    foreach ($rawZoneWorkingData as $row) {
        $zoneWorkingData[] = [
            'zone_name'     => $row['zone_name'],
            'vehicle_count' => (int)$row['vehicle_count']
        ];
    }
}

$rawZoneDamagedData = $branchModel->zoneDamagedVehiclesGraph($id ?? null);
$zoneDamagedData = [];
if (!empty($rawZoneDamagedData)) {
    foreach ($rawZoneDamagedData as $row) {
        $zoneDamagedData[] = [
            'zone_name'     => $row['zone_name'],
            'vehicle_count' => (int)$row['vehicle_count']
        ];
    }
}


$rawMetroData = $branchModel->metropolitanVehiclesGraph($id ?? null);
$metropolitanData = [];
if (!empty($rawMetroData)) {
    foreach ($rawMetroData as $row) {
        $metropolitanData[] = [
            'metro_name'    => $row['metro_name'],
            'vehicle_count' => (int)$row['vehicle_count']
        ];
    }
}



    // ሁሉንም መረጃዎች ወደ ቪው መጫን (exit ስለሌለ አሁን በቀጥታ ወደ ቪው ይተላለፋል)
    $this->renderprintable('additionalanalysisgraph', [
        'summary'       => $summary,
        'serviceData'   => $serviceData,
        'statusData'    => $statusData,
        'reportData'    => $reportData,
        'trendData'     => $trendData,
        'noVehicleData' => $noVehicleData,
        'topVehiclesData' => $topVehiclesData,
        'purchaseYearData' => $purchaseYearData,
        'zoneWorkingData' => $zoneWorkingData,
        'zoneDamagedData' => $zoneDamagedData,
        'metropolitanData' => $metropolitanData
       
        
    ]);
}









}