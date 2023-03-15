<?php declare(strict_types = 1);

namespace Sloganator\Responses;

class ValidationError extends ApiResponse {
    public function __construct(string $message) {
        parent::__construct(400, (object) [
            "code" => 400,
            "message" => $message
        ]);
    }
}
