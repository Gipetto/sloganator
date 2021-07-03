<?php declare(strict_types = 1);

class Route {
    /**
     * @var callable $callback
     */
    protected $callback;
    protected string $path;
    protected string $method;

    public function __construct(string $path, string $method, callable $callback) {
        $this->callback = $callback;
        $this->method = $method;
        $this->path = $path;
    }

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
}

class Router {
    /**
     * @var Route[]
     */
    protected array $routes = [];

    protected string $base;

    protected string $method;
    protected string $path;
    protected string $request;

    /**
     * @var array<string, string>
     */
    protected array $params = [];

    public function __construct(string $base = "") {
        $request_uri = $_SERVER["REQUEST_URI"];

        if (substr($request_uri, 0, strlen($base)) == $base) {
            $request_uri = substr($request_uri, strlen($base));
        }

        /**
         * @var array<string, string> $uri
         */
        $uri = parse_url($request_uri);
        
        $this->path = $uri["path"];

        $query = $uri["query"] ??= "";
        parse_str($query, $this->params);
        
        $this->method = $_SERVER["REQUEST_METHOD"];
    }

    public function route(string $path, string $method, callable $callback): void { 
        $route = new Route($path, $method, $callback);
        $this->routes[$route->key()] = $route;
    }

    public function dispatch(): Response {
        $requestedRoute = Route::getKey($this->path, $this->method);

        if (empty($this->routes[$requestedRoute])) {
            return new NotFound;
        }

        /**
         * @var Route $route
         */
        $route = $this->routes[$requestedRoute];

        $params = $this->params;

        if ($this->method == "POST") {
            $body = (string) file_get_contents("php://input");
            $params["body"] = json_decode($body, true);
        }

        return $route->call($params);
    }
}
