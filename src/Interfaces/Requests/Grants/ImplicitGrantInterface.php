<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Grants;

use TrayDigita\OAuth2\Enums\RequestType;

/**
 * ## The implicit grant type is used to obtain access tokens
 * (it does not support the issuance of refresh tokens) and is optimized for public
 * clients known to operate a particular redirection URI.
 * These clients are typically implemented in a browser using a scripting language
 * such as JavaScript.
 *
 * ### Flow:
 * <code>
 * +----------+
 * | Resource |
 * |  Owner   |
 * |          |
 * +----------+
 * ^
 * |
 * (B)
 * +----|-----+          Client Identifier     +---------------+
 * |         -+----(A)-- & Redirection URI --->|               |
 * |  User-   |                                | Authorization |
 * |  Agent  -|----(B)-- User authenticates -->|     Server    |
 * |          |                                |               |
 * |          |<---(C)--- Redirection URI ----<|               |
 * |          |          with Access Token     +---------------+
 * |          |            in Fragment
 * |          |                                +---------------+
 * |          |----(D)--- Redirection URI ---->|   Web-Hosted  |
 * |          |          without Fragment      |     Client    |
 * |          |                                |    Resource   |
 * |     (F)  |<---(E)------- Script ---------<|               |
 * |          |                                +---------------+
 * +-|--------+
 * |    |
 * (A)  (G) Access Token
 * |    |
 * ^    v
 * +---------+
 * |         |
 * |  Client |
 * |         |
 * +---------+
 *
 * Note: The lines illustrating steps (A) and (B) are broken into two
 * parts as they pass through the user-agent.
 *
 * Implicit Grant Flow
 * </code>
 *
 * ### Example Request:
 * <code>
 * GET /authorize?response_type=token&client_id=s6BhdRkqt3&state=xyz
 * &redirect_uri=https%3A%2F%2Fclient%2Eexample%2Ecom%2Fcb
 * HTTP/1.1
 * Host: server.example.com
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2 Implicit Grant (RFC 6749, §4.2)
 *
 * @template-covariant ClientId of non-empty-string
 * @extends GrantTypeAuthorizationInterface<"implicit">
 * @note
 * For redirect URI recommended using (#)/url fragment to prevent the access token from being exposed
 *      to the resource owner and other applications.
 * For example, the redirection URI can be `https://client.example.com/cb#access_token=ACCESS_TOKEN&state=xyz`
 * instead of `https://client.example.com/cb?access_token=ACCESS_TOKEN&state=xyz`
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2.1
 * @template-extends GrantTypeAuthorizationInterface<"implicit", "response_type", "token">
 */
interface ImplicitGrantInterface extends GrantTypeAuthorizationInterface
{
    /**
     * Grant name constant
     * @var "implicit"
     */
    public const TYPE = 'implicit';

    /**
     * @inheritdoc
     * @return "response_type"
     */
    public function getGrantTypeKey(): string;

    /**
     * @inheritdoc
     * @param RequestType $requestType The type of the request (e.g., authorization request, token request, etc.)
     * @return list<'client_id','response_type'>&list<non-empty-string>
     */
    public function getRequiredClientRequestParameters(RequestType $requestType): array;

    /**
     * @Inheritdoc
     * @template TKey of string
     * @template TValue
     * @param RequestType $requestType The type of the request (e.g., authorization request, token request, etc.)
     * @param array<TKey, TValue> $parameters
     * @param array<TKey, TValue> $defaultParameters
     * @return array{
     *     "client_id" : ClientId,
     *     "response_type" : "token", // response_type is required and should be "token" for implicit grant
     *     "scope"?: non-empty-string,
     *     "state"?: non-empty-string,
     *     "redirect_uri"?: non-empty-string,
     *     ...<TKey, TValue>
     * }
     */
    public function prepareClientRequestParameters(
        RequestType $requestType,
        array $parameters,
        array $defaultParameters
    ): array;
}
