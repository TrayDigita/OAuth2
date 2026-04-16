<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters;

/**
 * The void / empty refresh token parameter
 * This for implicit grant request, this is follow RFC standard for: rfc6749#section-4.2.2
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2.2
 */
interface RefreshTokenParameterMustVoidInterface
{
    /**
     * Refresh token should empty
     *
     * @return void
     */
    public function getRefreshToken() : void;
}
