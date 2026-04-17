<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Base;

use TrayDigita\OAuth2\Interfaces\Parameters\Requests\ClientIdParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Requests\ClientSecretParameterInterface;

/**
 * Based on RFC6749#section-11.2.2,
 * The Initial Registry Contents
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 *
 * @template-covariant GrantType of non-empty-string
 * @template-covariant GrantTypeKey of non-empty-string
 * @template-covariant GrantTypeValue of non-empty-string
 *
 * @template-extends TokenRequestInterface<GrantType, GrantTypeKey, GrantTypeValue>
 */
interface TokenBasedOnCredentialsRequestInterface extends
    TokenRequestInterface,
    ClientIdParameterInterface,
    ClientSecretParameterInterface
{
}
