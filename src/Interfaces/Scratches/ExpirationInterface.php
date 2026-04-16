<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Scratches;

/**
 * Expiration Interface
 * This interface defines the contract for handling expiration of data in an OAuth2 server implementation.
 * It provides methods to check if the data is expired and to get the expiration timestamp.
 */
interface ExpirationInterface
{
    /**
     * Indicates whether the data that issued is expired or not,
     * this is for checking if the data is expired or not,
     * and also for calculating the remaining time until it expires.
     *
     * @return bool
     * @see self::getExpires()
     */
    public function isExpired() : bool;

    /**
     * Get the timestamp when the data that issued will expire,
     * this is for checking if the data is expired or not,
     * and also for calculating the remaining time until it expires.
     *
     * @return int|null should return the timestamp when the access token will expire,
     * or null if it will never expire
     */
    public function getExpires(): ?int;
}
