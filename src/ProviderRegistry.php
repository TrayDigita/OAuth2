<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2;

use TrayDigita\OAuth2\Exceptions\Clients\ProviderAlreadyExistsException;
use TrayDigita\OAuth2\Exceptions\Clients\ProviderNotFoundException;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use TrayDigita\OAuth2\Interfaces\Clients\ClientProviderInterface;
use TrayDigita\OAuth2\Interfaces\Clients\ClientProviderRegistryInterface;

/**
 * Provider Registry
 * Stores and manages client providers in the OAuth2 server implementation.
 */
class ProviderRegistry implements ClientProviderRegistryInterface
{
    /**
     * The providers array holds the registered client providers in the registry.
     * The keys are non-empty strings representing the provider names,
     * and the values are instances of ClientProviderInterface representing the corresponding client providers.
     *
     * @var array<non-empty-string, ClientProviderInterface>
     */
    protected array $providers = [];

    /**
     * @inheritdoc
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @inheritdoc
     */
    public function getProvider(string $name): ClientProviderInterface
    {
        if (!isset($this->providers[$name])) {
            throw new ProviderNotFoundException(
                "Client provider with name '{$name}' not found in the registry."
            );
        }
        return $this->providers[$name];
    }

    /**
     * @inheritdoc
     */
    public function hasProvider(string $name): bool
    {
        return isset($this->providers[$name]);
    }

    /**
     * @inheritdoc
     */
    public function removeProvider(string $name): void
    {
        if (!isset($this->providers[$name])) {
            throw new ProviderNotFoundException(
                "Client provider with name '{$name}' not found in the registry."
            );
        }
        unset($this->providers[$name]);
    }

    /**
     * @inheritdoc
     */
    public function addProvider(string $name, ClientProviderInterface $provider): void
    {
        if ($name === '') {
            throw new UnsatisfiedParameterException(
                'Provider name cannot be empty.'
            );
        }
        if (isset($this->providers[$name])) {
            throw new ProviderAlreadyExistsException(
                "Client provider with name '{$name}' already exists in the registry."
            );
        }
        $this->providers[$name] = $provider;
    }

    /**
     * @inheritdoc
     */
    public function setProvider(string $name, ClientProviderInterface $provider): void
    {
        if ($name === '') {
            throw new UnsatisfiedParameterException(
                'Provider name cannot be empty.'
            );
        }
        $this->providers[$name] = $provider;
    }
}
