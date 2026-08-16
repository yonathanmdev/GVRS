<?php
namespace App\Models;
use PDO;
class AdministrativeUnitModel {
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
    public function create(array $data): int
{
    try {
        $this->db->beginTransaction();

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
                    1,
                    NULL,
                    NULL,
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
            'registered_by'   => $data['registered_by'],
        ]);

        $internalId = (int) $this->db->lastInsertId();

        // Root node admin_path
        $adminPath = '/' . $internalId . '/';

        $updateSql = "
            UPDATE branches
            SET admin_path = :admin_path
            WHERE id = :id
        ";

        $updateStmt = $this->db->prepare($updateSql);

        $updateStmt->execute([
            'admin_path' => $adminPath,
            'id'         => $internalId,
        ]);

        // Everything succeeded
        $this->db->commit();

        return $internalId;

    } catch (\Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}
public function findById(string $id): ?array
{
    $findSql = "SELECT id, name, functional_path, admin_path, branch_type, is_active
                FROM branches WHERE uuid = :uuid AND is_active <> 3";
    $findStmt = $this->db->prepare($findSql);
    $findStmt->execute(['uuid' => $id]);
    $branch = $findStmt->fetch(\PDO::FETCH_ASSOC);

    if (!$branch) {
        return null;
    }

    return $branch;
}
public function updateZone(array $data): array
{
    // 1. Fetch the current row BEFORE updating
    $findSql = "SELECT id, branch_type FROM branches WHERE uuid = :uuid AND is_active <> 3";
    $findStmt = $this->db->prepare($findSql);
    $findStmt->execute(['uuid' => $data['uuid']]);
    $existing = $findStmt->fetch(\PDO::FETCH_ASSOC);

    if (!$existing) {
        return ['status' => 'error', 'message' => 'Branch not found.'];
    }

    $oldType = $existing['branch_type'];
    $newType = $data['branch_type'];
    $branchId = (int) $existing['id'];

    if ($oldType !== $newType) {
        $childTypeMap = [
            'regio'     => ['kifle_ketema'],
            'zone'      => ['ketema_woreda', 'woreda'],
        ];

        $childCheckSql = "SELECT DISTINCT branch_type FROM branches 
                           WHERE (admin_parent_id = :id OR functional_parent_id = :id)
                           AND is_active <> 3";
        $childStmt = $this->db->prepare($childCheckSql);
        $childStmt->execute(['id' => $branchId]);
        $existingChildTypes = $childStmt->fetchAll(\PDO::FETCH_COLUMN);

        if (!empty($existingChildTypes)) {
            $validChildTypesForNewType = $childTypeMap[$newType] ?? [];
            $incompatibleChildren = array_diff($existingChildTypes, $validChildTypesForNewType);

            if (!empty($incompatibleChildren)) {
                return [
                    'status'  => 'error',
                    'message' => "የዞኑን ዓይነት ከ '{$oldType}' ወደ '{$newType}' መቀየር አይቻልም፦ "
                               . "ይህ ዞን "
                               . implode(', ', $incompatibleChildren)
                               . " ዓይነት ያላቸው ነባር ወረዳዎች/ክ/ከተሞች አሉት፣ እነዚህም ለ '{$newType}' ትክክለኛ የወረዳ/ክ/ከተማ ዓይነት አይደሉም። "
                               . "እባክዎ መጀመሪያ በዚህ ዞን/ከተማ አስተዳደር ስር ያሉትን ያስተካክሉ።",
                    'incompatible_child_types' => array_values($incompatibleChildren),
                ];
            }
        }
    }

    $updateSql = "UPDATE branches
                  SET name = :name,
                      branch_type = :branch_type,
                      updated_by = :updated_by
                  WHERE uuid = :uuid
                    AND is_active <> 3
                    AND branch_type IN ('zone', 'regio')";

    $stmt = $this->db->prepare($updateSql);
    $stmt->execute([
        'name'        => $data['name'],
        'branch_type' => $newType,
        'updated_by'  => $data['updated_by'],
        'uuid'        => $data['uuid'],
    ]);

    if ($stmt->rowCount() === 0) {
        return ['status' => 'error', 'message' => 'Update failed or branch type not eligible for this operation.'];
    }

    return ['status' => 'success', 'id' => $branchId];
}

