<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Grants;

/**
 * Grant type authorization request parameters interface
 *
 * @template-covariant GrantType of non-empty-string
 * @template-covariant GrantTypeKey of non-empty-string
 * @template-covariant GrantTypeValue of non-empty-string
 * @template-extends GrantParametersInterface<GrantType, GrantTypeKey, GrantTypeValue>
 */
interface GrantTypeAuthorizationInterface extends GrantParametersInterface
{
}
