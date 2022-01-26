<?php

namespace Sloganator\TrieRouter;

use Sloganator\Responses\{NotFound, Response};
use Sloganator\TrieRouter\Handler;

/**
 * Store routes in a Trie.
 * 
 * Example:
 *   - /v1/foo/zot
 *   - /v1/foo/zap
 *   - /v1/bar
 *   - /v2/baz/bop
 * 
 * Are stored as:
 *          root
 *         /    \
 *        v1    v2
 *       /  \     \
 *     foo  bar   baz
 *    /  \          \
 *  zot  zap        bop
 * 
 */
class Router {
    private RouteNode $root;
    private RouteNode $notFound;

    public function __construct() {
        $this->root = $this->getNode();
        
        $this->notFound = new RouteNode("404");
        forEach(Handler::METHODS as $method) {
            $this->notFound->handler->add($method, function(Request $request) {
                return new NotFound();
            });
        }
    }

    protected function getNode(): RouteNode {
        return new RouteNode();
    }

    public function route(string $path, string $method, \Closure $callback): void {
        $current = $this->root;        
        $routePath = new RoutePath($path);

        foreach ($routePath->parts as $part) {
            $partIndex = $current->hasPath($part);

            if ($partIndex === false) {
                $newNode = $this->getNode();
                $newNode->value = $part;
                $partIndex = $current->addPath($newNode);
            }

            $current = $current->getNode($partIndex);
        }

        $current->handler->add($method, $callback);
    }

    public function search(string $path): RouteNode {
        $routePath = new RoutePath($path);

        if ($routePath->isEmpty) {
            return $this->root;
        }

        $current = $this->root;

        foreach ($routePath->parts as $part) {
            $childIndex = $current->hasPath($part);
            if ($childIndex === false) {
                $current = $this->notFound;
                break;
            }

            $current = $current->getNode($childIndex);
        }

        return $current;
    }

    public function dispatch(Request $request): Response {
        $route = $this->search($request->path);
        return $route->handle($request);
    }
}
