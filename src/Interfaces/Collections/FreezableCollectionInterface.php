<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Collections;

/**
 * Interface FrozenInterface
 *
 * This interface defines methods for objects that can be frozen (made immutable).
 *
 * @template TKey
 * @template TValue
 * @template-extends CollectionInterface<TKey, TValue>
 */
interface FreezableCollectionInterface extends CollectionInterface
{

    /**
     * Set a value in the collection
     *
     * @param TKey $key
     * @param TValue $value
     * @return void
     * @throws \TrayDigita\OAuth2\Exceptions\OperationNotPermittedException
     * if the set operation is not permitted (e.g., if the collection is frozen).
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function set($key, $value) : void;

    /**
     * Remove a key from the collection
     *
     * @param TKey $key
     *
     * @return void
     * @throws \TrayDigita\OAuth2\Exceptions\OperationNotPermittedException
     * if the remove operation is not permitted (e.g., if the collection is frozen).
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function remove($key): void;

    /**
     * Check if the object is frozen (immutable).
     *
     * @return bool True if the object is frozen, false otherwise.
     */
    public function isFrozen(): bool;

    /**
     * Freeze the object, making it immutable.
     *
     * @return void
     */
    public function freeze(): void;
}
