<?php declare(strict_types = 1);

namespace Sloganator\Service;

class SloganError extends SloganResult {
    public function __construct(public int $code, public string $message) {}
}