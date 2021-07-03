<?php declare(strict_types = 1);

namespace Sloganator\Cache;

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
