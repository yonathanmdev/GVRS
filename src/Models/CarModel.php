<?php
namespace App\Models;
use PDO;
class CarModel {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Creates a level-1 (root) functional org unit — e.g. Bureau, Commission, Authority.
     * No functional_parent_id needed since this is the top of the functional tree.
     * admin_parent_id is NULL because this row itself is NOT an administrative unit —
     * organization_id already tells us where it physically sits.
     */
   public function create(array $data): string
{
    $sql = "INSERT INTO brand (
                uud,
                brand_name,
                registered_by 
            ) VALUES (
                :uuid,
                :car_brand_name,
                :registered_by
            )";

    $stmt = $this->db->prepare($sql);
    
    $success = $stmt->execute([
        'uuid'           => $data['uuid'],
        'car_brand_name' => $data['car_brand_name'],
        'registered_by'  => $data['registered_by'],
    ]);

    // ክዋኔው የተሳካ ከሆነ የገባውን UUID ይመልሳል
    if ($success) {
        return $data['uuid'];
    }

    throw new \Exception("የብራንድ መረጃውን ማስቀመጥ አልተቻለም።");
}

public function getAll(): array
{
    $sql = "SELECT uud, brand_name  FROM brand
            WHERE  is_deleted = 0";

    $params = [];

 

    $sql .= " ORDER BY brand_name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
public function findById(string $id): ?array
{
    $findSql = "SELECT uud, brand_name FROM brand WHERE uud = :uuid AND is_deleted = 0";
    $findStmt = $this->db->prepare($findSql);
    $findStmt->execute(['uuid' => $id]);
    $brand = $findStmt->fetch(\PDO::FETCH_ASSOC);

    if (!$brand) {
        return null;
    }

    return $brand;
}
public function updatecar(array $data): array
{
    // 1. Fetch the current row BEFORE updating
    $findSql = "SELECT uud, brand_name FROM brand WHERE uud = :uuid AND is_deleted =0";
    $findStmt = $this->db->prepare($findSql);
    $findStmt->execute(['uuid' => $data['uuid']]);
    $existing = $findStmt->fetch(\PDO::FETCH_ASSOC);

    if (!$existing) {
        return ['status' => 'error', 'message' => 'car not found.'];
    }

     
    $brand_name =  $existing['brand_name'];

    $updateSql = "UPDATE brand
                  SET brand_name = :name,
                      updated_by = :updated_by
                  WHERE uud = :uuid
                    AND is_deleted = 0";

    $stmt = $this->db->prepare($updateSql);
    $stmt->execute([
        'name'        => $data['name'],
        'updated_by'  => $data['updated_by'],
        'uuid'        => $data['uuid'],
    ]);

    if ($stmt->rowCount() === 0) {
        return ['status' => 'error', 'message' => 'Update failed .'];
    }

    return ['status' => 'success', 'id' => $brand_name];
}
public function createAccountableOffices(array $data): int
{
    try {
        $this->db->beginTransaction();

        // 1. Fetch parent
        $parentSql = "
            SELECT id, functional_path, level
            FROM branches
            WHERE id = :parent_id
              AND is_active <> 3
              AND branch_type IN ('bureau', 'authority','commission','institution','enterprise','memriya','tsfet_bet')
            LIMIT 1
        ";

        $parentStmt = $this->db->prepare($parentSql);

        $parentStmt->execute([
            'parent_id' => $data['functional_parent_id']
        ]);

        $parent = $parentStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$parent) {
            throw new \InvalidArgumentException(
                "Parent branch not found: {$data['functional_parent_id']}"
            );
        }

        // 2. Child level
        $level = (int) $parent['level'] + 1;


        // 3. Insert branch
        $sql = "INSERT INTO branches (
                    uuid,
                    organization_id,
                    admin_parent_id,
                    name,
                    branch_type,
                    level,
                    admin_path,
                    functional_parent_id,
                    functional_path,
                    registered_by,
                    is_active
                ) VALUES (
                    :uuid,
                    :organization_id,
                    NULL,
                    :name,
                    :branch_type,
                    :level,
                    NULL,
                    :functional_parent_id,
                    NULL,
                    :registered_by,
                    1
                )";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'uuid'            => $data['uuid'],
            'organization_id' => $data['organization_id'],
            'name'            => $data['branch_name'],
            'branch_type'     => $data['branch_type'],
            'level'           => $level,
            'functional_parent_id' => $data['functional_parent_id'],
            'registered_by'   => $data['registered_by'],
        ]);

        $internalId = (int) $this->db->lastInsertId();


        // 4. Build admin_path from parent's path
        $functionalPath = $parent['functional_path'] . $internalId . '/';


        // 5. Update admin_path
        $updateSql = "
            UPDATE branches
            SET functional_path = :functional_path
            WHERE id = :id
        ";

        $updateStmt = $this->db->prepare($updateSql);

        $updateStmt->execute([
            'functional_path' => $functionalPath,
            'id'         => $internalId,
        ]);


        // 6. Everything succeeded
        $this->db->commit();

        return $internalId;

    } catch (\Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}
