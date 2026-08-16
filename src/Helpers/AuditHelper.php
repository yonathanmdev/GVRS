<?php
namespace App\Helpers;

use App\Models\AuditLog;
class AuditHelper
{
    private static ?\PDO      $db    = null;
    private static ?AuditLog  $model = null;

  
    public static function init(\PDO $db): void
    {
        self::$db    = $db;
        self::$model = new AuditLog($db);
    }
    public static function log(
        string  $action,
        string  $entityType,
       int|string|null  $entityId  = null,
        mixed   $oldValues = null,
        mixed   $newValues = null,
        array   $metadata  = []
    ): bool {
        $userId = $_SESSION['user']['id'] ?? null;   // adjust to your session key
        return self::model()->log($userId, $action, $entityType, $entityId, $oldValues, $newValues, $metadata);
    }

    /**
     * Convenience for actions that already know the user ID
     * (e.g. login events, where the session may not be set yet).
     */
    public static function logAs(
        ?int $userId,
        string  $action,
        string  $entityType,
       int|string|null  $entityId  = null,
        mixed   $oldValues = null,
        mixed   $newValues = null,
        array   $metadata  = []
    ): bool {
        return self::model()->log($userId, $action, $entityType, $entityId, $oldValues, $newValues, $metadata);
    }


    public static function getLogs(array $filters = [], int $limit = 25, int $offset = 0): array
    {
        return self::model()->getLogs($filters, $limit, $offset);
    }


    public static function getStats(?string $dateFrom = null): array
    {
        return self::model()->getStats($dateFrom);
    }

    public static function getFilterOptions(): array
    {
        return self::model()->getFilterOptions();
    }

    public static function findById(string $id): ?array
    {
        return self::model()->findById($id);
    }

    public static function diff(array $before, array $after): array
    {
        $changed = array_keys(
            array_filter($after, fn($v, $k) => ($before[$k] ?? null) !== $v, ARRAY_FILTER_USE_BOTH)
        );

        $oldSlice = array_intersect_key($before, array_flip($changed));
        $newSlice = array_intersect_key($after,  array_flip($changed));

        return [$oldSlice, $newSlice];
    }

    // =========================================================================
    //  Private
    // =========================================================================

    private static function model(): AuditLog
    {
        if (self::$model === null) {
            throw new \RuntimeException(
                'AuditHelper not initialised. Call AuditHelper::init($db) in your bootstrap.'
            );
        }
        return self::$model;
    }
}