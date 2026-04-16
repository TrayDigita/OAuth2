<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Grants;

/**
 * Grant type token request parameters interface
 *
 * @template-covariant GrantType of non-empty-string
 * @template-extends GrantRequestParametersInterface<GrantType>
 */
interface GrantTypeTokenRequest extends GrantRequestParametersInterface
{
}
