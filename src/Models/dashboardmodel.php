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



public function getVehicleReportAdvanced($filters): array
{
    $sql = "SELECT 
                b.id as branch_id,
                b.name as branch_name,
                SUM(CASE WHEN v.vehicle_type = 'automobile' THEN 1 ELSE 0 END) as automobile,
                SUM(CASE WHEN v.vehicle_type = 'pickup_hilux' THEN 1 ELSE 0 END) as pickup_hilux,
                SUM(CASE WHEN v.vehicle_type = 'single_cab' THEN 1 ELSE 0 END) as single_cab,
                SUM(CASE WHEN v.vehicle_type = 'double_cab' THEN 1 ELSE 0 END) as double_cab,
                SUM(CASE WHEN v.vehicle_type = 'v8' THEN 1 ELSE 0 END) as v8,
                SUM(CASE WHEN v.vehicle_type = 'minibus' THEN 1 ELSE 0 END) as minibus,
                SUM(CASE WHEN v.vehicle_type = 'bus' THEN 1 ELSE 0 END) as bus,
                SUM(CASE WHEN v.vehicle_status = 'damaged' THEN 1 ELSE 0 END) as damaged,
                SUM(CASE WHEN v.vehicle_status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN v.vehicle_status = 'operational' THEN 1 ELSE 0 END) as operational,
                COUNT(v.id) as total_vehicles
            FROM branches b
            LEFT JOIN vehicles v ON b.id = v.branch_id AND v.is_active = 1
            WHERE b.is_active = 1";

    $params = [];

    // 1. second_select (ባዶ ያልሆነ እና 'all' ያልሆነ ከሆነ ብቻ እናጣራለን)
    if (!empty($filters['second_select']) && $filters['second_select'] !== 'all') {
        if ($filters['report_type'] === 'regional') {
            $sql .= " AND b.functional_parent_id = :second_select";
        } else {
            $sql .= " AND b.admin_parent_id = :second_select";
        }
        $params['second_select'] = $filters['second_select'];
    }

    // 2. third_select (ባዶ ያልሆነ እና 'all' ያልሆነ ከሆነ ብቻ)
    if (!empty($filters['third_select']) && $filters['third_select'] !== 'all') {
        $sql .= " AND b.id = :third_select";
        $params['third_select'] = $filters['third_select'];
    }

    // 3. fourth_select / calls (ባዶ ያልሆነ እና 'all' ያልሆነ ከሆነ ብቻ)
    if (!empty($filters['fourth_select']) && $filters['fourth_select'] !== 'all') {
        $sql .= " AND b.branch_type = :fourth_select";
        $params['fourth_select'] = $filters['fourth_select'];
    }

    $sql .= " GROUP BY b.id, b.name ORDER BY b.name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}