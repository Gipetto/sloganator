<?php

namespace Sloganator\Router;

use Sloganator\Responses\{ApiResponse, NoContent, NotFound, Response};

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

    private string $pathPrefix = "";

	/**
	 * @var string[]
	 */
	protected $allowedOrigins = [];

    public function __construct() {
        $this->root = $this->getNode();
        
        $this->notFound = new RouteNode("404");
        forEach(Request::METHODS as $method) {
            $this->notFound->handler->add($method, function(Request $request) {
                return new NotFound();
            });
        }
    }

    public function setPathPrefix(string $prefix): void {
        $this->pathPrefix = rtrim($prefix, "/");
    }

    public function getPathPrefix(): string {
        return $this->pathPrefix;
    }

	public function addAllowedOrigin(string $origin): void {
		$host = parse_url($origin, PHP_URL_HOST);
		$this->allowedOrigins[$host] = $origin;
	}

	public function getAllowedOrigin(): string {
		if (array_key_exists($_SERVER["SERVER_NAME"], $this->allowedOrigins)) {
			return $this->allowedOrigins[$_SERVER["SERVER_NAME"]];
		}
		return "";
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
		$this->route($path, Request::OPTIONS, function() {
			return new NoContent();	
		});
    }

    public function put(string $path, \Closure $callback): void {
        $this->route($path, Request::PUT, $callback);
		$this->route($path, Request::OPTIONS, function() {
			return new NoContent();	
		});
    }

    public function delete(string $path, \Closure $callback): void {
        $this->route($path, Request::DELETE, $callback);
		$this->route($path, Request::OPTIONS, function() {
			return new NoContent();			
		});
    }

    public function search(string $path): RouteNode {
        $routePath = new RoutePath(str_replace($this->pathPrefix, "", $path));

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
        $response = $route->handle($request);

		if ($response instanceof ApiResponse) {
			$response->setAllowedOrigin($this->getAllowedOrigin());
		}

		return $response;
    }
}
