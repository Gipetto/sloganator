<?php declare(strict_types = 1);

namespace Sloganator\Router;

class RoutePath {
    /**
     * @var array<string> $parts
     */
    public array $parts = [];
    public bool $isEmpty = true;

    public function __construct(
        public string $path
    ) {
        $parsed = parse_url(trim($path, "/"));

        if ($parsed && !empty($parsed["path"])) {
            $this->parts = explode("/", $parsed["path"]);
            $this->isEmpty = false;
        }
    }
}
