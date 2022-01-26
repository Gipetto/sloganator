<?php declare(strict_types=1);

namespace Sloganator\Responses;

use stdClass;

class NoContent extends ApiResponse {
    public function __construct() {
        parent::__construct(204, new stdClass);
    }

    public function getContent(): string {
        return "";
    }
}