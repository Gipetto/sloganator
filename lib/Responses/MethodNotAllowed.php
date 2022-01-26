<?php declare(strict_types = 1);

namespace Sloganator\Responses;


class MethodNotAllowed extends ApiResponse {
    public function __construct() {
        parent::__construct(405, (object) [
            "code" => 405,
            "message" => "Method Not Allowed"
        ]);
    }
}