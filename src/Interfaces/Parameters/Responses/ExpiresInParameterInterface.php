<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Responses;

/**
 * The "expires_in" parameter is a positive integer that represents the lifetime in seconds of the access token.
 * For example, the value "3600" denotes that the access token will expire in one
 * hour from the time the response was generated. If the client receives an "expires_in" parameter,
 * it MUST NOT assume the access token will expire within any specific time period unless
 * the client has received this parameter.
 * If the authorization server does not include the "expires_in" parameter in the response
 * The lifetime in seconds of the access token.  For
 * example, the value "3600" denotes that the access token will
 * expire in one hour from the time the response was generated.
 * If omitted, the authorization server SHOULD provide the
 * expiration time via other means or document the default value.
 *
 * <code>
 *     Parameter usage location: authorization response, token response
 * </code>
 *
 * The "expires_in" element is defined in Sections 4.2.2 and 5.1:
 * <code>
 *      expires-in = 1*DIGIT
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.14
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-5.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 *
 * @meta expires_in: int|null
 */
interface ExpiresInParameterInterface
{
    /**
     * The expires_in parameter name as defined in RFC6749#section-4.2.2 and 5.1
     */
    public const EXPIRES_IN_PARAMETER_NAME = 'expires_in';

    /**
     * Get the lifetime in seconds of the access token.
     * For example, the value "3600" denotes that
     * the access token will expire in one hour from the time the response was generated.
     *
     * @return int|null
     */
    public function getExpiresIn(): ?int;
}
