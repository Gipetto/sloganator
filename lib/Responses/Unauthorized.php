<?php declare(strict_types = 1);

namespace Sloganator\Responses;

class Unauthorized extends ApiResponse {
    public function __construct() {
        parent::__construct(401, (object) [
            "code" => 401,
            "message" => "You must be logged in to create a slogan"
        ]);
    }
}
