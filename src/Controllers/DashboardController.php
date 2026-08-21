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

    $reportType   = $_GET['report_type'] ?? '';
    $secondSelect = $_GET['second_select'] ?? '';
    $thirdSelect  = $_GET['third_select'] ?? '';
    $fourthSelect = $_GET['fourth_select'] ?? ''; 

    // 2. የተመረጠውን report_type መሠረት በማድረግ 2ተኛውን ዝርዝር እናመጣለን
    if ($reportType === 'regional') {
        $secondList = $branchModel->getRegionalBranches();
    } 
    else if ($reportType === 'agency') {
        $secondList = $branchModel->getRegionalBranches(); 
    }
    else if ($reportType === 'department') {
        // መምሪያ ሲመረጥ መጀመሪያ የሚወጡት የመምሪያዎች ዝርዝር ናቸው
        $secondList = $branchModel->getRegional2ndMemriya(null); // ወይም መምሪያዎችን ብቻ የሚያመጣ ፋንክሽን
    }
    else if ($reportType === 'zone') {
        // ዞን ሲመረጥ ቀጥታ አስተዳደራዊ ዞኖችን እናመጣለን
        $secondList = $branchModel->getAdministrativeBranches(); 
    }
    else if ($reportType === 'wereda') {
        // ወረዳ ሲመረጥ መጀመሪያ የሚወጡት ዞኖች (አስተዳደራዊ መዋቅሮች) ናቸው
        $secondList = $branchModel->getAdministrativeBranches(); 
    }

    // 3. 2ተኛው ሳጥን ተመርጦ ከመጣ 3ተኛውን እናመጣለን
    if (!empty($secondSelect) && $secondSelect !== 'all') {
        if ($reportType === 'regional' || $reportType === 'agency') {
            $thirdList = $branchModel->getRegional2ndBranches($secondSelect);
        }
        else if ($reportType === 'department') {
            // መምሪያ ተመርጦ ሲመጣ, በየትኛው ዞን ውስጥ እንዳለ እናመጣለን
            $thirdList = $branchModel->getAdministrativeBranches($secondSelect);
        }
        else if ($reportType === 'wereda') {
            // ዞኑ ተመርጦ ሲመጣ, በዛ ስር ያሉትን ወረዳዎች እናመጣለን
            $thirdList = $branchModel->getAdministrative2ndBranches($secondSelect);
        }
        else if ($reportType === 'calls') {
        // በመጠሪያ ስማቸው ሲመረጥ የቅርንጫፍ አይነት ዝርዝር እናመጣለን
        $secondList = $branchModel->getbytype(null); 
    }
    }

    // 4. አራተኛው ሳጥን (እንደ ደረጃቸው/ዓይነት)
    $fourthList = $branchModel->getbytype(null); 

    $data = [
        'title'        => 'የሪፖርት ማጣሪያ ፎርም',
        'currentLang'  => $currentLang,
        'r_type'       => $reportType,       
        'secondSelect' => $secondSelect,    
        'thirdSelect'  => $thirdSelect,
        'secondList'   => $secondList,
        'thirdList'    => $thirdList,
        'fourthList'   => $fourthList
    ];

    $this->render('report_view', $data);
}

