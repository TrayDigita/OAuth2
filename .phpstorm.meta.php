<?php
// .phpstorm.meta.php
declare(strict_types=1);

use TrayDigita\OAuth2\Clients\GrantRegistry;

namespace PHPSTORM_META {
    override(\TrayDigita\OAuth2\Interfaces\Clients\GrantRegistryInterface::getGrant(0), map([
        'authorization_code' => \TrayDigita\OAuth2\Interfaces\Requests\Grants\AuthorizationCodeGrantInterface::class,
        'client_credentials' => \TrayDigita\OAuth2\Interfaces\Requests\Grants\ClientCredentialsGrantInterface::class,
        'refresh_token' => \TrayDigita\OAuth2\Interfaces\Requests\Grants\RefreshTokenGrantInterface::class,
        'password' => \TrayDigita\OAuth2\Interfaces\Requests\Grants\ResourceOwnerGrantInterface::class,
        'implicit' => \TrayDigita\OAuth2\Interfaces\Requests\Grants\ImplicitGrantInterface::class,
    ]));
}
