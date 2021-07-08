<?php declare(strict_types = 1);

namespace Sloganator\Router;

use Sloganator\Responses\{ApiResponse, InternalServiceError, NotFound, Response};

class Router {
    /**
     * @var Route[]
     */
    protected array $routes = [];

    protected string $method;
    protected string $path;
    protected string $request;

    /**
     * @var array<string, string>
     */
    protected array $params = [];

    protected string $inputStream = "php://input";

    public function __construct(protected string $urlBase = "") {}

    public function parseRequest(): void {
        $this->method = $_SERVER["REQUEST_METHOD"];
        $request_uri = substr($_SERVER["REQUEST_URI"], strlen($this->urlBase));
        $this->path = (string) parse_url($request_uri,  PHP_URL_PATH);
    }

    /**
     * @return array<string, mixed>
     */
    public function parseParams(): array {
        $query = parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY) ?: "";
        parse_str($query, $params);

        if ($this->method == "POST") {
            $body = (string) file_get_contents($this->inputStream);
            $params["body"] = json_decode($body, true);
        }

        return $params;
    }

    public function route(string $path, string $method, \Closure $callback): void { 
        $route = new Route($path, $method, $callback);
        $this->routes[$route->key()] = $route;
    }

    public function dispatch(): Response {
        $this->parseRequest();
        
        $requestedRoute = Route::getKey($this->path, $this->method);

        if (empty($this->routes[$requestedRoute])) {
            return new NotFound;
        }

        /**
         * @var Route $route
         */
        $route = $this->routes[$requestedRoute];

        try {
            $params = $this->parseParams();
            $response = $route->call($params);
        } catch (\Throwable $e) {
            $response = new InternalServiceError;
            error_log("Fatal error on: " . $route->toString());
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }

        return $response;
    }

    public function setInputStream(string $inputStream): void {
        $this->inputStream = $inputStream;
    }
}
