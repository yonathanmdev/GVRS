<?php

namespace App\Helpers;

class ViewHelper
{
    /**
     * Null-safe HTML-escape for view output.
     * Applies the default BEFORE escaping, so null/empty values
     * render as the default rather than an empty cell.
     *
     * @param mixed  $value   Raw value (may be null, string, int, etc.)
     * @param string $default Fallback shown when $value is null
     * @return string
     */
    public static function e($value, string $default = '-'): string
    {
        if ($value === null) {
            $value = $default;
        }

        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}