<?php
namespace App\Helpers;

class Csrf {
    
    /**
     * Get or generate the CSRF token.
     */
    public static function token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Generate the HTML hidden input tag.
     */
    public static function field(): string {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Validate the submitted token.
     */
    public static function verify(): bool {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            return false;
        }
        return true;
    }

    /**
     * Validate and automatically handle failure (redirects & logs out error).
     */
    public static function verifyOrRedirect(string $fallbackUrl = 'login'): void {
        if (!self::verify()) {
            $_SESSION['error'] = 'Invalid or expired security token. Please try again.';
            header('Location: ' . $fallbackUrl);
            exit;
        }
    }
}