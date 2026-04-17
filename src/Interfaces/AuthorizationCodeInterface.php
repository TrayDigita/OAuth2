<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces;

use TrayDigita\OAuth2\Interfaces\Collections\CollectionInterface;
use TrayDigita\OAuth2\Interfaces\Responses\AuthorizationResponseInterface;
use TrayDigita\OAuth2\Interfaces\Scratches\ExpirationInterface;
use TrayDigita\OAuth2\Interfaces\Scratches\TimestampInterface;

/**
 * Based on RFC6749#section-11.2.2,
 * The Initial Registry Contents
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @template TState of string|null
 * @template TScope of string|null
 * @template-extends AuthorizationResponseInterface<TState, TScope>
 */
interface AuthorizationCodeInterface extends
    AuthorizationResponseInterface,
    ExpirationInterface,
    TimestampInterface
{
    /**
     * Data collection for access token, this is for custom data that can be added to the access token response
     * This is not defined in RFC6749, but it can be used to add custom data to the access token response.
     *
     * @return CollectionInterface<non-empty-string, mixed>
     *     should return collection of custom data that can be added to the access token response
     */
    public function getData(): CollectionInterface;

    /**
     * Get additional parameters response
     * This can be useful for client side verification
     *
     * @return array{
     *      "code": non-empty-string,
     *      "timestamp": positive-int,
     *      "expires_in"?: int|null, // recommended depending lifetime
     *      "state"?: TState, // state is required if client sending state
     *      "scope"?: TScope,
     *      ...<string, mixed>
     * }
     * @see self::getData()
     */
    public function toArray(): array;

    /**
     * Implementing JsonSerializable,
     *
     * @return array{
     *     "code": non-empty-string,
     *     "timestamp": positive-int,
     *     "expires_in"?: int|null, // recommended depending lifetime
     *     "state"?: TState, // state is required if client sending state
     *     "scope"?: TScope,
     *     ...<string, mixed>
     * }
     * @see self::toArray()
     */
    public function jsonSerialize(): array;

    /**
     * Returning casting string
     *
     * @return string should return access token
     * @see self::getCode()
     */
    public function __toString(): string;
}
