<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests;

use TrayDigita\OAuth2\Interfaces\Parameters\Requests\ClientIdParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Requests\RedirectUriParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Requests\ResponseTypeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\ScopeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\StateParameterInterface;
use TrayDigita\OAuth2\Interfaces\Scratches\PreparationRequestInterface;

/**
 * @template TResponse of "code"|"token"
 * @template-extends ResponseTypeParameterInterface<TResponse>
 */
interface AuthorizationRequestInterface extends
    ClientIdParameterInterface,
    ResponseTypeParameterInterface,
    RedirectUriParameterInterface,
    ScopeParameterInterface,
    StateParameterInterface,
    PreparationRequestInterface
{
}
