<?php

namespace App\Models;

use PDO;
use Ramsey\Uuid\Uuid;

class Archive
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Snapshot any record as JSON into data_archive.
     * Returns the archive row's UUID so the caller can trace back to it.
     */
    public function create(
        string $entityType,
        string $originalId,
        array $snapshot,
        int $archivedBy,
        string $reason,
        ?string $archiveId = null
    ): string {
        $archiveId ??= Uuid::uuid7()->toString();

        $stmt = $this->db->prepare(
            "INSERT INTO data_archive
                (id, entity_type, original_id, snapshot, archived_by, reason)
             VALUES
                (:id, :entity_type, :original_id, :snapshot, :archived_by, :reason)"
        );

        $stmt->execute([
            ':id'          => $archiveId,
            ':entity_type' => $entityType,
            ':original_id' => $originalId,
            ':snapshot'    => json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ':archived_by' => $archivedBy,
            ':reason'      => $reason,
        ]);

        return $archiveId;
    }

    public function findById(string $archiveId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM data_archive WHERE id = :id");
        $stmt->execute([':id' => $archiveId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function listByEntity(string $entityType, string $originalId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM data_archive
             WHERE entity_type = :entity_type AND original_id = :original_id
             ORDER BY archived_at DESC"
        );
        $stmt->execute([':entity_type' => $entityType, ':original_id' => $originalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markRestored(string $archiveId, int $restoredBy): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE data_archive SET restored_at = NOW(), restored_by = :by WHERE id = :id"
        );
        return $stmt->execute([':by' => $restoredBy, ':id' => $archiveId]);
    }
}