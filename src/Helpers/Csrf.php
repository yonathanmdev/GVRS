<?php
namespace App\Helpers;

class Csrf {

    public static function token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Validate a submitted token. Pass it explicitly (from $_POST, JSON body,
     * a header, wherever) — falls back to $_POST['csrf_token'] for regular
     * form submits so existing calls to verify() with no args keep working.
     */
    public static function verify(?string $token = null): bool {
        $token ??= $_POST['csrf_token'] ?? null;

        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function verifyOrRedirect(string $fallbackUrl = 'login'): void {
        if (!self::verify()) {
            $_SESSION['error'] = 'Invalid or expired security token. Please try again.';
            header('Location: ' . $fallbackUrl);
            exit;
        }
    }
}