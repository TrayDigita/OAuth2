<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Collections;

use TrayDigita\OAuth2\Exceptions\OperationNotPermittedException;
use TrayDigita\OAuth2\Interfaces\Collections\FreezableCollectionInterface;

/**
 * Freezable Collections
 * This class extends the Collections class and implements the FreezableCollectionInterface.
 * It allows the collection to be frozen, making it immutable.
 *
 * @template TKey of array-key
 * @template TValue
 * @template-extends Collections<TKey, TValue>
 * @template-implements FreezableCollectionInterface<TKey, TValue>
 */
class FreezableCollections extends Collections implements FreezableCollectionInterface
{
    /**
     * @var bool $frozen
     * This property is used to indicate whether the collection is frozen or not.
     */
    protected bool $frozen = false;

    /**
     * @inheritdoc
     */
    public function freeze(): void
    {
        $this->frozen = true;
    }

    public function isFrozen(): bool
    {
        return $this->frozen;
    }

    /**
     * @param TKey $key
     * @param TValue $value
     * @return void
     */
    public function set($key, $value) : void
    {
        if ($this->isFrozen()) {
            throw new OperationNotPermittedException('Cannot set item in a frozen collection.');
        }
        parent::set($key, $value);
    }

    /**
     * @inheritdoc
     * @return void
     * @throws OperationNotPermittedException if the collection is frozen.
     */
    public function remove($key): void
    {
        if ($this->isFrozen()) {
            throw new OperationNotPermittedException('Cannot remove item from a frozen collection.');
        }
        parent::remove($key);
    }

    /**
     * @return void
     */
    public function __clone(): void
    {
        $this->frozen = false;
    }
}
