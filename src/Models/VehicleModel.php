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
public function getVehicleBrands(): array {
    $sql = "SELECT * FROM vehicle_models";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}