public function getaccountableOfficesWithBureau(int $level, ?int $bureau_id = null, ?string $search = null, int $limit = 20, int $offset = 0): array
{
    $sql = "SELECT 
                aco.id, aco.uuid, aco.name AS office_name, aco.branch_type, aco.level,
                aco.functional_path, aco.organization_id, aco.is_active,
                b.id AS bureau_id, b.uuid AS bureau_uuid, b.name AS bureau_name, b.branch_type AS bureau_type
            FROM branches aco
            JOIN branches b ON b.id = aco.functional_parent_id
            WHERE aco.branch_type IN ('authority','commission','institution','enterprise','memriya','tsfet_bet','college')
            AND aco.is_active <> 3
            AND aco.level = :level";

    $params = [];

    if ($bureau_id !== null) {
        $sql .= " AND aco.functional_parent_id = :bureau_id";
        $params['bureau_id'] = $bureau_id;
    }
    if (!empty($search)) {
        $sql .= " AND (aco.name LIKE :search OR b.name LIKE :search)";
        $params['search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY b.name ASC, aco.name ASC LIMIT :limit OFFSET :offset";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue('level', $level, \PDO::PARAM_INT);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function countOfficesWithBureau(int $level, ?string $bureau_id = null, ?string $search = null): int
{
    $sql = "SELECT COUNT(*) FROM branches w
            JOIN branches z ON z.id = w.functional_parent_id
            WHERE w.branch_type IN ('college', 'authority','commission','institution','enterprise','memriya','tsfet_bet')
            AND w.is_active <> 3
            AND w.level = :level";

    $params = ['level' => $level];

    if ($bureau_id !== null) {
        $sql .= " AND w.functional_parent_id = :bureau_id";
        $params['bureau_id'] = $bureau_id;
    }
    if (!empty($search)) {
        $sql .= " AND (w.name LIKE :search OR z.name LIKE :search)";
        $params['search'] = '%' . $search . '%';
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

public function updateAccountableOffice(array $data): ?int
{
    try {

        $this->db->beginTransaction();

        // 1. Find the branch ID using UUID
        $findSql = "
            SELECT id
            FROM branches
            WHERE uuid = :uuid
              AND is_active <> 3
              AND branch_type IN ('college', 'authority','commission','institution','enterprise','memriya','tsfet_bet' 
              )
            LIMIT 1
        ";

        $findStmt = $this->db->prepare($findSql);

        $findStmt->execute([
            ':uuid' => $data['uuid']
        ]);

        $branchId = $findStmt->fetchColumn();

        if ($branchId === false) {
            $this->db->rollBack();
            return null;
        }

        $branchId = (int) $branchId;


        // 2. Fetch parent 
        $parentSql = "
            SELECT id, functional_path, level, branch_type
            FROM branches
            WHERE id = :parent_id
              AND is_active <> 3
              AND branch_type IN ('bureau', 'authority', 'enterprise', 'institution', 'commission')
            LIMIT 1
        ";

        $parentStmt = $this->db->prepare($parentSql);

        $parentStmt->execute([
            ':parent_id' => $data['functional_parent_id']
        ]);

        $parent = $parentStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$parent) {
            throw new \InvalidArgumentException(
                "Parent branch not found: {$data['functional_parent_id']}"
            );
        }


        // 3. Build new admin_path
        $functionalPath = $parent['functional_path'] . $branchId . '/';


        // 4. Update branch
        $sql = "
            UPDATE branches
            SET
                name = :name,
                functional_parent_id = :functional_parent_id,
                branch_type = :branch_type,
                functional_path = :functional_path,
                updated_by = :updated_by
            WHERE id = :id
              AND is_active <> 3
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':name'            => $data['name'],
            ':functional_parent_id' => $data['functional_parent_id'],
            ':branch_type'     => $data['branch_type'],
            ':functional_path'      => $functionalPath,
            ':updated_by'      => $data['updated_by'],
            ':id'              => $branchId
        ]);


        // 5. Commit
        $this->db->commit();

        // 6. Return integer branch ID
        return $branchId;

    } catch (\Throwable $e) {

        // Rollback if transaction is still active
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}

 public function softDelete(string $id, string $userId, string $reason, string $source): array
{
    // 1. Find the brand first
    $findSql = "SELECT uud, brand_name, registered_by, created_at,is_deleted FROM brand WHERE uud = :uuid";
    $findStmt = $this->db->prepare($findSql);
    $findStmt->execute(['uuid' => $id]);
    $brand = $findStmt->fetch(\PDO::FETCH_ASSOC);

    if (!$brand) {
        throw new \InvalidArgumentException("car brand not found: {$id}");
    }

    if ((int) $brand['is_deleted'] === 1) {
        throw new \InvalidArgumentException("{$brand['brand_name']} is already deleted: {$id}");
    }

    // 2. Pick the correct path column based on branch_type
     

    $this->db->beginTransaction();

    try {
        // 3a. Soft delete the SELECTED branch with the individual, user-supplied reason/source
        $selfSql = "UPDATE brand 
                     SET is_deleted = 1, 
                         deletedby = :deleted_by, 
                         deleteddate = NOW(),
                         deletion_source = :deletion_source, 
                         deletion_reason = :deletion_reason
                         
                     WHERE uud = :uuid
                     AND is_deleted = 0";
        $selfStmt = $this->db->prepare($selfSql);
        $selfStmt->execute([
            'deleted_by'      => $userId,
            'deletion_source' => $source,        // 'INDIVIDUAL'
            'deletion_reason' => $reason,
            'uuid'            => $id,
        ]);

        $branchDeletedCount = $selfStmt->rowCount();

        // 3b. Soft delete all DESCENDANTS with a forced CASCADE source
        
        
        $this->db->commit();

        return [
            'status'      => 'success',
            'oldRecord'   => $brand,
            
            'id'    => $brand['uud'],
        ];

    } catch (\Throwable $e) {
        $this->db->rollBack();
        throw $e;
    }
}
/**
     * አዲስ Car Type መዝግብ
     */
    public function createcartype(array $data): bool
    {
        
        $sql = "INSERT INTO `car_type` (
                   
                    `brand_id`, 
                    `type_name`, 
                    `service_type`, 
                    `measurement`, 
                    `catagory`, 
                    `registerd_by`
                ) VALUES (
                     
                    :brand_id, 
                    :type_name, 
                    :service_type, 
                    :measurement, 
                    :catagory, 
                    :registerd_by
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':brand_id'     => $data['brand_id'],
            ':type_name'    => $data['type_name'],
            ':service_type' => $data['service_type'],
            ':measurement'  => $data['measurement'],
            ':catagory'     => $data['catagory'],
            ':registerd_by' => $data['registerd_by'],
        ]);
    }
public function isDuplicate(int $brandId, int $typeName, int $serviceType, string $measurement, string $catagory): bool
{
    $sql = "SELECT COUNT(*) FROM `car_type` 
            WHERE `brand_id`     = :brand_id 
              AND `type_name`    = :type_name 
              AND `service_type` = :service_type 
              AND `measurement`  = :measurement 
              AND `catagory`     = :catagory";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':brand_id'     => $brandId,
        ':type_name'    => $typeName,
        ':service_type' => $serviceType,
        ':measurement'  => $measurement,
        ':catagory'     => $catagory,
    ]);

    return ((int) $stmt->fetchColumn()) > 0;
}
    /**
     * ለ Dropdown የሚሆኑ የ Brand መረጃዎችን ማምጫ
     */
    public function getActiveBrands(): array
    {
        $stmt = $this->db->prepare("SELECT id, brand_name FROM brand WHERE is_deleted = 0 ORDER BY brand_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ለ Dropdown የሚሆኑ የ Car Type List መረጃዎችን ማምጫ
     */
    public function getCarTypeLists(): array
    {
        $stmt = $this->db->prepare("SELECT id, cartype FROM cartypelist WHERE is_deleted = 0 ORDER BY cartype ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ለ Dropdown የሚሆኑ የ Service Type መረጃዎችን ማምጫ
     */
    public function getCarServices(): array
    {
        $stmt = $this->db->prepare("SELECT id, service_name FROM car_service   ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
 * የተመዘገቡ የመኪና/ማሽን አይነቶችን ከነ ሙሉ መረጃቸው ከዳታቤዝ ያመጣል
 * 
 * @return array
 */
// የተመዘገቡትን ሙሉ ዝርዝር ማምጫ
public function getRegisteredCarTypes(): array
{
    try {
        $sql = "SELECT 
                    ct.id,
                    ct.uuid,
                    ct.brand_id,
                    ct.type_name AS type_name_id,
                    ct.service_type AS service_type_id,
                    ct.measurement,
                    ct.catagory,
                    b.brand_name,
                    ctl.cartype AS type_name, -- በዳታቤዙ መሰረት ctl.cartype ተደርጓል
                    cs.service_name
                FROM car_type ct
                LEFT JOIN brand b ON ct.brand_id = b.id
                LEFT JOIN cartypelist ctl ON ct.type_name = ctl.id
                LEFT JOIN car_service cs ON ct.service_type = cs.id
                WHERE ct.is_deleted = 0
                ORDER BY ct.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    } catch (\PDOException $e) {
        error_log("Error in getRegisteredCarTypes: " . $e->getMessage());
        return [];
    }
}
// መረጃ ማስተካከያ (Update)
public function updateCarType(array $data): bool
{
    try {
        $sql = "UPDATE car_type SET 
                    brand_id = :brand_id,
                    type_name = :type_name,
                    service_type = :service_type,
                    measurement = :measurement,
                    catagory = :catagory,
                    updated_by = :updated_by
                WHERE uuid = :uuid AND is_deleted = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':brand_id'     => $data['brand_id'],
            ':type_name'    => $data['type_name'],
            ':service_type' => $data['service_type'],
            ':measurement'  => $data['measurement'],
            ':catagory'     => $data['catagory'],
            ':updated_by'   => $data['updated_by'],
            ':uuid'         => $data['uuid'],
        ]);

        return $stmt->rowCount() > 0; // መረጃው በውኑ ከተቀየረ ብቻ true ይመልሳል
    } catch (\PDOException $e) {
        error_log("Error in updateCarType: " . $e->getMessage());
        return false;
    }
}

// መረጃ መሰረዣ (Soft Delete)
public function deleteCarType(string $uuid, int $userId): bool
{
    try {
        $sql = "UPDATE car_type SET is_deleted = 1, updated_by = :updated_by WHERE uuid = :uuid AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':updated_by' => $userId,
            ':uuid'       => $uuid
        ]);

        return $stmt->rowCount() > 0; // መረጃው ከተሰረዘ ብቻ true ይመልሳል
    } catch (\PDOException $e) {
        error_log("Error in deleteCarType: " . $e->getMessage());
        return false;
    }
}
}
