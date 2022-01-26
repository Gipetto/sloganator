<?php declare(strict_types=1);

namespace Sloganator\TrieRouter;

use Sloganator\Responses\{InternalServiceError, MethodNotAllowed, NotFound, Response};


class Handler {
    /**
     * @var \Closure[]
     */
    private $handlers = [];

    public function add(string $method, \Closure $handler): void {
        if (!in_array($method, Request::METHODS)) {
            throw new InvalidMethodException;
        }

        $this->handlers[$method] = $handler;
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
