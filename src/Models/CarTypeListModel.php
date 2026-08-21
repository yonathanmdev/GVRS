<?php

namespace App\Models;

use PDO;
use PDOException;

class CarTypeListModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $this->db = $db;
    }

    /**
     * ያልተሰረዙ የ cartypelist መረጃዎችን ያመጣል
     */
    public function getCarTypeLists(): array
    {
        try {
            $sql = "SELECT 
                        id, 
                        uuid, 
                        cartype, 
                        carcatagory, 
                        created_at 
                    FROM cartypelist 
                    WHERE is_deleted = 0 
                    ORDER BY id DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error in getCarTypeLists: " . $e->getMessage());
            return [];
        }
    }

    /**
     * አዲስ የመኪና/ማሽን አይነት ይመዘግባል
     */
    public function createCarType(array $data): bool
    {
        try {
            $sql = "INSERT INTO cartypelist (
                        uuid, 
                        cartype, 
                        carcatagory, 
                        registered_by
                    ) VALUES (
                        :uuid, 
                        :cartype, 
                        :carcatagory, 
                        :registered_by
                    )";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':uuid'          => $data['uuid'],
                ':cartype'       => $data['cartype'],
                ':carcatagory'   => $data['carcatagory'],
                ':registered_by' => $data['registered_by']
            ]);
        } catch (PDOException $e) {
            error_log("Error in createCarType: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ተመሳሳይ cartype በዳታቤዝ ውስጥ ቀድሞ መኖሩን ያረጋግጣል
     */
    public function isDuplicate(string $cartype): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM cartypelist 
                    WHERE LOWER(cartype) = LOWER(:cartype) 
                      AND is_deleted = 0";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':cartype' => trim($cartype)]);
            return ((int) $stmt->fetchColumn()) > 0;
        } catch (PDOException $e) {
            error_log("Error in isDuplicate: " . $e->getMessage());
            return false;
        }
    }

    /**
     * የመኪና/ማሽን አይነት መረጃ ያስተካክላል (Update)
     */
    public function updateCarType(array $data): bool
    {
        try {
            $sql = "UPDATE cartypelist SET 
                        cartype = :cartype,
                        carcatagory = :carcatagory,
                        updated_by = :updated_by
                    WHERE uuid = :uuid AND is_deleted = 0";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':cartype'     => $data['cartype'],
                ':carcatagory' => $data['carcatagory'],
                ':updated_by'  => $data['updated_by'],
                ':uuid'        => $data['uuid']
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error in updateCarType: " . $e->getMessage());
            return false;
        }
    }

    /**
     * መረጃን በቋሚነት ሳይሆን በ Soft Delete ይሰርዛል
     */
    public function deleteCarType(string $uuid, int $userId): bool
    {
        try {
            $sql = "UPDATE cartypelist SET 
                        is_deleted = 1, 
                        updated_by = :updated_by 
                    WHERE uuid = :uuid AND is_deleted = 0";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':updated_by' => $userId,
                ':uuid'       => $uuid
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error in deleteCarType: " . $e->getMessage());
            return false;
        }
    }
}