<?php declare(strict_types = 1);

namespace Sloganator\Responses;

class NotFound extends ApiResponse {
    public function __construct() {
        parent::__construct(404, (object) [
            "code" => 404,
            "message" => "Invalid Route"
        ]);
    }
}
