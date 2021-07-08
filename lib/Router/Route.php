<?php declare(strict_types = 1);

namespace Sloganator\Router;

use Sloganator\Responses\Response;

class Route {
    public function __construct(protected string $path, protected string $method, protected \Closure $callback) {}

    public function key(): string {
        return static::getKey($this->path, $this->method);
    }

    public static function getKey(string $path, string $method): string {
        $path = trim($path, "/");
        $method = strtolower($method);
        return $method . "-" . ($path ?: "/"); 
    }

    /**
     * @param array<string, int|string> $params
     */
    public function call(array $params): Response {
        return call_user_func($this->callback, $params);
    }

    public function toString(): string {
        return $this->method . " :: " . $this->path;
    }
}