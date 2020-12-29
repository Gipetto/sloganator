<?php declare(strict_types = 1);

class Router {
    protected array $routes = [];

    protected string $base;

    protected string $method;
    protected string $path;
    protected string $request;

    public function __construct(string $base = "") {
        $request_uri = $_SERVER["REQUEST_URI"];

        if (substr($request_uri, 0, strlen($base)) == $base) {
            $request_uri = substr($request_uri, strlen($base));
        }

        $uri = parse_url($request_uri);
        
        $this->path = $uri["path"];

        $query = $uri["query"] ??= "";
        parse_str($query, $this->params);
        
        $this->method = $_SERVER["REQUEST_METHOD"];
    }

    public function key(string $method, string $path): string {
        $path = trim($path, "/");
        $method = strtolower($method);
        return $method . "-" . ($path ?: "/");
    }

    public function route($path, string $method, callable $callback): void { 
        $route = $this->key($method, $path);
        $this->routes[$route] = $callback;
    }

    public function dispatch(): Response {
        $route = $this->key($this->method, $this->path);
        $callback = $this->routes[$route];

        if (!$callback) {
            return new NotFound;
        }

        $params = $this->params;

        if ($this->method == "POST") {
            $body = file_get_contents("php://input");
            $params["body"] = json_decode($body, true);
        }

        return call_user_func_array($callback, [$params]);
    }
}

