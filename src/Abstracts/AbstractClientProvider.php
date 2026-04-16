<?php
// todo: Completing abstract class with common methods and properties for client providers
declare(strict_types=1);

namespace TrayDigita\OAuth2\Abstracts;

use TrayDigita\OAuth2\Interfaces\Clients\ClientProviderInterface;

/**
 * Abstract Client Provider
 * Provides a base implementation for client providers in the OAuth2 server implementation.
 * This abstract class can be extended by specific client provider implementations to provide common functionality.
 * @link https://datatracker.ietf.org/doc/html/rfc6749
 */
abstract class AbstractClientProvider implements ClientProviderInterface
{
    /**
     * @inheritdoc
     */
    abstract public function getName(): string;
}
