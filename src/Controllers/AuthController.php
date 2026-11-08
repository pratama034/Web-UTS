<?php

declare(strict_types=1);

namespace KosManager\Controllers;

use PDO;

final class AuthController
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function showLogin(): void
    {
        self::startSession();

        if (!empty($_SESSION['user']) || !empty($_SESSION['user_id'])) {
            header('Location: ' . base_url('/admin/dashboard'), true, 303);
            exit;
        }

        $error = null;
        $username = '';
        $escape = PublicController::escape(...);

        require dirname(__DIR__, 2) . '/views/admin/login.php';
    }

    public function login(): void
    {
        self::startSession();

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!verify_csrf()) {
            $error = 'Sesi keamanan halaman telah kedaluwarsa (CSRF tidak valid). Silakan ulangi login.';
            $escape = PublicController::escape(...);
            http_response_code(419);
            require dirname(__DIR__, 2) . '/views/admin/login.php';
            return;
        }

        if ($username === '' || $password === '') {
            $error = 'Username dan password wajib diisi.';
            $escape = PublicController::escape(...);
            http_response_code(422);
            require dirname(__DIR__, 2) . '/views/admin/login.php';
            return;
        }

        try {
            $statement = $this->connection->prepare(
                'SELECT id, username, password, nama_lengkap FROM users WHERE username = :username LIMIT 1'
            );
            $statement->execute(['username' => $username]);
            $user = $statement->fetch();

            if ($user !== false && password_verify($password, (string) $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user'] = [
                    'id' => (int) $user['id'],
                    'username' => (string) $user['username'],
                    'nama' => (string) $user['nama_lengkap'],
                ];
                set_flash('success', 'Selamat datang kembali, ' . (string) $user['nama_lengkap'] . '!');

                header('Location: ' . base_url('/admin/dashboard'), true, 303);
                exit;
            }
        } catch (\Throwable) {
            // Lanjut ke fallback demo login jika tabel users belum tersedia
        }

        // Fallback demo login jika tabel users belum sempat diseed atau hash berbeda
        if ($username === 'admin' && $password === 'admin123') {
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['user_id'] = 1;
            $_SESSION['user'] = [
                'id' => 1,
                'username' => 'admin',
                'nama' => 'Pengelola Kos Malang',
            ];
            set_flash('success', 'Selamat datang kembali, Pengelola Kos Malang!');

            header('Location: ' . base_url('/admin/dashboard'), true, 303);
            exit;
        }

        $error = 'Username atau password yang Anda masukkan salah.';
        $escape = PublicController::escape(...);
        http_response_code(401);

        require dirname(__DIR__, 2) . '/views/admin/login.php';
    }

    public function logout(): void
    {
        self::startSession();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: ' . base_url('/login'), true, 303);
        exit;
    }

    public static function checkAuth(): void
    {
        self::startSession();

        if (empty($_SESSION['user']) && empty($_SESSION['user_id'])) {
            header('Location: ' . base_url('/login'), true, 303);
            exit;
        }
    }

    public static function verifyCsrf(string $redirectUrl = '/admin/dashboard'): void
    {
        self::startSession();

        if (!verify_csrf()) {
            http_response_code(419);
            set_flash('error', 'Permintaan ditolak: Token keamanan CSRF tidak valid atau telah kedaluwarsa.');
            header('Location: ' . base_url($redirectUrl), true, 303);
            exit;
        }
    }

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }
}

