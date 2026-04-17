<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Base;

use TrayDigita\OAuth2\Interfaces\Parameters\Requests\ClientIdParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Requests\RedirectUriParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Requests\ResponseTypeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\ScopeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\StateParameterInterface;
use TrayDigita\OAuth2\Interfaces\Requests\OAuth2RequestInterface;
use TrayDigita\OAuth2\Interfaces\Scratches\PreparationRequestInterface;

/**
 * @template TResponse of "code"|"token"
 * @template-covariant GrantType of non-empty-string
 * @template-covariant GrantTypeKey of non-empty-string
 * @template-covariant GrantTypeValue of non-empty-string
 *
 * @template-extends OAuth2RequestInterface<GrantType, GrantTypeKey, GrantTypeValue>
 * @template-extends ResponseTypeParameterInterface<TResponse>
 */
interface AuthorizationRequestInterface extends
    ClientIdParameterInterface,
    ResponseTypeParameterInterface,
    RedirectUriParameterInterface,
    ScopeParameterInterface,
    StateParameterInterface,
    PreparationRequestInterface,
    OAuth2RequestInterface
{
}