public function getSecondList()
{
    $type = $_GET['type'] ?? '';
    $branchModel = new dashboardmodel($this->db);
    $data = [];

    if ($type === 'regional') {
        // የክልልና ተጠሪ ቢሮዎችን ከዳታቤዝ የሚያመጣበት ლოጂክ
        $data = $branchModel->getRegionalOffices(); 
    } elseif ($type === 'administrative') {
        // የዞን ቢሮዎችን ከዳታቤዝ የሚያመጣበት ლოጂክ
        $data = $branchModel->getZones(); 
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

public function getThirdList()
{
    $parentId = $_GET['parent_id'] ?? null;
    $branchModel = new dashboardmodel($this->db);
    $data = [];

    if ($parentId) {
        // በ 2ተኛው ሳጥን መታወቂያ ታግዞ ተዛማጅ ወረዳዎችን/ተጠሪዎችን የሚያመጣ ፋንክሽን
        $data = $branchModel->getSubBranchesByParent($parentId);
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}




public function report1controller(): void
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

    // --- 🔍 የዲባግ ማረጋገጫ ---
    echo "<div style='background:#111; color:#0f0; padding:12px; margin:10px; font-family:monospace; z-index:9999; position:relative;'>";
    echo "<h3>--- ዴቨሎፐር ዲባግ ስክሪን (Report 1) ---</h3>";
    echo "የተቀበለው ሪኬስት ዓይነት (Request Method): " . $_SERVER['REQUEST_METHOD'] . "<br>";
    echo "የተያዘው report_type ቫልዩ: [ <b>" . ($reportType ?: 'ባዶ ነው (አልተመረጠም)') . "</b> ]<br>";
    echo "የተያዘው second_select ቫልዩ: [ <b>" . ($secondSelect ?: 'ባዶ ነው') . "</b> ]<br>";
    echo "የተያዘው third_select ቫልዩ: [ <b>" . ($thirdSelect ?: 'ባዶ ነው') . "</b> ]<br>";
    echo "የተያዘው fourth_select ቫልዩ: [ <b>" . ($fourthSelect ?: 'ባዶ ነው') . "</b> ]<br>";
    echo "</div>";
    // ----------------------------------------------------

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

    // 5. የሪፖርት ማምጫው ሎጂክ
    if (!empty($reportType)) {
        $reportData = $branchModel->getVehicleReportAdvanced([
            'report_type'   => $reportType,
            'second_select' => $secondSelect,
            'third_select'  => $thirdSelect,
            'fourth_select' => $fourthSelect
        ]);
    }

    // =========================================================================
    // 🏷️ 6. የተመረጡትን ማጣሪያዎች ስም እንደ ቅደም ተከተላቸው (ከላይ ወደ ታች) መለየት
    // =========================================================================
    $reportTitle = 'የተሽከርካሪዎች ማጠቃለያ ሪፖርት';
    $filterPathParts = [];

    // ሀ) 1ኛው ማጣሪያ (Report Type)
    if ($reportType === 'regional') {
        $filterPathParts[] = 'የክልል ተጠሪ ሪፖርት';
    } elseif ($reportType === 'administrative') {
        $filterPathParts[] = 'የአስተዳደር ዞን/ወረዳ ሪፖርት';
    }

    // ለ) 2ኛው ማጣሪያ (Second Select) - ከ regional ወይም ከ administrative ዝርዝር ውስጥ ስሙን መፈለግ
    if (!empty($secondSelect)) {
        // የትኛውን ዝርዝር መጠቀም እንዳለብን በ reportType እንለያለን
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

    // ሐ) 3ተኛው ማጣሪያ (Third Select) - ከሁለተኛው ደረጃ ዝርዝር ውስጥ ስሙን መፈለግ
    if (!empty($thirdSelect)) {
        // `$thirdList` ቀደም ብሎ በኮዱ ስለተጫነ በቀጥታ መጠቀም እንችላለን
        foreach ($thirdList as $item) {
            $tId = $item['id'] ?? $item['branch_id'] ?? '';
            $tName = $item['name'] ?? $item['branch_name'] ?? '';
            if ($tId == $thirdSelect) {
                $filterPathParts[] = $tName;
                break;
            }
        }
    }

    // መ) 4ተኛው ማጣሪያ (Fourth Select / Branch Type) ስም ከ $fourthList መፈለግ
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
        $reportTitle = 'የተሽከርካሪዎች ማጠቃለያ ሪፖርት - ' . implode(' > ', $filterPathParts);
    }
    // =========================================================================
    // 7. የተሰበሰቡትን መረጃዎች ወደ ሪፖርት 1 ቪው እንልካለን
    $data = [
        'title'        => $reportTitle,
        'currentLang'  => $currentLang,
        'secondList'   => $secondList,
        'thirdList'    => $thirdList,
        'fourthList'   => $fourthList,
        'reportData'   => $reportData
    ];

    // ሪፖርት 1 ቪው ፋይልን እንጠራዋለን
    $this->renderPrintable('report1_view', $data);
}
}