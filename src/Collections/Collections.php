<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Collections;

use ArrayIterator;
use IteratorAggregate;
use Traversable;
use TrayDigita\OAuth2\Exceptions\OperationNotPermittedException;
use TrayDigita\OAuth2\Interfaces\Collections\CollectionInterface;
use function array_key_exists;
use function array_keys;
use function array_values;
use function count;
use function in_array;
use function is_array;
use function is_numeric;
use function is_string;
use function iterator_to_array;

/**
 * Data collection
 * This class is used to store and retrieve data in a collection.
 *
 * @template TKey of array-key
 * @template TValue
 * @template-implements CollectionInterface<TKey, TValue>
 * @template-implements IteratorAggregate<TKey, TValue>
 */
class Collections implements IteratorAggregate, CollectionInterface
{
    /**
     * @var array<TKey, TValue> $data
     * This property is used to store the data in the collection.
     */
    protected array $data = [];

    /**
     * Collections constructor.
     *
     * @param iterable<TKey, TValue> $data
     */
    public function __construct(iterable $data = [])
    {
        if (!is_array($data)) {
            $this->data = iterator_to_array($data);
        } else {
            $this->data = $data;
        }
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value) : void
    {
        $this->data[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function has($key): bool
    {
        // skip non-string and non-numeric keys to prevent warnings from array_key_exists
        if (!is_string($key) && !is_numeric($key)) {
            return false;
        }
        return array_key_exists($key, $this->data);
    }

    /**
     * @inheritdoc
     * @return void
     */
    public function remove($key) : void
    {
        if (!is_string($key) || !is_numeric($key)) {
            throw new OperationNotPermittedException('Key must be a string or numeric.');
        }
        unset($this->data[$key]);
    }

    /**
     * @inheritdoc
     */
    public function contains($value, bool $strict = true): bool
    {
        return in_array($value, $this->data, $strict);
    }

    /**
     * @inheritdoc
     */
    public function keys(): array
    {
        return array_keys($this->data);
    }

    /**
     * @inheritdoc
     */
    public function values(): array
    {
        return array_values($this->all());
    }

    /**
     * @inheritdoc
     * @return array<TKey, TValue>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @inheritdoc
     *
     * @return Traversable<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        /**
         * @var Traversable<TKey, TValue> $traversable
         */
        $traversable = new ArrayIterator($this->data);
        return $traversable;
    }

    /**
     * @return array<TKey, TValue>
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_string($offset) || is_numeric($offset)) {
            /**
             * @var TKey $offset
             */
            $this->set($offset, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }
}
