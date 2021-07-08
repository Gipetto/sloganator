<?php

use Carbon\Carbon;
use Sloganator\Cache\{SerializingFileCache, SuccessfulResponseCache};
use Sloganator\Responses\ApiResponse;

class CacheTest extends SloganatorTestCase {
    protected $cacheDir = "/tmp";
    protected $cacheFileName = "test-cache";

    protected function tearDown(): void {
        $cacheFile = $this->cacheDir . DIRECTORY_SEPARATOR . $this->cacheFileName;
        if (is_file($cacheFile)) {
            unlink($cacheFile);
        }
        $this->startTime();    
    }

    public function testCache() {
        $this->stopTime();

        $response = new ApiResponse(200, (object) ["foo" => "bar"]);
        $cache = new SuccessfulResponseCache($this->cacheFileName, $this->cacheDir);

        $noCache = $cache->get();
        $this->assertEquals(false, $noCache);

        $set = $cache->set($response);
        $this->assertEquals(strlen(serialize($response)), $set);

        $cachedResponse = $cache->get();
        $response->setCacheHeaders((Carbon::now())->format("r"));
        $this->assertEquals($response, $cachedResponse);

        $cache->flush();
        $this->startTime();
    }

    public function testSuccessfulResponseCacheSetException() {
        $this->expectException(InvalidArgumentException::class);

        $cache = new SuccessfulResponseCache("test-cache", "/tmp");
        $cache->set("foo");
    }

    public function testSerializingFileCacheSetException() {
        $this->expectException(InvalidArgumentException::class);

        $cache = new SerializingFileCache($this->cacheFileName, $this->cacheDir);
        $cache->set("foo");
    }
}