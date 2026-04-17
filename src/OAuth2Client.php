<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use TrayDigita\OAuth2\Clients\ProviderRegistry;
use TrayDigita\OAuth2\Interfaces\Clients\ClientProviderRegistryInterface;
use TrayDigita\OAuth2\Interfaces\Clients\GrantRegistryInterface;
use TrayDigita\OAuth2\Utils\HttpFactoryClientResolver;

/**
 * Class OAuth2Client
 *
 * This class serves as the main entry point for interacting with the OAuth2 library.
 * It manages the grant registry, provider registry, and HTTP client dependencies.
 * @template GrantReg of GrantRegistryInterface
 * @template TList of "authorization_code"|"client_credentials"|"refresh_token"|"password"|"implicit"|non-empty-string
 */
class OAuth2Client
{
    /**
     * @param GrantRegistryInterface<TList> $grantRegistry
     * @param ClientProviderRegistryInterface $providerRegistry
     * @param ClientInterface|null $client
     * @param StreamFactoryInterface|null $streamFactory
     * @param UriFactoryInterface|null $uriFactory
     * @param RequestFactoryInterface|null $requestFactory
     */
    public function __construct(
        private GrantRegistryInterface          $grantRegistry = new GrantRegistry(),
        private ClientProviderRegistryInterface $providerRegistry = new ProviderRegistry(),
        private ?ClientInterface                $client = null,
        private ?StreamFactoryInterface         $streamFactory = null,
        private ?UriFactoryInterface            $uriFactory = null,
        private ?RequestFactoryInterface        $requestFactory = null,
    ) {
    }

    /**
     * Set HTTP client
     *
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * Get HTTP client
     *
     * @return ClientInterface
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedDependencyException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getClient(): ClientInterface
    {
        if (!isset($this->client)) {
            $this->client = HttpFactoryClientResolver::getClient();
        }
        return $this->client;
    }

    /**
     * Get Stream Factory
     *
     * @return StreamFactoryInterface|null
     */
    public function getStreamFactory(): ?StreamFactoryInterface
    {
        return $this->streamFactory;
    }

    /**
     * Set Stream Factory
     *
     * @param StreamFactoryInterface $streamFactory
     */
    public function setStreamFactory(StreamFactoryInterface $streamFactory): void
    {
        $this->streamFactory = $streamFactory;
    }

    /**
     * Get URI Factory
     *
     * @return UriFactoryInterface|null
     */
    public function getUriFactory(): ?UriFactoryInterface
    {
        if (!isset($this->uriFactory)) {
            $this->uriFactory = HttpFactoryClientResolver::getUriFactory();
        }
        return $this->uriFactory;
    }

    /**
     * Set URI Factory
     *
     * @param UriFactoryInterface $uriFactory
     */
    public function setUriFactory(UriFactoryInterface $uriFactory): void
    {
        $this->uriFactory = $uriFactory;
    }

    /**
     * Get Request Factory
     *
     * @return RequestFactoryInterface|null
     */
    public function getRequestFactory(): ?RequestFactoryInterface
    {
        if (!isset($this->requestFactory)) {
            $this->requestFactory = HttpFactoryClientResolver::getRequestFactory();
        }
        return $this->requestFactory;
    }

    /**
     * Set Request Factory
     *
     * @param RequestFactoryInterface $requestFactory
     */
    public function setRequestFactory(RequestFactoryInterface $requestFactory): void
    {
        $this->requestFactory = $requestFactory;
    }

    /**
     * Set grant registry
     *
     * @param GrantRegistryInterface<TList> $grantRegistry
     */
    public function setGrantRegistry(GrantRegistryInterface $grantRegistry): void
    {
        $this->grantRegistry = $grantRegistry;
    }

    /**
     * Get grant registry
     *
     * @return GrantRegistryInterface<TList>
     */
    public function getGrantRegistry(): GrantRegistryInterface
    {
        return $this->grantRegistry;
    }

    /**
     * Get provider registry
     *
     * @return ClientProviderRegistryInterface
     */
    public function getProviderRegistry(): ClientProviderRegistryInterface
    {
        return $this->providerRegistry;
    }

    /**
     * Set provider registry
     *
     * @param ClientProviderRegistryInterface $providerRegistry
     */
    public function setProviderRegistry(ClientProviderRegistryInterface $providerRegistry): void
    {
        $this->providerRegistry = $providerRegistry;
    }
}
