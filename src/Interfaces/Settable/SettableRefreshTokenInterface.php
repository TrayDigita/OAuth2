<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Settable;

/**
 * Settable refresh token
 */
interface SettableRefreshTokenInterface
{
    /**
     * Set refresh token
     *
     * @param string $refreshToken
     * @return void
     */
    public function setRefreshToken(string $refreshToken) : void;
}
