<?php declare(strict_types = 1);

class FileCache {
    private string $cacheDir = BASEDIR . "/cache";
    private string $cacheFileName;

    public function __construct(string $fileName) {
        $this->cacheFileName = $fileName;
    }

    public function cacheFilePath(): string {
        return $this->cacheDir . "/" . $this->cacheFileName;
    }

    /**
     * @return string|false
     */
    public function get() {
        $cacheFilePath = $this->cacheFilePath();
        
        if (!file_exists($cacheFilePath)) {
            return false;
        }

        return file_get_contents($cacheFilePath);
    }

    /**
     * @param string $value
     */
    public function set($value): int {
        return (int) file_put_contents($this->cacheFilePath(), $value);
    }

    public function flush(): void {
        $cacheFilePath = $this->cacheFilePath();

        if (file_exists($cacheFilePath)) {
            unlink($cacheFilePath);
        }
    }
}

class SerializingFileCache extends FileCache {

    /**
     * @return object|false
     */
    public function get() {
        $cached = parent::get();

        if (!$cached) {
            return false;
        }

        return unserialize($cached);
    }

    /**
     * @param object $object
     */
    public function set($object): int {
        if (!is_object($object)) {
            throw new \InvalidArgumentException;
        }

        return parent::set(serialize($object));
    }
}

class SuccessfulResponseCache extends SerializingFileCache {
    /**
     * @param ApiResponse $response
     */
    public function set($response): int {
        if (!($response instanceof \ApiResponse)) {
            throw new \InvalidArgumentException;
        }

        $r = clone $response;
        $r->setCode(200);
        return parent::set($r);
    }

    /**
     * @return \ApiResponse
     */
    public function get() {
        /**
         * @var \ApiResponse $response
         */
        $response = parent::get();

        if ($response instanceof \ApiResponse) {
            $mtime = (int) filemtime($this->cacheFilePath());
            $response->addHeaders([
                "X-Cache: HIT",
                "Last-Modified: " . date("r", $mtime)
            ]);
        }

        return $response;
    }
}
