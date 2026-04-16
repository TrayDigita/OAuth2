<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Requests;

/**
 * The resource owner password.
 * The "password" parameter is used in the
 * Resource Owner Password Credentials Grant to include the resource owner's password in the token request.
 * The client uses this parameter to obtain an access token by directly using the resource owner's credentials.
 * <code>
 *     Parameter usage location: token request
 * </code>
 *
 * The "password" element is defined in Section 4.3.2:
 * <code>
 *     password = *UNICODECHARNOCRLF
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.16
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.3.2
 *
 * @meta password: string|null
 */
interface PasswordParameterInterface
{
    /**
     * The password parameter name as defined in RFC6749#section-4.3.2
     */
    public const PASSWORD_PARAMETER_NAME = 'password';

    /**
     * The resource owner's password as described on RFC6749#section-4.3.2
     * @return ?string
     */
    public function getPassword(): ?string;
}
