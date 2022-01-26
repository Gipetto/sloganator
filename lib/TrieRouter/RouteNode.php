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

    /**
     * Add the path as a child node, return the index of the new node
     */
    public function addPath(RouteNode $node): int {
        $cnt = array_push($this->children, $node);
        return $cnt - 1;
    }

    public function getNode(int $index): RouteNode {
        return $this->children[$index];
    }

    public function handle(Request $request): Response {
        return $this->handler->handle($request);
    }
}
