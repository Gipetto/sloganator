<?php

use \Sloganator\Database;
use \Sloganator\Service\{Author, ListMeta, Sloganator, Slogan, SloganError, SloganList};

class DummyUser extends \Sloganator\User {
    public function __construct($userId = 1, $userName = "TestUser") {
        $this->userId = $userId;
        $this->userName = $userName;
    }
}

class SloganatorTest extends SloganatorTestCase {
    public function getDummyUsers() {
        return [
            new DummyUser(1, "User1"),
            new DummyUser(2, "User2"),
            new DummyUser(3, "User3")
        ];
    }

    public function getSloganator() {
        $db = new Database(":memory:");
        return new Sloganator($db);
    }

    public function simplifySloganListForComparison(SloganList $sloganList) {
        return array_map(fn($slogan) => [
                $slogan->userid,
                $slogan->username,
                $slogan->slogan
            ], $sloganList->slogans);
    }

    public function testSloganator() {
        $user = new DummyUser;
        
        $sloganator = $this->getSloganator();

        # Add and fetch new slogan
        $testTime = $this->stopTime();

        $newSloganId = $sloganator->add($user, "This is a slogan");
        $newSlogan = $sloganator->get($newSloganId);

        $this->startTime();

        $this->assertEquals(1, $newSlogan->userid);
        $this->assertEquals("TestUser", $newSlogan->username);
        $this->assertEquals("This is a slogan", $newSlogan->slogan);
        $this->assertEquals($testTime->timestamp, $newSlogan->timestamp);


        # List slogans
        $newSloganId = $sloganator->add($user, "This is another slogan");
        $newSloganId = $sloganator->add($user, "I'm a slogan too!");
        $sloganList = $sloganator->list([]);

        $this->assertInstanceOf("\Sloganator\Service\SloganList", $sloganList);
        $this->assertEquals(3, count($sloganList->slogans));

        $this->assertEquals([
            [1, "TestUser", "I'm a slogan too!"],
            [1, "TestUser", "This is another slogan"],
            [1, "TestUser", "This is a slogan"]
        ], $this->simplifySloganListForComparison($sloganList));

        $this->assertEquals(new ListMeta(
            page: 1,
            pageSize: 100,
            results: 3,
            previousPage: null,
            nextPage: null,
            filter: []
        ), $sloganList->meta);

        // Get random
        $slogan = $sloganator->getRandom();
        $this->assertInstanceOf("Sloganator\Service\Slogan", $slogan);
    }

    public function testAuthors() {
        list($user1, $user2, $user3) = $this->getDummyUsers();

        $sloganator = $this->getSloganator();
        $sloganator->add($user1, "User1 slogan");
        
        $authors = $sloganator->authors();
        $this->assertEquals([
            1 => new Author(1, ["User1"])
        ], $authors->getArrayCopy());

        // User 1 changed username
        $user1Alt = new DummyUser(1, "User1Alt");
        $sloganator->add($user1Alt, "User1 slogan, again");

        // More users
        $sloganator->add($user2, "That user 1, so unimaginative");
        $sloganator->add($user3, "I like turtles!");

        $moreAuthors = $sloganator->authors();
        $this->assertEquals([
            1 => new Author(1, ["User1", "User1Alt"]),
            2 => new Author(2, ["User2"]),
            3 => new Author(3, ["User3"])
        ], $moreAuthors->getArrayCopy());
    }

