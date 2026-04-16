<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Clients;

use TrayDigita\OAuth2\Interfaces\Clients\ClientProviderRegistryInterface;
use TrayDigita\OAuth2\Interfaces\Clients\GrantRegistryInterface;

class OAuth2Client
{
    public function __construct(
        private GrantRegistryInterface $grantRegistry = new GrantRegistry(),
        private ClientProviderRegistryInterface $providerRegistry = new ProviderRegistry()
    ) {
    }

    public function setGrantRegistry(GrantRegistryInterface $grantRegistry): void
    {
        $this->grantRegistry = $grantRegistry;
    }

    public function getGrantRegistry(): GrantRegistryInterface
    {
        return $this->grantRegistry;
    }

    public function getProviderRegistry(): ClientProviderRegistryInterface
    {
        return $this->providerRegistry;
    }

    public function setProviderRegistry(ClientProviderRegistryInterface $providerRegistry): void
    {
        $this->providerRegistry = $providerRegistry;
    }
}
