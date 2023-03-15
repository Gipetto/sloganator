<?php

use \PHPUnit\Framework\TestCase;
use \Sloganator\Service\{Author, AuthorList};

class AuthorListTest extends TestCase {
    public function testAuthorList() {
        $authors = new AuthorList();
        $authors[1] = new Author(1, ["Author1"]);
        $authors[2] = new Author(2, ["Author2"]);
        $authors[1]->addUsername("Author1 Alt");

        $this->assertEquals(json_encode([
            (object) [
                "userid" => 1, 
                "usernames" => [
                    "Author1", 
                    "Author1 Alt"
                ]
            ],
            (object) [
                "userid" => 2, 
                "usernames" => [
                    "Author2"
                ]
            ]
        ]), json_encode($authors));
    }

    public function testAuthorListException() {
        $this->expectException(TypeError::class);

        $authors = new AuthorList();
        $authors[1] = "foo";    
    }
}