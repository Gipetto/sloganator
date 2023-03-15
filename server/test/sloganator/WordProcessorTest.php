<?php 

use Sloganator\Database;
use Sloganator\Processors\WordCounter;

class WordProcessorTest extends SloganatorTestCase {

    public function testWordProcessor() {
        $generator = function() {
            $data = [
                "This is a Slogan",
                "This is another Slogan",
                "It's a mad mad mad mad mad mad world!"
            ];

            foreach ($data as $datum) {
                yield $datum;
            }
        };

        $wp = new WordCounter($generator);
        $this->assertEqualsCanonicalizing([
            (object) ["x" => "mad", "value" => 6],
            (object) ["x" => "slogan", "value" => 2],
            (object) ["x" => "world", "value" => 1],
            (object) ["x" => "another", "value" => 1]
        ], $wp->run());
    }

    public function testWordProcessorExceptionHandling() {
        $generator = function() {
            throw new Exception("ha ha!");
        };

        $wp = new WordCounter($generator);
        $this->assertEqualsCanonicalizing([], $wp->run());
    }
}