<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters;

/**
 * Refresh tokens are credentials used to obtain access tokens.  Refresh
 * tokens are issued to the client by the authorization server and are
 * used to obtain a new access token when the current access token
 * becomes invalid or expires, or to obtain additional access tokens
 * with identical or narrower scope (access tokens may have a shorter
 * lifetime and fewer permissions than authorized by the resource
 * owner).  Issuing a refresh token is optional at the discretion of the
 * authorization server.
 *
 * <code>
 *  Parameter usage location: token request, token response
 * </code>
 *
 * The "refresh_token" element is defined in Sections 5.1 and 6:
 * <code>
 *     refresh-token = 1*VSCHAR
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.17
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-5.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-6
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 *
 * @meta refresh_token: string|null
 */
interface RefreshTokenParameterInterface
{
    /**
     * The refresh token parameter name as defined in RFC6749#section-5.1 and 6
     */
    public const REFRESH_TOKEN_PARAMETER_NAME = 'refresh_token';

    /**
     * Get the refresh token parameter value.
     *
     * @return string|null The refresh token value, or null if not present.
     */
    public function getRefreshToken(): ?string;
}
