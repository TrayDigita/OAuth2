<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Responses;

use JsonSerializable;
use TrayDigita\OAuth2\Interfaces\Parameters\RefreshTokenParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Responses\AccessTokenParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Responses\ExpiresInParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Responses\TokenTypeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Scratches\TokenInterface;

/**
 * Access token response
 *
 * If the access token request is valid and authorized,
 * the authorization server issues an access token and optional refresh
 * token as described in Section 5.1.  If the request client
 * authentication failed or is invalid, the authorization server returns
 * an error response as described in Section 5.2.
 *
 * ### Example Response
 * <code>
 * HTTP/1.1 200 OK
 * Content-Type: application/json;charset=UTF-8
 * Cache-Control: no-store
 * Pragma: no-cache
 *
 * {
 * "access_token":"2YotnFZFEjr1zCsicMWpAA",
 * "token_type":"example",
 * "expires_in":3600,
 * "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA",
 * "example_parameter":"example_value"
 * }
 * </code>
 *
 * <code>
 * HTTP/1.1 302 Found
 * Location: http://example.com/cb#access_token=2YotnFZFEjr1zCsicMWpAA
 * &state=xyz&token_type=example&expires_in=3600
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.4
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-5.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-5.1
 *
 * @template TokenType of non-empty-string
 * @template TState of string|null
 * @template TScope of string|null
 * @template TRefreshToken of string|null
 * @note
 *   <strong>
 *       The authorization server MUST NOT issue a refresh token, if the request is authorization request,
 *      -> when client sending using implicit grant
 * </strong>
 */
interface AccessTokenResponseInterface extends
    TokenInterface,
    AccessTokenParameterInterface,
    ExpiresInParameterInterface,
    RefreshTokenParameterInterface,
    TokenTypeParameterInterface,
    JsonSerializable
{
    /**
     * @inheritdoc
     * @return TokenType
     */
    public function getTokenType(): string;

    /**
     * Get additional parameters response
     * This can be useful for client side verification.
     * Maybe returning full data collection,
     * or only additional data that not defined in RFC6749,
     * this is up to implementation.
     *
     * @return array{
     *     ...<non-empty-string, mixed>
     * }
     * eg: ['example' => 'example_value', 'state' => 'client-string-state']
     */
    public function getAdditionalData() : array;

    /**
     * @return array{
     *     "access_token": string,
     *     "token_type": TokenType,
     *     "expires_in"?: int|null, // recommended depending lifetime
     *     "refresh_token"?: TRefreshToken,
     *     "scope"?: TScope, // required if scope not null
     *     "state"?: TState, // state is optional but recommended on some cases
     *     ...<non-empty-string, mixed>
     * }
     */
    public function jsonSerialize(): array;
}
