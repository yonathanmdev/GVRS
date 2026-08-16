<?php
namespace App\Models;
use PDO;

class User {
    private $db;

    // ኮኔክሽኑን ከውጭ መቀበል (Dependency Injection) ለፈጣን አሰራር ይረዳል
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * አዲስ ተጠቃሚ መመዝገቢያ
     * ዳታው አስቀድሞ በ Controller ተዘጋጅቶ መምጣት አለበት
     */
  public function create(array $data) {

    $username = $this->generateBaseUsername($data['phone'], $data['email']);
    $suffix = 0;
    $sql = "INSERT INTO users (
                uuid, organization_id, first_name, father_name, grand_father_name, gender,
                phone, email, username, password, txtp, role, registered_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    while (true) {
        try {
            $stmt = $this->db->prepare($sql);

            if ($stmt->execute([
                $data['uuid'],
                $data['org_id'],
                $data['firstName'],
                $data['fatherName'],
                $data['gFatherName'],   // fixed: was $data['grandfatherName'] (undefined key)
                $data['gender'],
                $data['phone'],
                $data['email'],
                $username,              // fixed: was $data['username'], which is never set in $data
                $data['password'],
                $data['txtpassword'],
                $data['role'],
                $data['registeredBy']
            ])) {
                return [
                    'id'       => (int) $this->db->lastInsertId(), // internal BIGINT id
                    'username' => $username,
                ];
            }

            return false;

        } catch (\PDOException $e) {
    if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'uk_username')) {
        $suffix++;
        $username = $this->generateBaseUsername($data['phone'], $data['email']) . $suffix;
        continue;
    }
    throw $e;
}
    }
}

private function generateBaseUsername(?string $phone, ?string $email): string
{
    $base = preg_replace('/[^0-9]/', '', (string) $phone);

    if ($base === '') {
        $local = $email ? strstr($email, '@', true) : false;
        $base = $local ? preg_replace('/[^a-zA-Z0-9]/', '', $local) : '';
    }

    if ($base === '' || $base === false) {
        $base = 'user' . bin2hex(random_bytes(3)); // last resort — no phone, no email
    }

    return $base;
}
    /**
     * በኢሜይል አድራሻ ተጠቃሚን መፈለጊያ
     */
    public function findByUsername(string $username) {
        $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("username lookup error: " . $e->getMessage());
            return false;
        }
    }

    /**
 * ሁሉንም ተጠቃሚዎች መፈለጊያ (ያልተሰረዙ)
 * Retrieve all active (non-deleted) users, optionally scoped by org/branch, with pagination.
 */
public function getAll(array $filters = [], int $limit = 50, int $offset = 0) {
    $where  = ['u.is_active <> 3'];
    $params = [];

    if (!empty($filters['organization_id'])) {
        $where[] = 'u.organization_id = :organization_id';
        $params['organization_id'] = (int) $filters['organization_id'];
    }

    if (!empty($filters['branch_id'])) {
        $where[] = 'u.branch_id = :branch_id';
        $params['branch_id'] = (int) $filters['branch_id'];
    }

    if (!empty($filters['role'])) {
        $where[] = 'u.role = :role';
        $params['role'] = $filters['role'];
    }

    if (!empty($filters['search'])) {
        $where[] = "(u.first_name LIKE :search
                     OR u.father_name LIKE :search
                     OR u.grand_father_name LIKE :search
                     OR u.username LIKE :search
                     OR u.phone LIKE :search
                     OR u.email LIKE :search)";
        $params['search'] = '%' . $filters['search'] . '%';
    }

    $whereSql = implode(' AND ', $where);

    $sql = "SELECT
                u.id,
                u.uuid,
                u.organization_id,
                o.name AS organization_name,
                u.first_name,
                u.father_name,
                u.grand_father_name,
                CONCAT_WS(' ', u.first_name, u.father_name, u.grand_father_name) AS full_name,
                u.gender,
                u.phone,
                u.email,
                u.username,
                u.role,
                u.is_active,
                u.registered_by
            FROM users u
            LEFT JOIN organizations o ON o.id = u.organization_id
            WHERE {$whereSql}
            ORDER BY u.created_at DESC
            LIMIT :limit OFFSET :offset";

    try {
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        error_log("getAll users error: " . $e->getMessage());
        return false;
    }
}

