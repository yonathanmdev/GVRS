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
              AND branch_type != 'memriya' 
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

    // 2. ማጣሪያዎችን እንለያለን
    $r_type = trim($filters['report_type'] ?? '');
    $secondSelect = $filters['second_select'];
    $thirdSelect = $filters['third_select'];
    $zoneSubFilter = $filters['zone_sub_filter']; // የዞን ንዑስ ማጣሪያ

    // 3. የ SQL ኪዩሪውን መሠረት እንገነባለን
    $sql = "SELECT 
        b.id AS branch_id,
        b.uuid AS branch_uuid,
        b.name AS branch_name,
        b.branch_type,
        COUNT(DISTINCT v.id) AS total_vehicles,
        COUNT(DISTINCT CASE WHEN v.vehicle_status = 'active' THEN v.id END) AS status_active,
        COUNT(DISTINCT CASE WHEN v.vehicle_status = 'out_of_service' THEN v.id END) AS status_out_of_service,
        COUNT(DISTINCT CASE WHEN v.vehicle_status = 'lost' THEN v.id END) AS status_lost,
        COUNT(DISTINCT CASE WHEN v.vehicle_status = 'destroyed' THEN v.id END) AS status_destroyed,
        COUNT(DISTINCT CASE WHEN v.vehicle_status = 'disposed' THEN v.id END) AS status_disposed";

    foreach ($carTypes as $type) {
        $typeId = (int)$type['id'];
        $sql .= ", COUNT(DISTINCT CASE WHEN v.vehicle_type = {$typeId} THEN v.id END) AS vehicle_type_{$typeId}";
    }

    $sql .= " FROM branches b";

    $params = [];

   // 🌟 4. የዞን ሪፖርት ሲመረጥ ከንዑስ ማጣሪያው ጋር የተጣጣመ የ JOIN ሎጂክ
    if ($r_type === 'zone') {
        if ($zoneSubFilter === 'memriya_only') {
            $sql .= " LEFT JOIN vehicles v ON v.branch_id = b.id AND v.is_active = 1";
        } 
        elseif ($zoneSubFilter === 'wereda_only') {
            $sql .= " LEFT JOIN vehicles v ON v.branch_id = b.id AND v.is_active = 1";
        } 
        else {
            // ሁሉም ሲመረጥ ከዞን ID ጋር በቀጥታ እንዲያያዝ
            $sql .= " LEFT JOIN vehicles v ON v.zone_id = b.id AND v.is_active = 1";
        }
    }
    else if ($r_type === 'department') {
        if (!empty($thirdSelect) && $thirdSelect !== 'all') {
            $sql .= " LEFT JOIN vehicles v ON v.branch_id = b.id AND v.is_active = 1 AND v.zone_id = :third_select_zone";
            $params['third_select_zone'] = $thirdSelect;
        } else {
            $sql .= " LEFT JOIN vehicles v ON v.branch_id = b.id AND v.is_active = 1";
        }
    } 
    else {
        $sql .= " LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1";
    }

    if ($r_type === 'department') {
        $sql .= " WHERE b.is_active = 1 AND b.branch_type = 'memriya'";
    } else {
        $sql .= " WHERE b.is_active = 1";
    }

    // 5. ሌሎች ማጣሪያዎችን መለየት
    if ($r_type !== '' && $r_type !== 'all') {
        switch ($r_type) {
            case 'regional': 
                $sql .= " AND (b.functional_path IS NOT NULL AND b.functional_path != '')";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND (
                        b.id = :sec_id 
                        OR b.functional_parent_id = :sec_parent 
                        OR b.functional_path LIKE CONCAT((SELECT admin_path FROM branches WHERE id = :sec_path_id), '/%')
                        OR b.functional_path LIKE CONCAT('%/', :sec_path_id, '/%')
                    )";
                    $params['sec_id'] = $secondSelect;
                    $params['sec_parent'] = $secondSelect;
                    $params['sec_path_id'] = $secondSelect;
                }
                elseif ($secondSelect === 'all') {
                    $sql .= " AND b.level = '1'";
                }
                break;

            case 'agency': 
                $sql .= " AND b.branch_type != 'memriya' AND b.functional_parent_id IS NOT NULL AND (b.functional_path IS NOT NULL AND b.functional_path != '')";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND (b.id = :second_select OR b.functional_parent_id = :second_select)";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'department': 
                $sql .= " AND b.level = 2 AND b.functional_path IS NOT NULL AND b.functional_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

          case 'zone': 
                if ($zoneSubFilter === 'memriya_only') {
                    // መምሪያ ብቻ
                    $sql .= " AND b.level = 2 AND b.branch_type = 'memriya'";
                } 
                elseif ($zoneSubFilter === 'wereda_only') {
                    // ወረዳ ብቻ
                    $sql .= " AND b.level = 2 AND b.admin_path IS NOT NULL AND b.admin_path != ''";
                } 
                else {
                    // ሁሉም (ዞን ራሱ - level 1)
                    $sql .= " AND b.level = 1 AND b.admin_path IS NOT NULL AND b.admin_path != ''";
                }
                
                // ሁለተኛው ማጣሪያ (የተመረጠው ዞን ID) ካለ
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND v.zone_id = :zone_filter_id";
                    $params['zone_filter_id'] = $secondSelect;
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

    // 2. ማጣሪያዎችን እንለያለን
    $r_type = trim($filters['report_type'] ?? '');
    $secondSelect = $filters['second_select'];
    $thirdSelect = $filters['third_select'];
    $fourthSelect = $filters['fourth_select'] ?? null;
    $zoneSubFilter = $filters['zone_sub_filter']; // የዞን ንዑስ ማጣሪያ

    // 3. የ SQL ኪዩሪውን መሠረት እንገነባለን
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
        
        FROM branches b";

    $params = [];

    // 🌟 4. የዞን ሪፖርት ሲመረጥ ከንዑስ ማጣሪያው ጋር የተጣጣመ የ JOIN ሎጂክ
    if ($r_type === 'zone') {
        if ($zoneSubFilter === 'memriya_only') {
            $sql .= " LEFT JOIN vehicles v ON v.branch_id = b.id AND v.is_active = 1";
        } 
        elseif ($zoneSubFilter === 'wereda_only') {
            $sql .= " LEFT JOIN vehicles v ON v.branch_id = b.id AND v.is_active = 1";
        } 
        else {
            // ሁሉም ሲመረጥ ከዞን ID ጋር በቀጥታ እንዲያያዝ
            $sql .= " LEFT JOIN vehicles v ON v.zone_id = b.id AND v.is_active = 1";
        }
    } 
    // 🌟 5. ለ መምሪያ (department) ሪፖርት
    else if ($r_type === 'department') {
        if (!empty($thirdSelect) && $thirdSelect !== 'all') {
            $sql .= " LEFT JOIN vehicles v ON v.branch_id = b.id AND v.is_active = 1 AND v.zone_id = :third_select_zone";
            $params['third_select_zone'] = $thirdSelect;
        } else {
            $sql .= " LEFT JOIN vehicles v ON v.branch_id = b.id AND v.is_active = 1";
        }
    } 
    // 🌟 6. ለሌሎች የሪፖርት አይነቶች መደበኛ ጆይን
    else {
        $sql .= " LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1";
    }

    // መምሪያዎችን ብቻ ማጣራት (ለ department ብቻ)
    if ($r_type === 'department') {
        $sql .= " WHERE b.is_active = 1 AND b.branch_type = 'memriya'";
    } else {
        $sql .= " WHERE b.is_active = 1";
    }

    // 7. ማጣሪያዎችን በቅደም ተከተል መለየት
    if ($r_type !== '' && $r_type !== 'all') {
        switch ($r_type) {
            case 'regional': 
                $sql .= " AND (b.functional_path IS NOT NULL AND b.functional_path != '')";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND (
                        b.id = :sec_id 
                        OR b.functional_parent_id = :sec_parent 
                        OR b.functional_path LIKE CONCAT((SELECT admin_path FROM branches WHERE id = :sec_path_id), '/%')
                        OR b.functional_path LIKE CONCAT('%/', :sec_path_id, '/%')
                    )";
                    $params['sec_id'] = $secondSelect;
                    $params['sec_parent'] = $secondSelect;
                    $params['sec_path_id'] = $secondSelect;
                }
                elseif ($secondSelect === 'all') {
                    $sql .= " AND b.level = '1'";
                }
                break;

            case 'agency': 
                $sql .= " AND b.branch_type != 'memriya' AND b.functional_parent_id IS NOT NULL AND (b.functional_path IS NOT NULL AND b.functional_path != '')";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND (b.id = :second_select OR b.functional_parent_id = :second_select)";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'department': 
                $sql .= " AND b.level = 2 AND b.functional_path IS NOT NULL AND b.functional_path != ''";
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND b.id = :second_select";
                    $params['second_select'] = $secondSelect;
                }
                break;

            case 'zone': 
                if ($zoneSubFilter === 'memriya_only') {
                    // መምሪያ ብቻ
                    $sql .= " AND b.level = 2 AND b.branch_type = 'memriya'";
                } 
                elseif ($zoneSubFilter === 'wereda_only') {
                    // ወረዳ ብቻ
                    $sql .= " AND b.level = 2 AND b.admin_path IS NOT NULL AND b.admin_path != ''";
                } 
                else {
                    // ሁሉም (ዞን ራሱ - level 1)
                    $sql .= " AND b.level = 1 AND b.admin_path IS NOT NULL AND b.admin_path != ''";
                }
                
                // ሁለተኛው ማጣሪያ (የተመረጠው ዞን ID) ካለ
                if ($secondSelect !== 'all' && $secondSelect !== '') {
                    $sql .= " AND v.zone_id = :zone_filter_id";
                    $params['zone_filter_id'] = $secondSelect;
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

    // 8. 4ተኛው ማጣሪያ (fourth_select)
    if (!empty($fourthSelect) && $fourthSelect !== 'all') {
        $sql .= " AND b.branch_type = :fourth_select";
        $params['fourth_select'] = $fourthSelect;
    }

    // መረጃውን በቅርንጫፍ (Branch) ማሰባሰብ
    $sql .= " GROUP BY b.id, b.uuid, b.name, b.branch_type ORDER BY b.name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    
    return [
        'report_data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ];
}


public function zonegraph(): array
{
    $sql = "WITH RECURSIVE SubBranches AS (
                -- 1. መነሻው እያንዳንዱ ቅርንጫፍ (ዞን፣ ሪጅኦ ወዘተ) ራሱ ነው።
                SELECT id, id AS child_id, branch_type
                FROM branches
                WHERE is_active = 1
                
                UNION ALL
                
                -- 2. በ recursive በኩል በስሩ ያሉትን ልጆች (Child branches) እንፈልጋለን (በ admin_parent_id ወይም parent ግንኙነት)
                SELECT sb.id, b.id AS child_id, b.branch_type
                FROM branches b
                INNER JOIN SubBranches sb ON b.admin_parent_id = sb.child_id
                WHERE b.is_active = 1
            )
            SELECT 
                b.id,
                b.name AS Name, 
                CASE 
                    WHEN LOWER(TRIM(b.branch_type)) = 'zone'  THEN 'ዞን'
                    WHEN LOWER(TRIM(b.branch_type)) = 'regio'  THEN 'ሪጅኦ'
                    WHEN LOWER(TRIM(b.branch_type)) = 'kifle_ketema'  THEN 'ክፍለ ከተማ'
                    WHEN LOWER(TRIM(b.branch_type)) = 'ketema_woreda'  THEN 'ከተማ ወረዳ'
                    WHEN LOWER(TRIM(b.branch_type)) = 'woreda'  THEN 'ወረዳ'
                    ELSE COALESCE(bt.type_in_am, b.branch_type, 'ሌላ')
                END AS CallerName,
                -- ቆጠራው የሚደረገው በራሱ በዞኑ/ሪጅኦው ስር ባሉ ሁሉም ቅርንጫፎች (memriya, woreda ጨምሮ) ባሉ ተሽከርካሪዎች ድምር ነው።
                COUNT(DISTINCT v.id) AS vehicle_count
            FROM branches b
            LEFT JOIN branch_type bt ON TRIM(b.branch_type) = TRIM(bt.type_in_eng)
            -- ዞኑን ወይም ሪጅኦውን ከስሩ ካሉት (memriya, woreda ወዘተ) ጋር እናገናኘዋለን
            LEFT JOIN SubBranches sb ON b.id = sb.id
            LEFT JOIN vehicles v ON sb.child_id = v.branch_id AND v.is_active = 1
            WHERE b.is_active = 1 
              AND b.admin_path IS NOT NULL 
              AND b.admin_path != '' 
            GROUP BY b.id, b.name, b.branch_type, bt.type_in_am, bt.type_in_eng
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
                    WHEN LOWER(TRIM(vehicle_status)) = 'out_of_service' THEN 'በብልሽት የቆመ'
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



public function vehiclesurviceGraph(): array
{
    $sql = "SELECT 
    CASE 
        WHEN LOWER(TRIM(cs.service_name)) = 'የህዝብ' THEN 'የህዝብ'
        WHEN LOWER(TRIM(cs.service_name)) = 'የጭነት' THEN 'የጭነት'
        WHEN LOWER(TRIM(cs.service_name)) = 'የእሳት አደጋ' THEN 'የእሳት አደጋ'
        WHEN LOWER(TRIM(cs.service_name)) = 'የኮንስትራክሽን እና ግንባታ' THEN 'የኮንስትራክሽን እና ግንባታ'
        WHEN LOWER(TRIM(cs.service_name)) = 'የህክምና አገልግሎት/አምቡላንስ' THEN 'የህክምና አገልግሎት/አምቡላንስ'
        WHEN LOWER(TRIM(cs.service_name)) = 'የፍሳሽ ማስወገጃ' THEN 'የፍሳሽ ማስወገጃ'
        WHEN LOWER(TRIM(cs.service_name)) = 'የደርቅ ቆሻሻ ማስወገጃ' THEN 'የደርቅ ቆሻሻ ማስወገጃ'
        WHEN LOWER(TRIM(cs.service_name)) = 'shower truck(የፈሳሽ ጭነት)' THEN 'Shower truck(የፈሳሽ ጭነት)'
        ELSE COALESCE(cs.service_name, 'ለይቶ የማይታወቅ')
    END AS service_name, 
    COUNT(v.id) AS vehicle_count
FROM vehicles v
JOIN car_type ct ON v.vehicle_type = ct.id
JOIN car_service cs ON ct.service_type = cs.id
WHERE v.is_active = 1 
  AND v.deleted_at IS NULL
GROUP BY cs.service_name
ORDER BY vehicle_count DESC";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function vehicleAgeGraph(): array
{
    $sql = "SELECT 
                SUM(CASE WHEN (YEAR(CURRENT_DATE()) - manufactured_year) <= 5 THEN 1 ELSE 0 END) AS years_0_to_5, 
                SUM(CASE WHEN (YEAR(CURRENT_DATE()) - manufactured_year) BETWEEN 6 AND 15 THEN 1 ELSE 0 END) AS years_6_to_15, 
                SUM(CASE WHEN (YEAR(CURRENT_DATE()) - manufactured_year) BETWEEN 16 AND 25 THEN 1 ELSE 0 END) AS years_16_to_25, 
                SUM(CASE WHEN (YEAR(CURRENT_DATE()) - manufactured_year) BETWEEN 26 AND 35 THEN 1 ELSE 0 END) AS years_26_to_35, 
                SUM(CASE WHEN (YEAR(CURRENT_DATE()) - manufactured_year) >= 36 THEN 1 ELSE 0 END) AS years_36_plus 
            FROM vehicles 
            WHERE is_active = 1 
              AND deleted_at IS NULL";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// 1. አጠቃላይ ማጠቃለያ (Summary Cards) መረጃዎችን ማምጫ
    public function getDashboardSummary(): array {
        $summary = [];

        // አጠቃላይ ተሽከርካሪዎች ብዛት
        $sqlTotal = "SELECT COUNT(*) as total FROM vehicles WHERE is_active = 1 AND deleted_at IS NULL";
        $stmtTotal = $this->db->prepare($sqlTotal);
        $stmtTotal->execute();
        $summary['total_vehicles'] = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // በስራ ላይ ያሉ
        $sqlActive = "SELECT COUNT(*) as total FROM vehicles WHERE vehicle_status = 'active' AND is_active = 1 AND deleted_at IS NULL";
        $stmtActive = $this->db->prepare($sqlActive);
        $stmtActive->execute();
        $summary['active_vehicles'] = $stmtActive->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // በጥገና ላይ ያሉ
        $sqlMaint = "SELECT COUNT(*) as total FROM vehicles WHERE (vehicle_status = 'out_of_service') AND is_active = 1 AND deleted_at IS NULL";
        $stmtMaint = $this->db->prepare($sqlMaint);
        $stmtMaint->execute();
        $summary['maintenance_vehicles'] = $stmtMaint->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // ጉዳት የደረሰባቸው / የተወገዱ
        $sqlDamaged = "SELECT COUNT(*) as total FROM vehicles WHERE (vehicle_status = 'disposed' OR vehicle_status = 'destroyed') AND is_active = 1 AND deleted_at IS NULL";
        $stmtDamaged = $this->db->prepare($sqlDamaged);
        $stmtDamaged->execute();
        $summary['damaged_vehicles'] = $stmtDamaged->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        return $summary;
    }

    //2. በተሽከርካሪ አገልግሎት ዓይነት (Service Type Chart) ዳታ ማምጫ
    public function getVehiclesByServiceType(): array {
        $sql = "SELECT 
    CASE 
        WHEN LOWER(TRIM(cs.service_name)) = 'የህዝብ' THEN 'የህዝብ'
        WHEN LOWER(TRIM(cs.service_name)) = 'የጭነት' THEN 'የጭነት'
        WHEN LOWER(TRIM(cs.service_name)) = 'የእሳት አደጋ' THEN 'የእሳት አደጋ'
        WHEN LOWER(TRIM(cs.service_name)) = 'የኮንስትራክሽን እና ግንባታ' THEN 'የኮንስትራክሽን እና ግንባታ'
        WHEN LOWER(TRIM(cs.service_name)) = 'የህክምና አገልግሎት/አምቡላንስ' THEN 'የህክምና አገልግሎት/አምቡላንስ'
        WHEN LOWER(TRIM(cs.service_name)) = 'የፍሳሽ ማስወገጃ' THEN 'የፍሳሽ ማስወገጃ'
        WHEN LOWER(TRIM(cs.service_name)) = 'የደርቅ ቆሻሻ ማስወገጃ' THEN 'የደርቅ ቆሻሻ ማስወገጃ'
        WHEN LOWER(TRIM(cs.service_name)) = 'shower truck(የፈሳሽ ጭነት)' THEN 'Shower truck(የፈሳሽ ጭነት)'
        ELSE COALESCE(cs.service_name, 'ለይቶ የማይታወቅ')
    END AS service_name, 
    COUNT(v.id) AS vehicle_count
FROM vehicles v
JOIN car_type ct ON v.vehicle_type = ct.id
JOIN car_service cs ON ct.service_type = cs.id
WHERE v.is_active = 1 
  AND v.deleted_at IS NULL
GROUP BY cs.service_name
ORDER BY vehicle_count DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. በተሽከርካሪ ወቅታዊ ሁኔታ (Status Doughnut Chart) ዳታ ማምጫ
    public function getVehiclesByStatus(): array {
        $sql = "SELECT 
                CASE 
                    WHEN LOWER(TRIM(vehicle_status)) = 'active' THEN 'በስራ ላይ ያለ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'lost' THEN 'የጠፋ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'destroyed' THEN 'የወደመ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'disposed' THEN 'የተወገደ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'out_of_service' THEN 'በብልሽት የቆመ'
                    ELSE COALESCE(vehicle_status, 'ለይቶ የማይታወቅ')
                END AS CallerName, 
                COUNT(id) AS vehicle_count
            FROM vehicles
            WHERE is_active = 1 
              AND deleted_at IS NULL
            GROUP BY 
                CASE 
                    WHEN LOWER(TRIM(vehicle_status)) = 'active' THEN 'በስራ ላይ ያለ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'lost' THEN 'የጠፋ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'destroyed' THEN 'የወደመ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'disposed' THEN 'የተወገደ'
                    WHEN LOWER(TRIM(vehicle_status)) = 'out_of_service' THEN 'በብልሽት የቆመ'
                    ELSE COALESCE(vehicle_status, 'ለይቶ የማይታወቅ')
                END
            ORDER BY vehicle_count DESC";
            
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. በተሽከርካሪ ዕድሜ ክልል (Vehicle Age Chart) ዳታ ማምጫ
    public function getVehiclesByAge(): array {
        $sql = "SELECT vichel_age, COUNT(*) as vehicle_count 
                FROM vehicles 
                WHERE is_active = 1 AND deleted_at IS NULL 
                GROUP BY vichel_age";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. በተመረቱበት ዓመት ልክ መረጃ ማምጫ
    public function getMonthlyTrend(): array {
        $sql = "SELECT manufactured_year AS manufactured_year, COUNT(*) as activity_count 
                FROM vehicles
                WHERE is_active = 1 
                  AND deleted_at IS NULL 
                  AND manufactured_year IS NOT NULL
                GROUP BY manufactured_year 
                ORDER BY manufactured_year ASC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




public function novichelregiongraph(?string $id = null): array
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
      AND b.functional_path != ''";

    $params = [];
    
    // የተመረጠው $id ካለ ኩየሪውን ማጣሪያ እንጨምርበት
    if (!empty($id)) {
        // $id የሚወክለው የባንክ/የተቋም መለያ (ID) ወይም  ፓርዝ ካልሆነ 
        // እንደ ዳታቤዙ አወቃቀር b.id = ? ወይም b.functional_path LIKE ? ማድረግ ይቻላል
        $sql .= " AND (b.id = ? OR b.parent_id = ?)";
        $params[] = $id;
        $params[] = $id;
    }

    $sql .= " GROUP BY b.id, b.name, b.branch_type, bt.type_in_am
              HAVING COUNT(v.id) = 0";
              
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function topvehiclesregiongraph(?string $id = null): array
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
      AND b.functional_path != ''";

    $params = [];
    
    if (!empty($id)) {
        $sql .= " AND (b.id = ? OR b.parent_id = ?)";
        $params[] = $id;
        $params[] = $id;
    }

    // 20 እና ከዛ በላይ ያላቸውን ለማምጣት
    $sql .= " GROUP BY b.id, b.name, b.branch_type, bt.type_in_am
              HAVING COUNT(v.id) >= 20
              ORDER BY vehicle_count DESC";
              
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function vehiclePurchaseYearGraph(?string $id = null): array
{
    $sql = "SELECT 
        COALESCE(v.purchase_year, 'ያልታወቀ') AS purchase_year, 
        COUNT(v.id) AS vehicle_count
    FROM vehicles v
    LEFT JOIN branches b ON v.branch_id = b.id
    WHERE v.is_active = 1";

    $params = [];
    
    if (!empty($id)) {
        $sql .= " AND (b.id = ? OR b.parent_id = ?)";
        $params[] = $id;
        $params[] = $id;
    }

    $sql .= " GROUP BY v.purchase_year
              ORDER BY purchase_year ASC";
              
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function zoneWorkingVehiclesGraph(?string $id = null): array
{
    $sql = "SELECT 
        b.name AS zone_name, 
        COUNT(v.id) AS vehicle_count
    FROM branches b
    LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1 
    -- እዚህ ጋር የተሽከርካሪው ሁኔታ በስራ ላይ መሆኑን የሚገልጸውን ማጣሪያ (ለምሳሌ status ወይም working condition) ያስገቡ
    WHERE b.is_active = 1  and vehicle_status = 'active'
      AND b.admin_path IS NOT NULL 
      AND b.admin_path != ''";

    $params = [];
    
    if (!empty($id)) {
        $sql .= " AND (b.id = ? OR b.parent_id = ?)";
        $params[] = $id;
        $params[] = $id;
    }

    $sql .= " GROUP BY b.id, b.name
              ORDER BY vehicle_count DESC";
              
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




public function zoneDamagedVehiclesGraph(?string $id = null): array
{
    $sql = "SELECT 
        b.name AS zone_name, 
        COUNT(v.id) AS vehicle_count
    FROM branches b
    LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1 
    -- እዚህ ጋር የወደሙ/ጉዳት የደረሰባቸውን ተሽከርካሪዎች የሚለይበትን condition (ለምሳሌ v.status = 'damaged' ወይም የመሳሰሉትን) ማካተት ይቻላል
    WHERE b.is_active = 1  and vehicle_status= 'destroyed'
      AND b.admin_path IS NOT NULL 
      AND b.admin_path != ''";

    $params = [];
    
    if (!empty($id)) {
        $sql .= " AND (b.id = ? OR b.parent_id = ?)";
        $params[] = $id;
        $params[] = $id;
    }

    $sql .= " GROUP BY b.id, b.name
              ORDER BY vehicle_count DESC";
              
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function metropolitanVehiclesGraph(?string $id = null): array
{
    $sql = "SELECT 
        b.name AS metro_name, 
        COUNT(v.id) AS vehicle_count
    FROM branches b
    LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1 
    -- እዚህ ጋር የሪጅኦፖሊታን ከተሞችን የሚለይበትን condition (ለምሳሌ b.branch_type = 'metropolitan' ወይም የመሳሰሉትን) ማካተት ይቻላል
    WHERE b.is_active = 1  and b.branch_type = 'regio'
      AND b.admin_path IS NOT NULL 
      AND b.admin_path != ''";

    $params = [];
    
    if (!empty($id)) {
        $sql .= " AND (b.id = ? OR b.parent_id = ?)";
        $params[] = $id;
        $params[] = $id;
    }

    $sql .= " GROUP BY b.id, b.name
              ORDER BY vehicle_count DESC";
              
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function inactiveVehiclesByInstitutionGraph(?string $id = null): array
{
    $sql = "SELECT 
        b.name AS institution_name, 
        COUNT(v.id) AS vehicle_count
    FROM branches b
    LEFT JOIN vehicles v ON b.id = v.branch_id
    WHERE b.is_active = 1  and v.vehicle_status != 'active'
      AND b.functional_path IS NOT NULL 
      AND b.functional_path != ''";

    $params = [];
    
    if (!empty($id)) {
        $sql .= " AND (b.id = ? OR b.parent_id = ?)";
        $params[] = $id;
        $params[] = $id;
    }

    $sql .= " GROUP BY b.id, b.name
              HAVING COUNT(v.id) > 0
              ORDER BY vehicle_count DESC";
              
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}