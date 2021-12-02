<?php declare(strict_types = 1);

namespace Sloganator\Service;

/**
 * @template TValue
 * @template-extends \ArrayObject<int, Author>
 */
class AuthorList extends \ArrayObject implements \JsonSerializable {
    public function offsetSet(mixed $key, mixed $value): void {
        if (!($value instanceof Author)) {
            throw new \TypeError("Value must be of type Author");
        }

        parent::offsetSet($key, $value);
    }

    /**
     * Discard the numeric Ids in favor of a simple list when JSON serializing
     * Slightly magical, and nobody likes surprises :(
     * @return Author[]
     */
    public function jsonSerialize(): array {
        return array_values($this->getArrayCopy());
    }
}