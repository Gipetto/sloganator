<?php declare(strict_types=1);

namespace Sloganator\TrieRouter;

use Sloganator\Responses\{InternalServiceError, MethodNotAllowed, NotFound, Response};


class Handler {
    /**
     * @var \Closure[]
     */
    private $handlers = [];

    /**
     * @var string[]
     */
    const METHODS = [
        "GET",
        "POST",
        "PUT",
        "DELETE"
    ];

    public function add(string $method, \Closure $handler): void {
        $requestMethod = strtoupper($method);

        if (!in_array($requestMethod, static::METHODS)) {
            throw new InvalidMethodException;
        }

        $this->handlers[$requestMethod] = $handler;
    }

    public function handle(Request $request): Response {
        if (empty($this->handlers)) {
            return new NotFound;
        }

        if (empty($this->handlers[$request->method])) {
            return new MethodNotAllowed;
        }

        try {
            return ($this->handlers[$request->method])($request);
        } catch (\Exception $e) {
            return new InternalServiceError;
        }
    }
}
