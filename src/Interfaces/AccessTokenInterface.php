<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces;

use Stringable;
use TrayDigita\OAuth2\Interfaces\Collections\CollectionInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Responses\ResourceOwnerIdParameterInterface;
use TrayDigita\OAuth2\Interfaces\Responses\AccessTokenResponseInterface;
use TrayDigita\OAuth2\Interfaces\Scratches\ExpirationInterface;
use TrayDigita\OAuth2\Interfaces\Scratches\TimestampInterface;
use TrayDigita\OAuth2\Interfaces\Settable\SettableRefreshTokenInterface;

/**
 * Based on RFC6749#section-11.2.2,
 * The Initial Registry Contents
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @template TokenType of non-empty-string
 * @template TState of string|null
 * @template TScope of string|null
 * @template TRefreshToken of string|null
 * @template-extends AccessTokenResponseInterface<TokenType, TState, TScope, TRefreshToken>
 */
interface AccessTokenInterface extends
    AccessTokenResponseInterface,
    SettableRefreshTokenInterface,
    ResourceOwnerIdParameterInterface,
    ExpirationInterface,
    TimestampInterface,
    Stringable
{
    /**
     * Returning casting string
     *
     * @return string should return access token
     * @see self::getAccessToken()
     */
    public function __toString(): string;

    /**
     * Data collection for access token, this is for custom data that can be added to the access token response
     * This is not defined in RFC6749, but it can be used to add custom data to the access token response.
     *
     * @return CollectionInterface<non-empty-string, mixed>
     *     should return collection of custom data that can be added to the access token response
     */
    public function getData(): CollectionInterface;

    /**
     * Get the access token as an array, this is for custom data that can be added to the access token response
     * This is not defined in RFC6749, but it can be used to add custom data to the access token response.
     *
     * @return array{
     *     'timestamp': positive-int,
     *     "access_token": string,
     *     "token_type": TokenType,
     *     "expires_in"?: int|null, // recommended depending lifetime
     *     "refresh_token"?: TRefreshToken,
     *     "scope"?: TScope, // required if scope not null
     *     "state"?: TState, // state is optional but recommended on some cases
     *     ...<non-empty-string, mixed>
     * }
     * @see self::getData() to get the collection of custom data that can be added to the access token response,
     * @see self::getAdditionalData() to get the additional parameters that can be added to the access token response,
     */
    public function toArray(): array;

    /**
     * @return array{
     *     'timestamp': positive-int,
     *     "access_token": string,
     *     "token_type": TokenType,
     *     "expires_in"?: int|null, // recommended depending lifetime
     *     "refresh_token"?: TRefreshToken,
     *     "scope"?: TScope, // required if scope not null
     *     "state"?: TState, // state is optional but recommended on some cases
     *     ...<non-empty-string, mixed>
     * }
     * @uses self::toArray() to get the array representation of the access token,
     *      then it can be used to serialize the access token to JSON
     */
    public function jsonSerialize(): array;
}