    public function sloganatorListFilterProvider() {
        return [
            [
                "page" => 1,
                "pageSize" => 2,
                "expectedMeta" => [
                    "page" => 1,
                    "pageSize" => 2,
                    "results" => 2,
                    "previousPage" => null,
                    "nextPage" => 2,
                    "filter" => []
                ]
            ],
            [
                "page" => 2,
                "pageSize" => 2,
                "expectedMeta" => [
                    "page" => 2,
                    "pageSize" => 2,
                    "results" => 2,
                    "previousPage" => 1,
                    "nextPage" => null,
                    "filter" => []
                ]
            ],
            [
                "page" => 2,
                "pageSize" => 3,
                "expectedMeta" => [
                    "page" => 2,
                    "pageSize" => 3,
                    "results" => 1,
                    "previousPage" => 1,
                    "nextPage" => null,
                    "filter" => []
                ]
            ],
            [
                "page" => 1,
                "pageSize" => 4,
                "expectedMeta" => [
                    "page" => 1,
                    "pageSize" => 4,
                    "results" => 4,
                    "previousPage" => null,
                    "nextPage" => null,
                    "filter" => []
                ]
            ],
            [
                "page" => 2,
                "pageSize" => 4,
                "expectedMeta" => [
                    "page" => 2,
                    "pageSize" => 4,
                    "results" => 0,
                    "previousPage" => 1,
                    "nextPage" => null,
                    "filter" => []
                ]
            ]
        ];
    }

    /**
     * @dataProvider sloganatorListFilterProvider
     */
    public function testSloganatorListFilter($page, $pageSize, $expectedMeta) {
        list($user1, $user2, $user3) = $this->getDummyUsers();

        $sloganator = $this->getSloganator();
        $sloganator->add($user1, "User1 slogan");
        $sloganator->add($user1, "User1 slogan, again");
        $sloganator->add($user2, "That user 1, so unimaginative");
        $sloganator->add($user3, "I like turtles!");

        $sloganList = $sloganator->list([
            "page" => $page,
            "pageSize" => $pageSize
        ]);

        $this->assertEquals($expectedMeta["results"], count($sloganList->slogans));
        $this->assertEquals(new ListMeta(...$expectedMeta), $sloganList->meta);
    }

    public function sloganatorAuthorFilterProvider() {
        return [
            [
                "author" => 1,
                "expectedMeta" => [
                    "page" => 1,
                    "pageSize" => 100,
                    "results" => 2,
                    "previousPage" => null,
                    "nextPage" => null,
                    "filter" => [
                        "author" => 1
                    ]
                ],
                "expectedSlogans" => [
                    [1, "User1", "User1 slogan, again"],
                    [1, "User1", "User1 slogan"]
                ]
            ],
            [
                "author" => 3,
                "expectedMeta" => [
                    "page" => 1,
                    "pageSize" => 100,
                    "results" => 1,
                    "previousPage" => null,
                    "nextPage" => null,
                    "filter" => [
                        "author" => 3
                    ]
                ],
                "expectedSlogans" => [
                    [3, "User3", "I like turtles!"]
                ]
            ]
        ];
    }

    /**
     * @dataProvider sloganatorAuthorFilterProvider
     */
    public function testSloganatorAuthorFilter($author, $expectedMeta, $expectedSlogans) {
        list($user1, $user2, $user3) = $this->getDummyUsers();

        $sloganator = $this->getSloganator();
        $sloganator->add($user1, "User1 slogan");
        $sloganator->add($user1, "User1 slogan, again");
        $sloganator->add($user2, "That user 1, so unimaginative");
        $sloganator->add($user3, "I like turtles!");

        $sloganList = $sloganator->list([
            "author" => $author
        ]);

        $this->assertEquals($expectedMeta["results"], count($sloganList->slogans));
        $this->assertEquals(new ListMeta(...$expectedMeta), $sloganList->meta);
        $this->assertEquals($expectedSlogans, $this->simplifySloganListForComparison($sloganList));
    }

    public function sloganatorErrorProvider() {
        return [
            ["add", [new DummyUser, "ssssslogan"]],
            ["list",[[]]],
            ["authors", []],
            ["get",[1]],
            ["getRandom", []]
        ];
    }

    /**
     * @dataProvider sloganatorErrorProvider
     */
    public function testSloganatorError($func, $params) {
        $user = new DummyUser();

        $db = new Database(":memory:");
        $db->query("DROP TABLE slogans;");
        $sloganator = new Sloganator($db);

        $e = call_user_func_array([$sloganator, $func], $params);
        $this->assertInstanceOf("Sloganator\Service\SloganError", $e);
    }
}