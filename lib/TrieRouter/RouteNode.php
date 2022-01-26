<?php declare(strict_types = 1);

namespace Sloganator\TrieRouter;

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

    public function hasPath(string $path): int|bool {
        foreach($this->children as $index => $child) {
            if ($child->value == $path) {
                return $index;
            }
        }
        return false;
    }

    public function handle(Request $request): Response {
        return $this->handler->handle($request);
    }
}
