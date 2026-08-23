<?php
namespace App\Models;
use PDO;
class FunctionalOrgModel {
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
            'uuid'             => $data['uuid'],
            'organization_id'  => $data['organization_id'],
            'name'             => $data['branch_name'],
            'branch_type'      => $data['branch_type'],
            'registered_by'    => $data['registered_by'],
        ]);

        $internalId = (int) $this->db->lastInsertId();

      // functional_path is self-referencing for a root node: /{internal_id}/
// This lets child rows later build their path as "{parent.functional_path}{child_internal_id}/"
$functionalPath = '/' . $internalId . '/';

$updateSql = "UPDATE branches SET functional_path = :functional_path WHERE id = :id";
$updateStmt = $this->db->prepare($updateSql);
$updateStmt->execute([
    'functional_path' => $functionalPath,
    'id'               => $internalId,
]);

        return $internalId;
    }

public function getAll(int $level, $branchType = null): array
{
    $sql = "SELECT id, uuid, admin_parent_id, name, branch_type, level, 
                   admin_path, functional_parent_id, functional_path, registered_by
            FROM branches
            WHERE level = :level AND is_active <> 3";

    $params = [':level' => $level];

    if ($branchType !== null) {
        if (is_array($branchType)) {
            // e.g. ['bureau', 'baleseltan'] for a grouped menu view
            $placeholders = [];
            foreach ($branchType as $i => $type) {
                $key = ":type{$i}";
                $placeholders[] = $key;
                $params[$key] = $type;
            }
            $sql .= " AND branch_type IN (" . implode(',', $placeholders) . ")";
        } else {
            // single type, e.g. 'woreda'
            $sql .= " AND branch_type = :branch_type";
            $params[':branch_type'] = $branchType;
        }
    }

    $sql .= " ORDER BY name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
public function updateBureau(array $data): array
{
    // 1. Fetch the current row BEFORE updating
    $findSql = "SELECT id, branch_type FROM branches WHERE uuid = :uuid AND is_active <> 3";
    $findStmt = $this->db->prepare($findSql);
    $findStmt->execute(['uuid' => $data['uuid']]);
    $existing = $findStmt->fetch(\PDO::FETCH_ASSOC);

    if (!$existing) {
        return ['status' => 'error', 'message' => 'Branch not found.'];
    }
    $branchId = (int) $existing['id'];

    $updateSql = "UPDATE branches
                  SET name = :name,
                      branch_type = :branch_type,
                      updated_by = :updated_by
                  WHERE uuid = :uuid
                    AND is_active <> 3";

    $stmt = $this->db->prepare($updateSql);
    $stmt->execute([
        'name'        => $data['name'],
        'branch_type' => $data['branch_type'],
        'updated_by'  => $data['updated_by'],
        'uuid'        => $data['uuid'],
    ]);

    if ($stmt->rowCount() === 0) {
        return ['status' => 'error', 'message' => 'Update failed or branch type not eligible for this operation.'];
    }

    return ['status' => 'success', 'id' => $branchId];
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
    // Pull allowed types from branch_type, excluding 'bureau' —
    // same eligible set used by countOfficesWithBureau(), so
    // the count and the list always agree.
    $allTypes = array_column($this->getAllBranchTypes(), 'type_in_eng');
    $allowedTypes = array_values(array_diff($allTypes, ['bureau']));

    $typePlaceholders = [];
    $params = [];

    foreach ($allowedTypes as $i => $type) {
        $key = "type_{$i}";
        $typePlaceholders[] = ":{$key}";
        $params[$key] = $type;
    }

    $inClause = implode(', ', $typePlaceholders);

    $sql = "SELECT 
                aco.id, aco.uuid, aco.name AS office_name, aco.branch_type, aco.level,
                aco.functional_path, aco.organization_id, aco.is_active,
                b.id AS bureau_id, b.uuid AS bureau_uuid, b.name AS bureau_name, b.branch_type AS bureau_type
            FROM branches aco
            JOIN branches b ON b.id = aco.functional_parent_id
            WHERE aco.level = :level
              AND aco.branch_type IN ({$inClause})";

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
    // Pull allowed types from branch_type, excluding 'bureau' —
    // this count is specifically for non-bureau offices under a bureau.
    $allTypes = array_column($this->getAllBranchTypes(), 'type_in_eng');
    $allowedTypes = array_values(array_diff($allTypes, ['bureau']));

    $placeholders = [];
    $params = ['level' => $level];

    foreach ($allowedTypes as $i => $type) {
        $key = "type_{$i}";
        $placeholders[] = ":{$key}";
        $params[$key] = $type;
    }

    $inClause = implode(', ', $placeholders);

    $sql = "SELECT COUNT(*) FROM branches w
            JOIN branches z ON z.id = w.functional_parent_id
            WHERE w.branch_type IN ({$inClause})
            AND w.is_active <> 3
            AND w.level = :level";

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

public function getAllAccountableOfficesforSelectedBureau(int $level, ?int $bureau_id = null): array
{
    $allTypes = array_column($this->getAllBranchTypes(), 'type_in_eng');
    $allowedTypes = array_values(array_diff($allTypes, ['memriya']));

    $typePlaceholders = [];
    $params = [];

    foreach ($allowedTypes as $i => $type) {
        $key = "type_{$i}";
        $typePlaceholders[] = ":{$key}";
        $params[$key] = $type;
    }

    $inClause = implode(', ', $typePlaceholders);

    $sql = "SELECT 
                aco.id, aco.uuid, aco.name AS office_name, aco.branch_type, aco.level,
                aco.functional_path, aco.organization_id, aco.is_active,
                b.id AS bureau_id, b.uuid AS bureau_uuid, b.name AS bureau_name, b.branch_type AS bureau_type
            FROM branches aco
            JOIN branches b ON b.id = aco.functional_parent_id
            WHERE aco.level = :level
              AND aco.branch_type IN ({$inClause})";

    if ($bureau_id !== null) {
        $sql .= " AND aco.functional_parent_id = :bureau_id";
        $params['bureau_id'] = $bureau_id;
    }

    $sql .= " ORDER BY b.name ASC, aco.name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue('level', $level, \PDO::PARAM_INT);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
public function getAllBranchTypes(): array
{
    $sql = "SELECT id, type_in_eng, type_in_am
            FROM branch_type
            ORDER BY id ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
 public function softDelete(string $id, string $userId, string $reason, string $source): array
{
    // 1. Find the branch first
    $findSql = "SELECT id, functional_path, admin_path, branch_type, is_active
                FROM branches WHERE uuid = :uuid";
    $findStmt = $this->db->prepare($findSql);
    $findStmt->execute(['uuid' => $id]);
    $branch = $findStmt->fetch(\PDO::FETCH_ASSOC);

    if (!$branch) {
        throw new \InvalidArgumentException("Branch not found: {$id}");
    }

    if ((int) $branch['is_active'] === 3) {
        throw new \InvalidArgumentException("{$branch['branch_type']} is already deleted: {$id}");
    }

    // 2. Pick the correct path column based on branch_type
    $adminTypes = ['regio', 'zone', 'ketema_woreda', 'woreda',  'kifle_ketema'];
    $pathColumn = in_array($branch['branch_type'], $adminTypes, true) ? 'admin_path' : 'functional_path';
    $path = $branch[$pathColumn];

    if (empty($path)) {
        throw new \RuntimeException("Branch {$id} has no {$pathColumn} set — cannot cascade delete.");
    }

    $this->db->beginTransaction();

    try {
        // 3a. Soft delete the SELECTED branch with the individual, user-supplied reason/source
        $selfSql = "UPDATE branches 
                     SET is_active = 3, 
                         deleted_by = :deleted_by, 
                         deletion_source = :deletion_source, 
                         deletion_reason = :deletion_reason,
                         deleted_at = NOW()
                     WHERE uuid = :uuid
                     AND is_active <> 3";
        $selfStmt = $this->db->prepare($selfSql);
        $selfStmt->execute([
            'deleted_by'      => $userId,
            'deletion_source' => $source,        // 'INDIVIDUAL'
            'deletion_reason' => $reason,
            'uuid'            => $id,
        ]);
        $branchDeletedCount = $selfStmt->rowCount();

        // 3b. Soft delete all DESCENDANTS with a forced CASCADE source
        $cascadeSql = "UPDATE branches 
                        SET is_active = 3, 
                            deleted_by = :deleted_by, 
                            deletion_source = 'CASCADE', 
                            deletion_reason = :deletion_reason,
                            deleted_at = NOW()
                        WHERE {$pathColumn} LIKE :path_prefix
                        AND uuid <> :uuid
                        AND is_active <> 3";
        $cascadeStmt = $this->db->prepare($cascadeSql);
        $cascadeStmt->execute([
            'deleted_by'      => $userId,
            'deletion_reason' => "Cascaded from parent branch deletion (uuid: {$id})",
            'path_prefix'     => $path . '%',
            'uuid'            => $id,
        ]);
        $cascadedCount = $cascadeStmt->rowCount();

        $totalBranchCount = $branchDeletedCount + $cascadedCount;

        // 3c. Soft delete users assigned to this branch and its descendants (if applicable)
        $userSql = "UPDATE users 
                     SET is_active = 3, deleted_at = NOW()
                     WHERE branch_id IN (
                         SELECT id FROM branches 
                         WHERE uuid = :uuid OR {$pathColumn} LIKE :path_prefix
                     )
                     AND is_active <> 3";
        $userStmt = $this->db->prepare($userSql);
        $userStmt->execute([
            'uuid'        => $id,
            'path_prefix' => $path . '%',
        ]);
        $userCount = $userStmt->rowCount();

        $this->db->commit();

        return [
            'status'      => 'success',
            'oldRecord'   => $branch,
            'branchCount' => $totalBranchCount,
            'userCount'   => $userCount,
            'id'    => $branch['id'],
        ];

    } catch (\Throwable $e) {
        $this->db->rollBack();
        throw $e;
    }
}
}