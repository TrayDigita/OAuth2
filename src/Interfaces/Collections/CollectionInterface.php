<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Collections;

use ArrayAccess;
use Countable;
use JsonSerializable;
use Traversable;

/**
 * Data collection interface
 * This interface is used to define a collection of data that can be used to store and retrieve data.
 *
 * @template TKey
 * @template TValue
 * @template-extends Traversable<TKey, TValue>
 * @template-extends ArrayAccess<TKey, TValue>
 */
interface CollectionInterface extends Countable, Traversable, ArrayAccess, JsonSerializable
{
    /**
     * Set a value in the collection
     *
     * @param TKey $key
     * @param TValue $value
     * @return void
     * @throws \TrayDigita\OAuth2\Exceptions\OperationNotPermittedException
      * if the set operation is not permitted (e.g., if the collection is frozen or invalid key).
      * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function set($key, $value) : void;

    /**
     * Get a value from the collection
     * If the key does not exist, it should return null
     *
     * @param TKey $key
     * @return TValue|null
     */
    public function get($key);

    /**
     * Check if a key exists in the collection
     * If the key exists, it should return true, otherwise false
     *
     * @param TKey $key
     * @return bool
     */
    public function has($key): bool;

    /**
     * Remove a key from the collection
     *
     * @param TKey $key
     * @return void
     * @throws \TrayDigita\OAuth2\Exceptions\OperationNotPermittedException
      * if the remove operation is not permitted (e.g., if the collection is frozen or invalid key).
      * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function remove($key) : void;

    /**
     * Check if a value exists in the collection
     * If the value exists, it should return true, otherwise false
     *
     * @param TValue $value
     * @param bool $strict Whether to use strict comparison (===) or not (==)
     * @return bool
     * @uses in_array()
     */
    public function contains($value, bool $strict = true): bool;

    /**
     * Get all keys in the collection
     *
     * @return list<TKey>
     */
    public function keys(): array;

    /**
     * Get all values in the collection
     *
     * @return list<TValue>
     */
    public function values(): array;

    /**
     * Get all key-value pairs in the collection
     *
     * @return array<TKey, TValue>
     */
    public function all(): array;
}
