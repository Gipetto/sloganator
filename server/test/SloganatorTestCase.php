<?php

use Carbon\Carbon;
use \PHPUnit\Framework\TestCase;

abstract class SloganatorTestCase extends TestCase {
    public function stopTime($hour = 12, $min = 0, $sec = 0) {
        $testTime = Carbon::createFromTime($hour, $min, $sec, "America/Los_Angeles");
        Carbon::setTestNow($testTime);
        return $testTime;
    }

    public function startTime() {
        Carbon::setTestNow();
    }
}