public function createWoreda(array $data): int
{
    try {
        $this->db->beginTransaction();

        // 1. Fetch parent
        $parentSql = "
            SELECT id, admin_path, level
            FROM branches
            WHERE id = :parent_id
              AND is_active <> 3
              AND branch_type IN ('zone', 'regio')
            LIMIT 1
        ";

        $parentStmt = $this->db->prepare($parentSql);

        $parentStmt->execute([
            'parent_id' => $data['admin_parent_id']
        ]);

        $parent = $parentStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$parent) {
            throw new \InvalidArgumentException(
                "Parent branch not found: {$data['admin_parent_id']}"
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
                    :admin_parent_id,
                    :name,
                    :branch_type,
                    :level,
                    NULL,
                    NULL,
                    NULL,
                    :registered_by,
                    1
                )";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'uuid'            => $data['uuid'],
            'organization_id' => $data['organization_id'],
            'admin_parent_id' => $data['admin_parent_id'],
            'name'            => $data['branch_name'],
            'branch_type'     => $data['branch_type'],
            'level'           => $level,
            'registered_by'   => $data['registered_by'],
        ]);

        $internalId = (int) $this->db->lastInsertId();


        // 4. Build admin_path from parent's path
        $adminPath = $parent['admin_path'] . $internalId . '/';


        // 5. Update admin_path
        $updateSql = "
            UPDATE branches
            SET admin_path = :admin_path
            WHERE id = :id
        ";

        $updateStmt = $this->db->prepare($updateSql);

        $updateStmt->execute([
            'admin_path' => $adminPath,
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

public function getWoredasWithZone(?int $zoneId = null, ?string $search = null, int $limit = 20, int $offset = 0): array
{
    $sql = "SELECT 
                w.id, w.uuid, w.name AS woreda_name, w.branch_type, w.level,
                w.admin_path, w.organization_id, w.is_active,
                z.id AS zone_id, z.uuid AS zone_uuid, z.name AS zone_name, z.branch_type AS zone_branch_type
            FROM branches w
            JOIN branches z ON z.id = w.admin_parent_id
            WHERE w.branch_type IN ('woreda', 'ketema_woreda','kifle_ketema')
            AND w.is_active <> 3";

    $params = [];

    if ($zoneId !== null) {
        $sql .= " AND w.admin_parent_id = :zone_id";
        $params['zone_id'] = $zoneId;
    }
    if (!empty($search)) {
        $sql .= " AND (w.name LIKE :search OR z.name LIKE :search)";
        $params['search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY z.name ASC, w.name ASC LIMIT :limit OFFSET :offset";

    $stmt = $this->db->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function countWoredasWithZone(?string $zoneId = null, ?string $search = null): int
{
    $sql = "SELECT COUNT(*) FROM branches w
            JOIN branches z ON z.id = w.admin_parent_id
            WHERE w.branch_type IN ('woreda', 'ketema_woreda','kifle_ketema')
            AND w.is_active <> 3";

    $params = [];

    if ($zoneId !== null) {
        $sql .= " AND w.admin_parent_id = :zone_id";
        $params['zone_id'] = $zoneId;
    }
    if (!empty($search)) {
        $sql .= " AND (w.name LIKE :search OR z.name LIKE :search)";
        $params['search'] = '%' . $search . '%';
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

public function updateWoreda(array $data): ?int
{
    try {

        $this->db->beginTransaction();

        // 1. Find the branch ID using UUID
        $findSql = "
            SELECT id
            FROM branches
            WHERE uuid = :uuid
              AND is_active <> 3
              AND branch_type IN (
                  'woreda',
                  'ketema_woreda',
                  'kifle_ketema'
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


        // 2. Fetch parent zone/regio
        $parentSql = "
            SELECT id, admin_path, level, branch_type
            FROM branches
            WHERE id = :parent_id
              AND is_active <> 3
              AND branch_type IN ('zone', 'regio')
            LIMIT 1
        ";

        $parentStmt = $this->db->prepare($parentSql);

        $parentStmt->execute([
            ':parent_id' => $data['admin_parent_id']
        ]);

        $parent = $parentStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$parent) {
            throw new \InvalidArgumentException(
                "Parent branch not found: {$data['admin_parent_id']}"
            );
        }


        // 3. Build new admin_path
        $adminPath = $parent['admin_path'] . $branchId . '/';


        // 4. Update branch
        $sql = "
            UPDATE branches
            SET
                name = :name,
                admin_parent_id = :admin_parent_id,
                branch_type = :branch_type,
                admin_path = :admin_path,
                updated_by = :updated_by
            WHERE id = :id
              AND is_active <> 3
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':name'            => $data['name'],
            ':admin_parent_id' => $data['admin_parent_id'],
            ':branch_type'     => $data['branch_type'],
            ':admin_path'      => $adminPath,
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
}