<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Responses;

/**
 * The access token as described on RFC6749#section-1.4
 * The access token is a string representing an authorization issued to the client.
 * The client uses the access token to access the protected resources on behalf of the resource owner.
 *
 * <code>
 *     Parameter usage location: authorization response, token response
 * </code>
 *
 * The "access_token" element is defined in Sections 4.2.2 and 5.1:
 * <code>
 * access-token = 1*VSCHAR
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.12
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-5.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-1.4
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-8.4
 *
 * @meta access_token: non-empty-string
 */
interface AccessTokenParameterInterface
{
    /**
     * The access token parameter name as defined in RFC6749#section-4.2.2 and 5.1
     */
    public const ACCESS_TOKEN_PARAMETER_NAME = 'access_token';

    /**
     * The access token as described on RFC6749#section-1.4
     * @return string
     */
    public function getAccessToken(): string;
}
