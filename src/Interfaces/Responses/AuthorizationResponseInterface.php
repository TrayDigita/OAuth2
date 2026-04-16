<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Responses;

use JsonSerializable;
use TrayDigita\OAuth2\Interfaces\Parameters\CodeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Responses\ExpiresInParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Responses\TokenTypeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\ScopeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\StateParameterInterface;

/**
 * If the resource owner grants the access request, the authorization
 * server issues an authorization code and delivers it to the client by
 * adding the following parameters to the query component of the
 * redirection URI using the "application/x-www-form-urlencoded" format,
 * per Appendix B
 *
 * ### Example Response
 * <code>
 * HTTP/1.1 302 Found
 * Location: https://client.example.com/cb?code=SplxlOBeZQQYbYS6WxSbIA&state=xyz
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-B
 *
 * @template TokenType of non-empty-string
 * @template TState of string|null
 * @template TScope of string|null
 * @note
 *  <strong>
 *      The authorization server MUST include a state parameter in the response
 *  </strong>
 */
interface AuthorizationResponseInterface extends
    ScopeParameterInterface,
    StateParameterInterface,
    CodeParameterInterface,
    ExpiresInParameterInterface,
    TokenTypeParameterInterface,
    JsonSerializable
{
    /**
     * Get additional parameters response
     * This can be useful for client side verification
     *
     * @return array{
     *     ...<string, mixed>
     * }
     * eg: ['example' => 'example_value', 'state' => 'client-string-state']
     */
    public function getAdditionalData() : array;

    /**
     * @return array{
     *     "code": non-empty-string,
     *     "token_type": TokenType,
     *     "expires_in"?: int|null, // recommended depending lifetime
     *     "state"?: TState, // state is required if client sending state
     *     "scope"?: TScope,
     *     ...<string, mixed>
     * }
     * @see self::getCode()
     */
    public function jsonSerialize(): array;
}
