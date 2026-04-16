<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Requests;

/**
 * The resource owner username.
 * The "username" parameter is used in the Resource Owner Password Credentials Grant
 * to specify the resource owner's username.
 * The client uses the "username" parameter to authenticate the resource owner and obtain
 * an access token on their behalf.
 *
 * <code>
 *     Parameter usage location: token request
 * </code>
 *
 * The "username" element is defined in Section 4.3.2:
 * <code>
 *     username = *UNICODECHARNOCRLF
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.15
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.3.2
 *
 * @meta username: string|null
 */
interface UsernameParameterInterface
{
    /**
     * The username parameter name as defined in RFC6749#section-4.3.2
     */
    public const USERNAME_PARAMETER_NAME = 'username';

    /**
     * The resource owner's username as described on RFC6749#section-4.3.2
     * @return ?string
     */
    public function getUsername() : ?string;
}
