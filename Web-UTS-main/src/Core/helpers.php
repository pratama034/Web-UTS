<?php

declare(strict_types=1);

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        static $base = null;
        if ($base === null) {
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $dir = str_replace('\\', '/', dirname($scriptName));
            if (str_ends_with($dir, '/public')) {
                $dir = substr($dir, 0, -7);
            }
            $base = ($dir === '/' || $dir === '.') ? '' : rtrim($dir, '/');
        }

        if ($path === '' || $path === '/') {
            return $base !== '' ? $base . '/' : '/';
        }

        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = htmlspecialchars(csrf_token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(?string $token = null): bool
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        $submitted = $token ?? (string) ($_POST['csrf_token'] ?? '');
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (!is_string($sessionToken) || $sessionToken === '' || $submitted === '') {
            return false;
        }

        return hash_equals($sessionToken, $submitted);
    }
}

if (!function_exists('set_flash')) {
    function set_flash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('get_flash')) {
    /**
     * @return array{type: string, message: string}|null
     */
    function get_flash(): ?array
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        return [
            'type' => (string) ($flash['type'] ?? 'info'),
            'message' => (string) ($flash['message'] ?? ''),
        ];
    }
}
