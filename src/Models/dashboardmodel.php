<?php

namespace App\Models;

use PDO;

class dashboardmodel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function countbureaus(?string $id = null): int
{
        $sql = "SELECT COUNT(id) AS total 
                FROM branches 
                WHERE is_active = 1 
                  AND functional_path IS NOT NULL 
                  AND functional_path != ''";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? (int)$result['total'] : 0;
}

public function countzonal(?string $id = null): int
{
        $sql = "SELECT COUNT(id) AS zonal_total 
                FROM branches 
                WHERE is_active = 1 
                  AND admin_path IS NOT NULL 
                  AND admin_path != '' 
                  AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? (int)$result['zonal_total'] : 0;
}

public function countvichel(?string $id = null): int
{
        $sql = "SELECT COUNT(id) AS vichel_total 
                FROM vehicles 
                ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? (int)$result['vichel_total'] : 0;
}

// የክልል ቢሮዎችን ከ functional_path እና ሌሎች መስፈርቶች ጋር ለማምጣት
public function getRegionalBranches(): array
{
    // ምሳሌ፡ የክልል ቢሮዎችን ብቻ ለመለየት (እንደ ዳታቤዝ አወቃቀርዎ type ወይም parent_id ሊያስፈልግ ይችላል)
    $sql = "SELECT id, name 
            FROM branches 
            WHERE is_active = 1 and level = 1
              AND functional_path IS NOT NULL 
              AND functional_path != '' 
            ORDER BY name ASC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



public function getRegionalBranches2(): array
{
    // ምሳሌ፡ የክልል ቢሮዎችን ብቻ ለመለየት (እንደ ዳታቤዝ አወቃቀርዎ type ወይም parent_id ሊያስፈልግ ይችላል)
    $sql = "SELECT id, name 
            FROM branches 
            WHERE is_active = 1 and level = 1
              AND functional_path IS NOT NULL 
              AND functional_path != '' 
            ORDER BY name ASC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getAdministrativeBranches(): array
{
    // ምሳሌ፡ የክልል ቢሮዎችን ብቻ ለመለየት (እንደ ዳታቤዝ አወቃቀርዎ type ወይም parent_id ሊያስፈልግ ይችላል)
    $sql = "SELECT id, name 
            FROM branches 
            WHERE is_active = 1 and level = 1
              AND admin_path IS NOT NULL 
              AND admin_path != '' 
            ORDER BY name ASC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getRegional2ndMemriya($parentId): array
{
    // የተመረጠውን የዞን ID (parent_id ወይም functional_parent_id) በመጠቀም ማጣሪያውን እናካትታለን
    $sql = "SELECT id, name 
            FROM branches 
            WHERE is_active = 1 
              AND `level` = 2 
              AND `branch_type` = 'memriya' 
              AND `functional_path` IS NOT NULL 
              AND `functional_path` != '' 
            ORDER BY name ASC;";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}







public function getRegional2ndBranches($parentId): array
{
    // functional_parent_id በመጠቀም ከተመረጠው ወላጅ ስር ያሉትን እና ልቭል 2 የሆኑትን ብቻ እናመጣለን
    $sql = "SELECT id, name 
            FROM branches 
            WHERE is_active = 1 
              AND level = 2 
              AND functional_parent_id = :parent_id 
              AND functional_path IS NOT NULL 
              AND functional_path != '' 
            ORDER BY name ASC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['parent_id' => $parentId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getAdministrative2ndBranches($parentId): array
{
    // admin_parent_id በመጠቀም ከተመረጠው ወላጅ (Parent) ስር ያሉትን እና ልቭል 2 የሆኑትን ብቻ እናመጣለን
    $sql = "SELECT id, name 
            FROM branches 
            WHERE is_active = 1 
              AND level = 2 
              AND admin_parent_id = :parent_id 
              AND admin_path IS NOT NULL 
              AND admin_path != '' 
            ORDER BY name ASC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['parent_id' => $parentId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getbytype($parentId): array
{
    // ምሳሌ፡ ለቢሮ ተጠሪ የሆኑ ተቋማትን
    $sql = "SELECT DISTINCT branch_type FROM branches
            WHERE is_active = 1 
              AND branch_type IS NOT NULL 
              AND branch_type != '' 
            ORDER BY branch_type ASC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getDynamicVehicleReport($filters): array
{
    // 1. መጀመሪያ በ cartypelist ውስጥ ያሉትን የကား አይነቶች በሙሉ እናመጣለን
    $typesStmt = $this->db->query("SELECT car_type_id AS id, type_name as cartype FROM view_car_details ORDER BY id ASC");
    $carTypes = $typesStmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. የ SQL ኪዩሪውን መሠረት እንገነባለን
    $sql = "SELECT 
        b.id AS branch_id,
        b.uuid AS branch_uuid,
        b.name AS branch_name,
        b.branch_type,
        COUNT(v.id) AS total_vehicles,
        SUM(CASE WHEN v.vehicle_status = 'active' THEN 1 ELSE 0 END) AS status_active,
        SUM(CASE WHEN v.vehicle_status = 'out_of_service' THEN 1 ELSE 0 END) AS status_out_of_service,
        SUM(CASE WHEN v.vehicle_status = 'lost' THEN 1 ELSE 0 END) AS status_lost,
        SUM(CASE WHEN v.vehicle_status = 'destroyed' THEN 1 ELSE 0 END) AS status_destroyed,
        SUM(CASE WHEN v.vehicle_status = 'disposed' THEN 1 ELSE 0 END) AS status_disposed";

    foreach ($carTypes as $type) {
        $typeId = (int)$type['id'];
        $sql .= ", SUM(CASE WHEN v.vehicle_type = {$typeId} THEN 1 ELSE 0 END) AS vehicle_type_{$typeId}";
    }

    $sql .= " FROM branches b
              LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1
              WHERE b.is_active = 1";

    $params = [];

    // 3. ሬዲዮ በተኑ (report_type) የተመረጠ መሆኑን ማረጋገጥ
    $r_type = trim($filters['report_type'] ?? '');
    $secondSelect = $filters['second_select'] ?? 'all';

    // ሬዲዮ በተኑ ከተመረጠ ብቻ ማጣሪያውን እንጨምራለን፤ ካልተመረጠ (ባዶ ከሆነ) ይዘለላል (ሁሉንም ያመጣል)
    if ($r_type !== '' && $r_type !== 'all') {
        switch ($r_type) {
            case 'regional': 
                $sql .= " AND b.level = 1 AND b.functional_path IS NOT NULL AND b.functional_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'agency': 
                $sql .= " AND b.level = 2 AND b.functional_path IS NOT NULL AND b.functional_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.functional_parent_id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'department': 
                $sql .= " AND b.level = 2 AND b.branch_type = 'memriya' AND b.functional_path IS NOT NULL AND b.functional_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.functional_parent_id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'zone': 
                $sql .= " AND b.level = 1 AND b.admin_path IS NOT NULL AND b.admin_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'wereda': 
                $sql .= " AND b.level = 2 AND b.admin_path IS NOT NULL AND b.admin_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.admin_parent_id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'calls': 
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.branch_type = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;
        }
    }

    // 4. ሌሎች ተጨማሪ ማጣሪያዎች (third_select እና fourth_select)
    if (!empty($filters['third_select']) && $filters['third_select'] !== 'all') {
        $sql .= " AND b.id = :third_select";
        $params['third_select'] = $filters['third_select'];
    }

    if (!empty($filters['fourth_select']) && $filters['fourth_select'] !== 'all') {
        // የክልል ተጠሪ ሪፖርት ሲሆን አራተኛው ሳጥን የቢሮ ID ስለሚልክ በ b.id እናጣራለን
        if ($r_type === 'regional') {
            $sql .= " AND b.id = :fourth_select";
        } else {
            // ለሌሎች የሪፖርት አይነቶች branch_type ሆኖ መቀጠል ከፈለገ
            $sql .= " AND b.branch_type = :fourth_select";
        }
        $params['fourth_select'] = $filters['fourth_select'];
    }

    $sql .= " GROUP BY b.id, b.uuid, b.name, b.branch_type ORDER BY b.name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    
    return [
        'car_types' => $carTypes,
        'report_data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ];
}


public function getServiceYearReport($filters): array
{
    // 1. የዘንድሮውን ዓመት እናገኛለን (የተሽከርካሪውን ዕድሜ ለማስላት)
    $currentYear = date('Y');

    // 2. የ SQL ኪዩሪውን መሠረት እንገነባለን
    // ማስታወሻ፡ በዳታቤዝዎ ውስጥ የተሽከርካሪው የተመረተበት ዓመት የተመዘገበው በ `manufactured_year` ነው ብለን አስበናል። 
    // የእርስዎ ኮለም ስም የተለየ ከሆነ (ለምሳሌ፡ `model_year` ወይም `purchase_year`) እባክዎ ከታች ባለው ኮድ ላይ ይቀይሩት።
    $sql = "SELECT 
        b.id AS branch_id,
        b.uuid AS branch_uuid,
        b.name AS branch_name,
        b.branch_type,
        
        -- የአገልግሎት ዘመን ምድቦች (Service Years Categories)
        SUM(CASE WHEN ($currentYear - v.manufactured_year) <= 5 THEN 1 ELSE 0 END) AS years_0_to_5,
        SUM(CASE WHEN ($currentYear - v.manufactured_year) BETWEEN 6 AND 15 THEN 1 ELSE 0 END) AS years_6_to_15,
        SUM(CASE WHEN ($currentYear - v.manufactured_year) BETWEEN 16 AND 25 THEN 1 ELSE 0 END) AS years_16_to_25,
        SUM(CASE WHEN ($currentYear - v.manufactured_year) BETWEEN 26 AND 35 THEN 1 ELSE 0 END) AS years_26_to_35,
        SUM(CASE WHEN ($currentYear - v.manufactured_year) >= 36 THEN 1 ELSE 0 END) AS years_36_plus
        
        FROM branches b
        LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1
        WHERE b.is_active = 1";

    $params = [];

    // 3. ሬዲዮ በተኑ (report_type) የተመረጠ መሆኑን ማረጋገጥ
    $r_type = trim($filters['report_type'] ?? '');
    $secondSelect = $filters['second_select'] ?? 'all';

    // ሬዲዮ በተኑ ከተመረጠ ብቻ ማጣሪያውን እንጨምራለን፤ ካልተመረጠ (ባዶ ከሆነ) ይዘለላል (ሁሉንም ያመጣል)
    if ($r_type !== '' && $r_type !== 'all') {
        switch ($r_type) {
            case 'regional': 
                $sql .= " AND b.level = 1 AND b.functional_path IS NOT NULL AND b.functional_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'agency': 
                $sql .= " AND b.level = 2 AND b.functional_path IS NOT NULL AND b.functional_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.functional_parent_id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'department': 
                $sql .= " AND b.level = 2 AND b.branch_type = 'memriya' AND b.functional_path IS NOT NULL AND b.functional_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.functional_parent_id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'zone': 
                $sql .= " AND b.level = 1 AND b.admin_path IS NOT NULL AND b.admin_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'wereda': 
                $sql .= " AND b.level = 2 AND b.admin_path IS NOT NULL AND b.admin_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.admin_parent_id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'calls': 
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.branch_type = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;
        }
    }

    // 4. ሌሎች ተጨማሪ ማጣሪያዎች (third_select እና fourth_select)
    if (!empty($filters['third_select']) && $filters['third_select'] !== 'all') {
        $sql .= " AND b.id = :third_select";
        $params['third_select'] = $filters['third_select'];
    }

    if (!empty($filters['fourth_select']) && $filters['fourth_select'] !== 'all') {
        $sql .= " AND b.branch_type = :fourth_select";
        $params['fourth_select'] = $filters['fourth_select'];
    }

    // መረጃውን በቅርንጫፍ (Branch) ማሰባሰብ
    $sql .= " GROUP BY b.id, b.uuid, b.name, b.branch_type ORDER BY b.name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    
    // ለ report2_view በቀጥታ የሚነበበውን ዳታ እንመልሳለን
    return [
        'report_data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ];
}

public function getVehicleReportAdvanced($filters): array
{
    // 1. መጀመሪያ በ cartypelist ውስጥ ያሉትን የကား አይነቶች በሙሉ በዲናሚክ እናመጣለን
    $typesStmt = $this->db->query("SELECT id, cartype FROM cartypelist ORDER BY id ASC");
    $carTypes = $typesStmt->fetchAll(\PDO::FETCH_ASSOC);

    // 2. የ SQL ኪዩሪውን መሠረት በ $sql = እንጂ በ .= መጀመር የለብንም!
    $sql = "SELECT 
        b.id AS branch_id,
        b.uuid AS branch_uuid,
        b.name AS branch_name,
        b.branch_type,
        COUNT(v.id) AS total_vehicles,
        SUM(CASE WHEN v.vehicle_status = 'active' THEN 1 ELSE 0 END) AS status_active,
        SUM(CASE WHEN v.vehicle_status = 'out_of_service' THEN 1 ELSE 0 END) AS status_out_of_service,
        SUM(CASE WHEN v.vehicle_status = 'lost' THEN 1 ELSE 0 END) AS status_lost,
        SUM(CASE WHEN v.vehicle_status = 'destroyed' THEN 1 ELSE 0 END) AS status_destroyed,
        SUM(CASE WHEN v.vehicle_status = 'disposed' THEN 1 ELSE 0 END) AS status_disposed";

    // እያንዳንዱን የካር ዓይነት በ ID አማካኝነት በዲናሚክ SUM(CASE...) እንጨምራለን
    foreach ($carTypes as $type) {
        $typeId = (int)$type['id'];
        $sql .= ", SUM(CASE WHEN ct.type_name = {$typeId} THEN 1 ELSE 0 END) AS vehicle_type_{$typeId}";
    }

    $sql .= " FROM branches b
              LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1
              LEFT JOIN car_type ct ON v.vehicle_type = ct.id
              WHERE b.is_active = 1";

    $params = [];

    // 3. ማጣሪያዎችን (Filters) ማስተናገድ
    $reportType   = $filters['report_type'] ?? '';
    $secondSelect = $filters['second_select'] ?? '';
    $thirdSelect  = $filters['third_select'] ?? '';
    $fourthSelect = $filters['fourth_select'] ?? '';

    if (!empty($secondSelect) && $secondSelect !== 'all') {
        if ($reportType === 'regional') {
            $sql .= " AND b.functional_parent_id = :second_select";
        } else {
            $sql .= " AND b.admin_parent_id = :second_select";
        }
        $params['second_select'] = $secondSelect;
    }

    if (!empty($thirdSelect) && $thirdSelect !== 'all') {
        $sql .= " AND b.id = :third_select";
        $params['third_select'] = $thirdSelect;
    }

    if (!empty($fourthSelect) && $fourthSelect !== 'all') {
        $sql .= " AND b.branch_type = :fourth_select";
        $params['fourth_select'] = $fourthSelect;
    }

    $sql .= " GROUP BY b.id, b.name, b.branch_type ORDER BY b.name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    
    return [
        'car_types' => $carTypes,
        'report_data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
    ];
}



public function bureausgraph(?string $id = null): array
{
    $sql = "SELECT 
                b.name AS Name, 
                COALESCE(bt.type_in_am, b.branch_type) AS CallerName, 
                COUNT(v.id) AS vehicle_count
            FROM branches b
            LEFT JOIN branch_type bt ON b.branch_type = bt.type_in_eng
            LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1
            WHERE b.is_active = 1  
              AND b.functional_path IS NOT NULL 
              AND b.functional_path != ''
            GROUP BY b.id, b.name, b.branch_type, bt.type_in_am";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



public function zonegraph(): array
{
    $sql = "SELECT 
                b.id,
                b.name AS Name, 
                COALESCE(bt.type_in_am, b.branch_type) AS CallerName, 
                COUNT(v.id) AS vehicle_count
            FROM branches b
            LEFT JOIN branch_type bt ON TRIM(b.branch_type) = TRIM(bt.type_in_eng)
            LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1
            WHERE b.is_active = 1 
              AND b.admin_path IS NOT NULL 
              AND b.admin_path != '' 
            GROUP BY b.id, b.name, b.branch_type, bt.type_in_am
            ORDER BY b.name ASC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function vehicleStatusGraph(): array
{
    $sql = "SELECT 
                CASE 
                    WHEN LOWER(TRIM(vehicle_status)) = 'active' THEN 'በስራ ላይ ያለ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'lost' THEN 'የጠፋ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'destroyed' THEN 'የወደመ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'disposed' THEN 'የተወገደ'
                    ELSE COALESCE(vehicle_status, 'ለይቶ የማይታወቅ')
                END AS CallerName, 
                COUNT(id) AS vehicle_count
            FROM vehicles
            WHERE is_active = 1 
              AND deleted_at IS NULL
            GROUP BY vehicle_status
            ORDER BY vehicle_count DESC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}