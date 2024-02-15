<?php declare(strict_types = 1);

namespace Sloganator\Responses;

class InternalServiceError extends ApiResponse {
    public function __construct() {
        parent::__construct(500, (object) [
            "code" => 500,
            "message" => "Internal Service Error"
        ]);
    }
}