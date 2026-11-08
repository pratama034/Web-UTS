<?php

declare(strict_types=1);

namespace KosManager\Config;

use PDO;
use PDOException;

function getConnection(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $preferredHost = getenv('KOSMANAGER_DB_HOST') ?: '127.0.0.1';
    $port = getenv('KOSMANAGER_DB_PORT') ?: '3306';
    $database = getenv('KOSMANAGER_DB_NAME') ?: 'kostmanager';
    $username = getenv('KOSMANAGER_DB_USER') ?: 'root';
    $password = getenv('KOSMANAGER_DB_PASSWORD') ?: '';

    // Coba hubungkan menggunakan 127.0.0.1 dan fallback ke localhost jika diperlukan
    $hosts = array_unique(array_filter([
        $preferredHost,
        '127.0.0.1',
        'localhost',
    ]));

    $lastException = null;
    foreach ($hosts as $host) {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $host,
                $port,
                $database
            );

            $connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return $connection;
        } catch (PDOException $e) {
            $lastException = $e;
        }
    }

    if ($lastException instanceof PDOException) {
        throw $lastException;
    }
}
