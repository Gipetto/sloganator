<?php declare(strict_types = 1);

namespace Sloganator\Service;

class SloganError extends SloganResult {
    public int $code;
    public string $message;

    public function __construct(int $code, string $message) {
        $this->code = $code;
        $this->message = $message;
    }
}