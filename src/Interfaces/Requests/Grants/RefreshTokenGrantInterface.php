<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Grants;

use TrayDigita\OAuth2\Enums\RequestType;

/**
 * ## Refresh tokens are credentials used to obtain access tokens.
 *
 * Refresh tokens are issued to the client by the authorization server and are
 * used to obtain a new access token when the current access token
 * becomes invalid or expires, or to obtain additional access tokens
 * with identical or narrower scope (access tokens may have a shorter
 * lifetime and fewer permissions than authorized by the resource
 * owner).  Issuing a refresh token is optional at the discretion of the
 * authorization server.
 *
 * ### Flow:
 * <code>
 * +--------+                                           +---------------+
 * |        |--(A)------- Authorization Grant --------->|               |
 * |        |                                           |               |
 * |        |<-(B)----------- Access Token -------------|               |
 * |        |               & Refresh Token             |               |
 * |        |                                           |               |
 * |        |                            +----------+   |               |
 * |        |--(C)---- Access Token ---->|          |   |               |
 * |        |                            |          |   |               |
 * |        |<-(D)- Protected Resource --| Resource |   | Authorization |
 * | Client |                            |  Server  |   |     Server    |
 * |        |--(E)---- Access Token ---->|          |   |               |
 * |        |                            |          |   |               |
 * |        |<-(F)- Invalid Token Error -|          |   |               |
 * |        |                            +----------+   |               |
 * |        |                                           |               |
 * |        |--(G)----------- Refresh Token ----------->|               |
 * |        |                                           |               |
 * |        |<-(H)----------- Access Token -------------|               |
 * +--------+           & Optional Refresh Token        +---------------+
 *
 * </code>
 *
 * ### Example Request:
 * <code>
 * POST /token HTTP/1.1
 * Host: server.example.com
 * Content-Type: application/x-www-form-urlencoded
 *
 * grant_type=refresh_token&refresh_token=tGzv3JOkF0XG5Qx2TlKWIA
 * &client_id=s6BhdRkqt3&client_secret=7Fjfp0ZBr1KtDRbnfVdmIw
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-1.5 Introduction about Refresh Token
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-6 Refreshing an Access Token (RFC 6749, §6)
 * @see https://datatracker.ietf.org/doc/html/rfc6749#section-2.3.1 Example Password Authentication
 *
 * @template-extends GrantTypeTokenRequest<"refresh_token">
 */
interface RefreshTokenGrantInterface extends GrantTypeTokenRequest
{
    /**
     * Grant name constant
     * @var "refresh_token"
     */
    public const TYPE = 'refresh_token';

    /**
     * @inheritdoc
     *
     * @return list<'grant_type','refresh_token'>&list<non-empty-string>
     */
    public function getRequiredParameters(RequestType $requestType,): array;

    /**
     * @inheritdoc
     *
     * @template TKey of string
     * @template TValue
     * @param RequestType $requestType The type of the request (e.g., token endpoint, authorization endpoint)
     * @param array<TKey, TValue> $parameters
     * @param array<TKey, TValue> $defaultParameters
     * @return array{
     *     "grant_type" : "refresh_token",
     *     "refresh_token" : non-empty-string,
     *     "scope"?: non-empty-string,
     *     "state"?: non-empty-string,
     *     "redirect_uri"?: non-empty-string,
     *     ...<TKey, TValue>
     * }
     */
    public function prepareParameters(RequestType $requestType, array $parameters, array $defaultParameters): array;
}
