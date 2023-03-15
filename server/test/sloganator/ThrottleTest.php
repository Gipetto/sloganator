<?php

use \Sloganator\Throttle;
use \Sloganator\Database;

class DummyThrottleUser extends \Sloganator\User {
    public function __construct() {
        $this->userId = 1;
        $this->userName = "DummyUser";
    }
}

class ThrottleTest extends SloganatorTestCase {

    public function testThrottle() {
        $this->stopTime(12, 0, 0, "America/Los_Angeles");

        $db = new Database(":memory:");
        $throttle = new Throttle($db);
        
        $user = new DummyThrottleUser;

        // No slogans by user
        $this->assertEquals(0, $throttle->get($user));

        // Recent slogan by user
        $throttle->update($user);
        $this->stopTime(12, 0, 3);
        $this->assertEquals((Throttle::THROTTLE - 3), $throttle->get($user));

        $this->startTime();
    }

    public function testThrottleError() {
        $db = new Database(":memory:");
        $throttle = new Throttle($db);

        $db->query("DROP TABLE throttles;");
        
        $user = new DummyThrottleUser; 
        
        $throttle->update($user);
        $this->assertEquals(0, $throttle->get($user));
    }
}
