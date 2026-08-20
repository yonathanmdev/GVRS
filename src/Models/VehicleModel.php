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

public function getRegisteredVehicles($branchId = null, int $limit = 25, int $offset = 0, string $search = ''): array
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
            (!empty($search) ? " AND (
                v.plate_number   LIKE :search1 OR
                v.model          LIKE :search2 OR
                v.chassis_number LIKE :search3 OR
                v.engine_number  LIKE :search4 OR
                b.brand_name     LIKE :search5 OR
                ctl.cartype      LIKE :search6
            )" : "") . "
            ORDER BY v.created_at DESC
            LIMIT :lim OFFSET :offs";

    $stmt = $this->db->prepare($sql);

    if (!empty($branchId)) {
        $stmt->bindValue(':branch_id', $branchId, \PDO::PARAM_INT);
    }
    if (!empty($search)) {
        $term = '%' . $search . '%';
        foreach (range(1, 6) as $i) {
            $stmt->bindValue(":search{$i}", $term, \PDO::PARAM_STR);
        }
    }
    $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offs', $offset, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function countRegisteredVehicles($branchId = null, string $search = ''): int
{
    $sql = "SELECT COUNT(*) AS total
            FROM vehicles v
            LEFT JOIN car_type ct     ON ct.id = v.vehicle_type AND ct.is_deleted = 0
            LEFT JOIN brand b         ON b.id = ct.brand_id
            LEFT JOIN cartypelist ctl ON ctl.id = ct.type_name
            WHERE v.is_active <> 3" .
            (!empty($branchId) ? " AND v.branch_id = :branch_id" : "") .
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
    if (!empty($search)) {
        $term = '%' . $search . '%';
        foreach (range(1, 6) as $i) {
            $stmt->bindValue(":search{$i}", $term, \PDO::PARAM_STR);
        }
    }
    $stmt->execute();

    return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
}
    public function getDistinctBrands(): array
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
}