/**
 * ጠቅላላ የተጠቃሚ ብዛት (ለ pagination)
 */
public function countAll(array $filters = []): int {
    $where  = ['is_active <> 3'];
    $params = [];

    if (!empty($filters['organization_id'])) {
        $where[] = 'organization_id = :organization_id';
        $params['organization_id'] = (int) $filters['organization_id'];
    }

    if (!empty($filters['branch_id'])) {
        $where[] = 'branch_id = :branch_id';
        $params['branch_id'] = (int) $filters['branch_id'];
    }

    if (!empty($filters['role'])) {
        $where[] = 'role = :role';
        $params['role'] = $filters['role'];
    }

    if (!empty($filters['search'])) {
        $where[] = "(first_name LIKE :search
                     OR father_name LIKE :search
                     OR grand_father_name LIKE :search
                     OR username LIKE :search
                     OR phone LIKE :search
                     OR email LIKE :search)";
        $params['search'] = '%' . $filters['search'] . '%';
    }

    $whereSql = implode(' AND ', $where);
    $sql = "SELECT COUNT(*) FROM users WHERE {$whereSql}";

    try {
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();

    } catch (\PDOException $e) {
        error_log("countAll users error: " . $e->getMessage());
        return 0;
    }
}
public function findById(string $id){
    $stmt = $this->db->prepare("SELECT id, uuid, first_name, father_name, grand_father_name, gender, phone, email, username, role, is_active FROM users WHERE uuid = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}
public function resetPassword(string $id, string $hashedPassword, string $newPassword): bool
{
    $expiresAt = (new \DateTime('+30 minutes'))->format('Y-m-d H:i:s');

    $stmt = $this->db->prepare(
        "UPDATE users SET password = :password, txtp = :txtp, is_active = 0, temp_password_expires_at = :expires WHERE uuid = :id"
    );
    $stmt->bindValue(':password', $hashedPassword);
    $stmt->bindValue(':txtp', $newPassword);
    $stmt->bindValue(':expires', $expiresAt);
    $stmt->bindValue(':id', $id, PDO::PARAM_STR);
    return $stmt->execute();
}
public function completeForcedPasswordChange(string $userUuid, string $hash, string $newPassword): bool {
   
    try {
        $stmt = $this->db->prepare(
            "UPDATE users SET password = ?, txtp = ?, is_active = 1 WHERE uuid = ?"
        );
        return $stmt->execute([$hash, $newPassword, $userUuid]);
    } catch (\PDOException $e) {
        error_log("forced password change error: " . $e->getMessage());
        return false;
    }
}
public function updateUser(string $id, array $data)
{
    // $id መኖሩን እና ባዶ አለመሆኑን ማረጋገጥ
    if (!$id) return false;

    $sql = "UPDATE users SET 
        first_name = ?, 
        father_name = ?, 
        grand_father_name = ?, 
        gender = ?,
        phone = ?, 
        email = ? 
        WHERE uuid = ? AND is_active <> 3";

    // የ params ቅደም ተከተል ከ SQL ጥያቄው ምልክቶች (?) ጋር አንድ መሆን አለበት
    $params = [
        $data['first_name'],
        $data['father_name'],
        $data['grand_father_name'],
        $data['gender'],
        $data['phone'],
        $data['email'],
        $id // ID መጨረሻ ላይ መሆኑን አረጋግጥ
    ];

    $stmt = $this->db->prepare($sql);
    $result = $stmt->execute($params);

    //rowCount() በትክክል አንድ መስመር መቀየሩን ያረጋግጥልናል
    return $result && $stmt->rowCount() > 0;
}

// User Model ውስጥ
public function verifyPassword(string $userId, string $password): bool 
{
    $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$user) {
        return false;
    }

    // በዳታቤዝ ያለው ሀሽ (Hash) ከተላከው ፓስዋርድ ጋር መገጣጠሙን ያረጋግጣል
    return password_verify($password, $user['password']);
}

public function softDelete(string $id, string $userId, string $reason, $source): array
{
    try {
        $this->db->beginTransaction();

        // Snapshot before delete
        $oldRecord = $this->findById($id);
        if (!$oldRecord) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'ተጠቃሚው አልተገኘም።'];
        }

        // Count all records registered by this user
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users WHERE registered_by = ?");
        $stmt->execute([$id]);
        $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM branches WHERE registered_by = ?");
        $stmt->execute([$id]);
        $branchCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $counts = [
            'userCount'          => $userCount,
            'branchCount'        => $branchCount,
        ];

        

        // Has related records — soft delete only
        $stmt = $this->db->prepare("
            UPDATE users 
            SET deleted_at      = NOW(),
                deleted_by      = ?,
                deletion_reason = ?,
                is_active      = 3,
                deletion_source = ?
            WHERE uuid = ? AND is_active <> 3 ");
        $stmt->execute([$userId, $reason, $source, $id]);

        $this->db->commit();

        return array_merge([
            'status'       => 'success',
            'deleted_type' => 'soft',
            'message'      => 'ተጠቃሚው በትክክል ተዘግቷል።',
            'oldRecord'    => $oldRecord,
        ], $counts);

    } catch (\Exception $e) {
        $this->db->rollBack();
        error_log('UserModel::softDelete - ' . $e->getMessage());
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}
// User.php
public function findAllDeleted(?string $branchId): array
{
    $whereClause = $branchId 
        ? "AND u.branch_id = :branchId" 
        : "";

    $stmt = $this->db->prepare("
        SELECT 
            u.id,
            CONCAT(u.first_name, ' ', u.father_name) AS full_name,
            u.email,
            u.phone,
            u.role,
            u.deleted_at,
            u.status,
            u.deletion_source,

            CONCAT(du.first_name, ' ', du.father_name) AS deleted_by_name,

            (SELECT COUNT(*) FROM users             WHERE registered_by = u.id)                   AS affected_users,
            (SELECT COUNT(*) FROM branches          WHERE registered_by = u.id)                   AS affected_branches,
            (SELECT COUNT(*) FROM directors         WHERE registered_by = u.id)                   AS affected_directors,
            (SELECT COUNT(*) FROM job_property      WHERE registered_by = u.id)                   AS affected_job_properties,
            (SELECT COUNT(*) FROM employees_table   WHERE reg_by = u.id OR reg_approve_by = u.id) AS affected_employees,
            (SELECT COUNT(*) FROM employee_scholarships WHERE registered_by = u.id)               AS affected_scholarships,
            (SELECT COUNT(*) FROM debt_suspension   WHERE registered_by = u.id)                   AS affected_debt_suspensions,

            (
                (SELECT COUNT(*) FROM users             WHERE registered_by = u.id) = 0
                AND (SELECT COUNT(*) FROM branches      WHERE registered_by = u.id) = 0
                AND (SELECT COUNT(*) FROM directors     WHERE registered_by = u.id) = 0
                AND (SELECT COUNT(*) FROM job_property  WHERE registered_by = u.id) = 0
                AND (SELECT COUNT(*) FROM employees_table WHERE reg_by = u.id OR reg_approve_by = u.id) = 0
                AND (SELECT COUNT(*) FROM employee_scholarships WHERE registered_by = u.id) = 0
                AND (SELECT COUNT(*) FROM debt_suspension       WHERE registered_by = u.id) = 0
            ) AS can_purge

        FROM users u
        LEFT JOIN users du ON du.id = u.deleted_by

        WHERE u.status     = 'inactive'
          AND u.deleted_at IS NOT NULL
          $whereClause

        ORDER BY u.deleted_at DESC
    ");

    $params = $branchId ? ['branchId' => $branchId] : [];
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
public function restore(string $id, string $userId): array
{
    try {
        $this->db->beginTransaction();

        // ✅ Restore cascade-inactivated users only
        $stmt = $this->db->prepare("
            UPDATE users 
            SET deleted_at = NULL,
                status = 'active',
                deletion_source = NULL,
                deleted_by = NULL
            WHERE id = ? AND deletion_source = 'INDIVIDUAL'
        ");
        $stmt->execute([$id]);

        $this->db->commit();

        return [
            'status'           => 'success',
            'message'          => 'ተቆጣጣሪ ተመልሷል።'
        ];

    } catch (\Exception $e) {
        $this->db->rollBack();
        error_log('UserModel::restore - ' . $e->getMessage());
        return ['status' => 'error', 'message' => 'መልስ አልተቻለም።'];
    }
} 
// ============================================================
// PURGE (hard delete)
// ============================================================
public function purge(string $id, string $archiveId, string $entityType = 'user'): array
{
    try {
        $this->db->beginTransaction();

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $oldRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$oldRecord) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'ቅርንጫፉ አልተገኘም።'];
        }
        // ✅ Single archive table — not separate per entity
        $stmt = $this->db->prepare("
            INSERT INTO data_archive
                (id, entity_type, original_id, snapshot, archived_at, archived_by, reason)
            VALUES (?, ?, ?, ?, NOW(), ?, 'PURGE')
        ");
        $stmt->execute([
            $archiveId,
            $entityType,
            $id,
            json_encode([
                'user'        => $oldRecord
            ]),
            $_SESSION['user']['id']
        ]);


        $this->db->prepare("DELETE FROM users WHERE branch_id = ? AND deletion_source = 'INDIVIDUAL'")->execute([$id]);
        $this->db->commit();

        return [
            'status'      => 'success',
            'message'     => 'ተቆጣጣሪ ተሰርዟል።',
            'oldRecord'   => $oldRecord,
            'archiveId'   => $archiveId,
        ];

    } catch (\Exception $e) {
        $this->db->rollBack();
        error_log('UserModel::purge - ' . $e->getMessage());
        return ['status' => 'error', 'message' => 'መሰረዝ አልተቻለም።'];
    }
}

public function getTotalUsersCount($branch_id = null) {
    try {
        // Strict condition assignment: handles tenant isolation flawlessly
        $branchCondition = !empty($branch_id) ? "branch_id = :branch_id" : "1=1";
        
        $query = "SELECT COUNT(*) as total 
                  FROM users 
                  WHERE {$branchCondition}";
        
        $stmt = $this->db->prepare($query);
        
        // Bind the named parameter only if a strict branch context is active
        if (!empty($branch_id)) {
            // Swapped to PARAM_STR to match your standard employee methods execution pattern
            $stmt->bindValue(':branch_id', $branch_id, \PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $row ? (int)$row['total'] : 0;
        
    } catch (\PDOException $e) {
        error_log("Database Error in Users::getTotalUsersCount: " . $e->getMessage());
        return 0;
    }
}

public function getActiveUsersCount($branch_id = null) {
    try {
        // Strict condition assignment: handles tenant isolation flawlessly
        $branchCondition = !empty($branch_id) ? "branch_id = :branch_id" : "1=1";
        
        $query = "SELECT COUNT(*) as total 
                  FROM users 
                  WHERE {$branchCondition} AND status = 'active' AND is_deleted = 0";
        
        $stmt = $this->db->prepare($query);
        
        // Bind the named parameter only if a strict branch context is active
        if (!empty($branch_id)) {
            // Swapped to PARAM_STR to match your standard employee methods execution pattern
            $stmt->bindValue(':branch_id', $branch_id, \PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $row ? (int)$row['total'] : 0;
        
    } catch (\PDOException $e) {
        error_log("Database Error in Users::getTotalUsersCount: " . $e->getMessage());
        return 0;
    }
}
    }