<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Scratches;

use TrayDigita\OAuth2\Interfaces\Parameters\ScopeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\StateParameterInterface;

/**
 * Based on RFC6749#section-11.2.2,
 * The Initial Registry Contents
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 */
interface TokenInterface extends
    ScopeParameterInterface,
    StateParameterInterface
{
}
