<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Responses;

/**
 * This is not being part of OAuth2 specification,
 * but it is being used in OpenID Connect (OIDC) specification.
 *
 * The resource owner id
 * Being OpenID Connect (OIDC) / RFC 7519 (JWT).
 *
 * RFC 7519 (JSON Web Token): using `sub` as user identity
 * OpenID Connect (OIDC): using `sub` is required as Id Token claim
 *
 * ### Example of OpenID Connect (OIDC) id_token payload
 * <code>
 *     {
 *      "iss": "https://server.example.com",
 *      "sub": "24400320",
 *      "aud": "s6BhdRkqt3",
 *      "nonce": "n-0S6_WzA2Mj",
 *      "exp": 1311281970,
 *      "iat": 1311280970,
 *      "auth_time": 1311280969,
 *      "acr": "urn:mace:incommon:iap:silver"
 * }
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc7519#section-4.1.2 JSON Web Token (JWT) - Registered Claim Names
 * @link https://openid.net/specs/openid-connect-core-1_0.html#IDToken ID Token
 */
interface ResourceOwnerIdParameterInterface
{
    /**
     * This will be used as `sub` claim in JWT and OIDC id_token payload
     * Optional as response parameter.
     *
     * @return string|null
     */
    public function getResourceOwnerId() : ?string;
}
