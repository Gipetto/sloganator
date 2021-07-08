<?php declare(strict_types = 1);

namespace Sloganator\Cache;

class FileCache {
    private string $cacheDir = BASEDIR . "/cache";
    private string $cacheFileName;

    public function __construct(string $fileName, string $cacheDir = null) {
        $this->cacheFileName = $fileName;
        if ($cacheDir && is_dir($cacheDir) && is_writable($cacheDir)) {
            $this->cacheDir = $cacheDir;
        }
    }

    public function cacheFilePath(): string {
        return $this->cacheDir . DIRECTORY_SEPARATOR . $this->cacheFileName;
    }

    /**
     * @return string|false
     */
    public function get(): mixed {
        $cacheFilePath = $this->cacheFilePath();
        
        if (!file_exists($cacheFilePath)) {
            return false;
        }

        return file_get_contents($cacheFilePath);
    }

    /**
     * @param mixed $value
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
