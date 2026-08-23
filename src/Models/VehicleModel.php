<?php

namespace App\Models;

use PDO;

class VehicleModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

   public function create(array $data): bool
{
    $sql = "INSERT INTO vehicles (
                uuid, branch_id, zone_id, plate_number, vehicle_type, model,
                chassis_number, engine_number, capacity, manufactured_year,
                estimated_price, purchase_year, vehicle_status, is_active, registered_by
            ) VALUES (
                :uuid, :branch_id, :zone_id, :plate_number, :vehicle_type, :model,
                :chassis_number, :engine_number, :capacity, :manufactured_year,
                :estimated_price, :purchase_year, :vehicle_status, 1, :registered_by
            )";

    $stmt = $this->db->prepare($sql);

    try {
        return $stmt->execute([
            ':uuid'              => $data['uuid'],
            ':branch_id'         => $data['branch_id'],
            ':zone_id'           => $data['zone_id'],
            ':plate_number'      => $data['plate_number'],
            ':vehicle_type'      => $data['vehicle_type'],
            ':model'             => $data['model'],
            ':chassis_number'    => $data['chassis_number'],
            ':engine_number'     => $data['engine_number'] ?: null,
            ':capacity'          => $data['capacity'] ?: null,
            ':manufactured_year' => $data['manufactured_year'] !== '' ? (int) $data['manufactured_year'] : null,
            ':estimated_price'   => $data['estimated_price'] !== '' ? (float) $data['estimated_price'] : null,
            ':purchase_year'     => $data['purchase_year'] !== '' ? (int) $data['purchase_year'] : null,
            ':vehicle_status'    => $data['vehicle_status'] ?: null,
            ':registered_by'     => $data['registered_by']
        ]);
    } catch (\PDOException $e) {
        if ($e->getCode() === '23000') {
            // unique constraint hit — most likely duplicate plate_number
            throw new \RuntimeException('duplicate_plate_number', 0, $e);
        }
        throw $e;
    }
}
public function getRegisteredVehicles(
    $branchId = null,
    int $limit = 25,
    int $offset = 0,
    string $search = '',
    ?int $level = null,
    ?array $branchTypes = null
): array {

    $params = [];

    $typePlaceholders = [];
    if (!empty($branchTypes)) {
        foreach (array_values($branchTypes) as $i => $type) {
            $key = "btype_{$i}";
            $typePlaceholders[] = ":{$key}";
            $params[$key] = $type;
        }
    }

    $sql = "SELECT 
                v.id,
                v.uuid,
                v.branch_id,
                v.zone_id,
                v.plate_number,
                v.vehicle_type,
                v.model,
                v.chassis_number,
                v.engine_number,
                v.capacity,
                v.manufactured_year,
                v.estimated_price,
                v.purchase_year,
                v.vehicle_status,
                v.is_active,
                v.registered_by,
                v.created_at,

                b.brand_name,
                ctl.cartype   AS type_name,

                br.name       AS branch_name,
                z.name        AS zone_name

            FROM vehicles v

            LEFT JOIN car_type ct       ON ct.id = v.vehicle_type AND ct.is_deleted = 0
            LEFT JOIN brand b           ON b.id = ct.brand_id
            LEFT JOIN cartypelist ctl   ON ctl.id = ct.type_name

            LEFT JOIN branches br ON br.id = v.branch_id
            LEFT JOIN branches z  ON z.id = v.zone_id

            WHERE v.is_active <> 3" .
            (!empty($branchId) ? " AND v.branch_id = :branch_id" : "") .
            ($level !== null ? " AND br.level = :level" : "") .
            (!empty($branchTypes) ? " AND br.branch_type IN (" . implode(', ', $typePlaceholders) . ")" : "") .
            (!empty($search) ? " AND (
                v.plate_number   LIKE :search1 OR
                v.model          LIKE :search2 OR
                v.chassis_number LIKE :search3 OR
                v.engine_number  LIKE :search4 OR
                b.brand_name     LIKE :search5 OR
                ctl.cartype      LIKE :search6 OR
                br.name          LIKE :search7 OR
                z.name           LIKE :search8

            )" : "") . "
            ORDER BY v.created_at DESC
            LIMIT :lim OFFSET :offs";

    $stmt = $this->db->prepare($sql);

    if (!empty($branchId)) {
        $stmt->bindValue(':branch_id', $branchId, \PDO::PARAM_INT);
    }
    if ($level !== null) {
        $stmt->bindValue(':level', $level, \PDO::PARAM_INT);
    }
    foreach ($params as $key => $val) {
        $stmt->bindValue(":{$key}", $val, \PDO::PARAM_STR);
    }
    if (!empty($search)) {
        $term = '%' . $search . '%';
        foreach (range(1, 8) as $i) {
            $stmt->bindValue(":search{$i}", $term, \PDO::PARAM_STR);
        }
    }
    $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offs', $offset, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function countRegisteredVehicles(
    $branchId = null,
    string $search = '',
    ?int $level = null,
    ?array $branchTypes = null
): int {

    $params = [];

    $typePlaceholders = [];
    if (!empty($branchTypes)) {
        foreach (array_values($branchTypes) as $i => $type) {
            $key = "btype_{$i}";
            $typePlaceholders[] = ":{$key}";
            $params[$key] = $type;
        }
    }

    $sql = "SELECT COUNT(*) AS total
            FROM vehicles v
            LEFT JOIN car_type ct     ON ct.id = v.vehicle_type AND ct.is_deleted = 0
            LEFT JOIN brand b         ON b.id = ct.brand_id
            LEFT JOIN cartypelist ctl ON ctl.id = ct.type_name
            LEFT JOIN branches br     ON br.id = v.branch_id
            WHERE v.is_active <> 3" .
            (!empty($branchId) ? " AND v.branch_id = :branch_id" : "") .
            ($level !== null ? " AND br.level = :level" : "") .
            (!empty($branchTypes) ? " AND br.branch_type IN (" . implode(', ', $typePlaceholders) . ")" : "") .
            (!empty($search) ? " AND (
                v.plate_number   LIKE :search1 OR
                v.model          LIKE :search2 OR
                v.chassis_number LIKE :search3 OR
                v.engine_number  LIKE :search4 OR
                b.brand_name     LIKE :search5 OR
                ctl.cartype      LIKE :search6 
            )" : "");

    $stmt = $this->db->prepare($sql);

    if (!empty($branchId)) {
        $stmt->bindValue(':branch_id', $branchId, \PDO::PARAM_INT);
    }
    if ($level !== null) {
        $stmt->bindValue(':level', $level, \PDO::PARAM_INT);
    }
    foreach ($params as $key => $val) {
        $stmt->bindValue(":{$key}", $val, \PDO::PARAM_STR);
    }
    if (!empty($search)) {
        $term = '%' . $search . '%';
        foreach (range(1, 6) as $i) {
            $stmt->bindValue(":search{$i}", $term, \PDO::PARAM_STR);
        }
    }
    $stmt->execute();

    return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
}    public function getDistinctBrands(): array
    {
        $sql = "SELECT DISTINCT brand_name, brand_id
                FROM view_car_details
                WHERE is_deleted=0
                ORDER BY brand_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Types for a given brand - returns everything the option needs
    // (uuid becomes the option value, service_name/measurement become
    // data attributes) so the frontend needs no second lookup on select
    public function getTypesByBrand(string $brand): array
    {
        $sql = "SELECT type_list_id, type_name, car_type_id, service_name, measurement
                FROM view_car_details
                WHERE brand_id = :brand
                  AND is_deleted=0
                ORDER BY type_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('brand', $brand);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
/**
 * Public: general-purpose lookup by UUID. No locking — safe to call
 * anywhere (display, edit forms, dropdowns, etc.)
 */
public function findByUuid(string $uuid): ?array
{
    $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE uuid = :uuid LIMIT 1");
    $stmt->execute([':uuid' => $uuid]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}


/**
 * Private: row-locking lookup for use ONLY inside an active transaction
 * (purge, restore, or any read-then-mutate flow). Locks just the
 * vehicles row — no joins, so no risk of locking unrelated tables.
 */
private function lockByUuid(string $uuid): ?array
{
    $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE uuid = :uuid FOR UPDATE");
    $stmt->execute([':uuid' => $uuid]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

public function findByUuidWithDetails(string $uuid): ?array
{
    $sql = "SELECT 
                v.id,
                v.uuid,
                v.branch_id,
                v.zone_id,
                v.plate_number,
                v.vehicle_type,
                v.model,
                v.chassis_number,
                v.engine_number,
                v.capacity,
                v.manufactured_year,
                v.estimated_price,
                v.purchase_year,
                v.vehicle_status,
                v.is_active,
                v.registered_by,
                v.created_at,

                b.id          AS brand_id,
                b.brand_name,
                ctl.cartype   AS type_name,

                br.name       AS branch_name,
                z.name        AS zone_name

            FROM vehicles v

            LEFT JOIN car_type ct       ON ct.id = v.vehicle_type AND v.is_active = 1
            LEFT JOIN brand b           ON b.id = ct.brand_id
            LEFT JOIN cartypelist ctl   ON ctl.id = ct.type_name

            LEFT JOIN branches br ON br.id = v.branch_id
            LEFT JOIN branches z  ON z.id = v.zone_id

            WHERE v.uuid = :uuid
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([':uuid' => $uuid]);

    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $row ?: null;
}

public function updateByUuid(array $data): bool
{
    $this->db->beginTransaction();

    try {
        $vehicle = $this->lockByUuid($data['uuid']);

        if (!$vehicle) {
            $this->db->rollBack();
            return false;
        }


        $sql = "UPDATE vehicles SET
                    plate_number      = :plate_number,
                    vehicle_type      = :vehicle_type,
                    model             = :model,
                    chassis_number    = :chassis_number,
                    engine_number     = :engine_number,
                    capacity          = :capacity,
                    manufactured_year = :manufactured_year,
                    estimated_price   = :estimated_price,
                    purchase_year     = :purchase_year,
                    vehicle_status    = :vehicle_status,
                    updated_by        = :updated_by,
                    updated_at        = :updated_at
                WHERE branch_id = :branch_id
                  AND id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':plate_number'      => $data['plate_number'],
            ':vehicle_type'      => $data['vehicle_type'],
            ':model'             => $data['model'],
            ':chassis_number'    => $data['chassis_number'],
            ':engine_number'     => $data['engine_number'],
            ':capacity'          => $data['capacity'],
            ':manufactured_year' => $data['manufactured_year'],
            ':estimated_price'   => $data['estimated_price'],
            ':purchase_year'     => $data['purchase_year'],
            ':vehicle_status'    => $data['vehicle_status'],
            ':updated_by'        => $data['updated_by'],
            ':updated_at'        => date('Y-m-d H:i:s'),
            ':branch_id'         => $vehicle['branch_id'],
            ':id'                => $vehicle['id'],
        ]);


        /*
         * The UPDATE executed successfully.
         *
         * Do NOT use rowCount() > 0 as the success condition.
         * rowCount() may be 0 when the submitted values are
         * exactly the same as the existing values.
         */
        $this->db->commit();

        return true;

    } catch (\Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        error_log(
            'Vehicle update failed: ' . $e->getMessage()
        );

        return false;
    }
}
public function purge(string $uuid, string $archiveId, int $purgedBy, string $reason): array
{
    $this->db->beginTransaction();

    try {
        $vehicle = $this->lockByUuid($uuid);

        if (!$vehicle) {
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'ተሽከርካሪው አልተገኘም።'];
        }

        $archive = new Archive($this->db);
        $archive->create(
            entityType: 'vehicle',
            originalId: $vehicle['uuid'],
            snapshot:   $vehicle,
            archivedBy: $purgedBy,
            reason:     $reason,
            archiveId:  $archiveId
        );

        $del = $this->db->prepare("DELETE FROM vehicles WHERE uuid = :uuid");
        $del->execute([':uuid' => $uuid]);

        $this->db->commit();

        return [
            'status'    => 'success',
            'message'   => 'ተሽከርካሪው ሙሉ በሙሉ ተሰርዟል።',
            'archiveId' => $archiveId,
        ];
    } catch (\Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}
}
