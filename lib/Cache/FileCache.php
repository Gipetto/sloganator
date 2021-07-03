<?php declare(strict_types = 1);

namespace Sloganator\Cache;

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
