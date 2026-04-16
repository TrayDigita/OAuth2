<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Clients;

/**
 * Client Provider Registry Interface
 *
 * This interface defines the contract for a client provider registry that manages the various client providers
 * in an OAuth2 server implementation.
 * @link https://datatracker.ietf.org/doc/html/rfc6749
 */
interface ClientProviderRegistryInterface
{
    /**
     * Get all registered client providers in the registry.
     *
     * @return array<non-empty-string, ClientProviderInterface>
     */
    public function getProviders(): array;

    /**
     * @param non-empty-string $name
     * @return ClientProviderInterface
     * @throws \TrayDigita\OAuth2\Exceptions\Clients\ProviderNotFoundException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getProvider(string $name): ClientProviderInterface;

    /**
     * Check if a client provider with the specified name exists in the registry.
     *
     * @param non-empty-string $name
     * @return bool
     */
    public function hasProvider(string $name): bool;

    /**
     * Remove the client provider with the specified name from the registry.
     *
     * @param non-empty-string $name
     * @return void
     * @throws \TrayDigita\OAuth2\Exceptions\Clients\ProviderNotFoundException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function removeProvider(string $name): void;

    /**
     * Add a client provider to the registry with the specified name.
     *
     * @param non-empty-string $name
     * @param ClientProviderInterface $provider
     * @return void
     * @throws \TrayDigita\OAuth2\Exceptions\Clients\ProviderAlreadyExistsException
     * If a client provider with the same name already exists in the registry.
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException
     * If the provider name is empty or if the provider instance does not meet the required criteria for registration.
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function addProvider(string $name, ClientProviderInterface $provider): void;

    /**
     * Replace the client provider with the specified name in the registry.
     * If a client provider with the same name already exists, it will be replaced with the new provider instance.
     * If not found, the new provider will be added to the registry.
     *
     * @param non-empty-string $name
     * @param ClientProviderInterface $provider
     * @return void
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException
     * If the provider name is empty or if the provider instance does not meet the required criteria for registration.
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function setProvider(string $name, ClientProviderInterface $provider): void;
}
