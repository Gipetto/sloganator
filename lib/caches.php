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

	public function get() {
		$cacheFilePath = $this->cacheFilePath();
		
		if (!file_exists($cacheFilePath)) {
			return false;
		}

		return file_get_contents($cacheFilePath);
	}

	public function set(string $value) {
		return file_put_contents($this->cacheFilePath(), $value);
	}

    public function flush() {
        $cacheFilePath = $this->cacheFilePath();

        if (file_exists($cacheFilePath)) {
            unlink($cacheFilePath);
        }
    }
}

class SerializingFileCache extends FileCache {
	public function get() {
		$cached = parent::get();

		if (!$cached) {
			return false;
		}

		return unserialize($cached);
	}

	public function set($object) {
		return parent::set(serialize($object));
	}
}

class SuccessfulResponseCache extends SerializingFileCache {
	public function set($response) {
		$r = clone $response;
		$r->setCode(200);
		return parent::set($r);
	}

	public function get() {
		$response = parent::get();

		if ($response instanceof \Response) {
			$mtime = filemtime($this->cacheFilePath());
			$response->addHeaders([
				"X-Cache: HIT",
				"Last-Modified: " . date("r", $mtime)
			]);
		}

		return $response;
	}
}

