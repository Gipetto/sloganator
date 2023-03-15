<?php declare(strict_types = 1);

namespace Sloganator\Router;

use Sloganator\Responses\Response;

class RouteNode {
    public Handler $handler;

    /**
     * @param array<RouteNode> $children
     */
    public function __construct(
        public string $value = "",
        public array $children = []
    ) {
        $this->handler = new Handler();
    }

    public function hasPath(string $path): string|bool {
        if (isset($this->children[$path])) {
            return $path;
        }
        return false;
    }

    /**
     * Add the path as a child node, return the index of the new node
     */
    public function addPath(RouteNode $node): string {
        $this->children[$node->value] = $node;
        return $node->value;
    }

    public function getPath(string $index): RouteNode {
        return $this->children[$index];
    }

    public function handle(Request $request): Response {
        return $this->handler->handle($request);
    }
}
