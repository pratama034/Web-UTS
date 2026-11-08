<?php

declare(strict_types=1);

namespace KosManager\Core;

use InvalidArgumentException;
use LogicException;

final class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, callable $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function dispatch(?string $method = null, ?string $uri = null): void
    {
        $method ??= $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri ??= $_SERVER['REQUEST_URI'] ?? '/';
        $method = strtoupper($method);
        $path = parse_url($uri, PHP_URL_PATH);

        if (!is_string($path)) {
            $this->respondNotFound();
            return;
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = str_replace('\\', '/', dirname($scriptName));
        if (str_ends_with($baseDir, '/public')) {
            $baseDir = substr($baseDir, 0, -7);
        }
        if ($baseDir !== '/' && $baseDir !== '.' && $baseDir !== '' && str_starts_with($path, $baseDir)) {
            $path = substr($path, strlen($baseDir));
        }

        $path = $this->normalizePath($path);
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler !== null) {
            $handler();
            return;
        }

        foreach ($this->routes as $registeredMethod => $routes) {
            if (isset($routes[$path])) {
                header('Allow: ' . implode(', ', $this->allowedMethods($path)));
                http_response_code(405);
                echo '405 Method Not Allowed';
                return;
            }
        }

        $this->respondNotFound();
    }

    private function addRoute(string $method, string $path, callable $handler): self
    {
        if (!str_starts_with($path, '/')) {
            throw new InvalidArgumentException('Route paths must start with "/".');
        }

        if (str_contains($path, '?') || str_contains($path, '#')) {
            throw new InvalidArgumentException('Route paths must not include a query string or fragment.');
        }

        $path = $this->normalizePath($path);

        if (isset($this->routes[$method][$path])) {
            throw new LogicException(sprintf('Route %s %s is already registered.', $method, $path));
        }

        $this->routes[$method][$path] = $handler;

        return $this;
    }

    /**
     * @return list<string>
     */
    private function allowedMethods(string $path): array
    {
        $methods = [];

        foreach ($this->routes as $method => $routes) {
            if (isset($routes[$path])) {
                $methods[] = $method;
            }
        }

        return $methods;
    }

    private function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        return $path === '/' ? '/' : rtrim($path, '/');
    }

    private function respondNotFound(): void
    {
        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        echo '404 Not Found';
    }
}
