<?php

declare(strict_types=1);

use KosManager\Core\Router;
use KosManager\Controllers\AuthController;
use KosManager\Controllers\DashboardController;
use KosManager\Repositories\KamarRepository;
use KosManager\Repositories\PenghuniRepository;

spl_autoload_register(static function (string $class): void {
    $namespacePrefix = 'KosManager\\';

    if (!str_starts_with($class, $namespacePrefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($namespacePrefix));
    $file = dirname(__DIR__) . '/src/' . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require_once dirname(__DIR__) . '/src/Core/helpers.php';
require_once dirname(__DIR__) . '/src/config/koneksi.php';

AuthController::startSession();

try {
    $connection = \KosManager\Config\getConnection();
    $kamarRepository = new KamarRepository($connection);
    $penghuniRepository = new PenghuniRepository($connection);

    $dashboardController = new DashboardController($kamarRepository, $penghuniRepository);
    $authController = new AuthController($connection);

    $router = new Router();
    $router
        ->get('/', [$dashboardController, 'index'])
        ->get('/login', [$authController, 'showLogin'])
        ->post('/login', [$authController, 'login'])
        ->get('/logout', [$authController, 'logout'])
        ->get('/admin/dashboard', [$dashboardController, 'index']);

    $router->dispatch();
} catch (Throwable $exception) {
    error_log($exception->__toString());
    $isDbError = $exception instanceof PDOException;
    http_response_code($isDbError ? 503 : 500);
    header('Content-Type: text/html; charset=utf-8');
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isDbError ? '503 - Database Tidak Tersedia' : '500 - Kesalahan Sistem' ?> | KosManager</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px; }
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 32px; max-width: 520px; width: 100%; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); text-align: center; }
        .badge { display: inline-block; background-color: #fee2e2; color: #dc2626; font-size: 13px; font-weight: 600; padding: 4px 12px; border-radius: 9999px; margin-bottom: 16px; }
        h1 { font-size: 20px; margin-bottom: 12px; color: #0f172a; }
        p { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 20px; }
        .hint { background-color: #f1f5f9; border-radius: 8px; padding: 12px; font-size: 13px; color: #334155; text-align: left; }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">Error <?= $isDbError ? '503 (Database)' : '500 (Sistem)' ?></div>
        <h1><?= $isDbError ? 'Layanan Database Sedang Tidak Tersedia' : 'Terjadi Kendala Sistem' ?></h1>
        <p><?= htmlspecialchars($exception->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
        <div class="hint">
            <strong>Catatan:</strong><br>
            <?= $isDbError
                ? 'Pastikan MySQL aktif di XAMPP Control Panel dan database <code>kostmanager</code> sudah diisi tabel di phpMyAdmin.'
                : 'File: <code>' . htmlspecialchars(basename($exception->getFile()), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code> baris ' . $exception->getLine() ?>
        </div>
    </div>
</body>
</html>
<?php
}
