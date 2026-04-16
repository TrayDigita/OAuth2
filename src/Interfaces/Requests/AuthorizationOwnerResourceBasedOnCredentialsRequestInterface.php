<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests;

use TrayDigita\OAuth2\Interfaces\Parameters\Requests\PasswordParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Requests\UsernameParameterInterface;

/**
 * Based on RFC6749#section-11.2.2,
 * The Initial Registry Contents
 * Token request based on user & password
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 *
 * @template-extends TokenRequestInterface<"password">
 */
interface AuthorizationOwnerResourceBasedOnCredentialsRequestInterface extends
    TokenRequestInterface,
    UsernameParameterInterface,
    PasswordParameterInterface
{
}
