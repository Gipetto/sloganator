<?php

namespace Sloganator\Router;

use Sloganator\Responses\{NotFound, Response};
use Sloganator\Router\Handler;

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
        forEach(Request::METHODS as $method) {
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

            $current = $current->getPath((string) $partIndex);
        }

        $current->handler->add($method, $callback);
    }

    public function get(string $path, \Closure $callback): void {
        $this->route($path, Request::GET, $callback);
    }

    public function post(string $path, \Closure $callback): void {
        $this->route($path, Request::POST, $callback);
    }

    public function put(string $path, \Closure $callback): void {
        $this->route($path, Request::PUT, $callback);
    }

    public function delete(string $path, \Closure $callback): void {
        $this->route($path, Request::DELETE, $callback);
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

            $current = $current->getPath((string) $childIndex);
        }

        return $current;
    }

    public function dispatch(?Request $request = null): Response {
        if (!$request) {
            $request = Request::new();
        }

        $route = $this->search($request->path);
        return $route->handle($request);
    }
}
