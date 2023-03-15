<?php declare(strict_types = 1);

namespace Sloganator\Responses;

class TooManyRequests extends ApiResponse {
    public function __construct(int $retryTime) {
        parent::__construct(429, (object) [
            "code" => 429,
            "message" => "Hang loose. Slow and steady wins the race."
        ]);
        $this->addHeaders([
            sprintf("Retry-After: %d", $retryTime)
        ]);
    }
}
