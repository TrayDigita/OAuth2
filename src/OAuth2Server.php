<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Throwable;
use TrayDigita\OAuth2\Enums\ErrorType;
use TrayDigita\OAuth2\Exceptions\Response\OAuth2ResponseErrorException;
use TrayDigita\OAuth2\Interfaces\Clients\GrantRegistryInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantParametersInterface;
use TrayDigita\OAuth2\Utils\HttpFactoryClientResolver;
use function is_array;
use function is_int;
use function method_exists;

/**
 * OAuth2 Server class
 * @template TList of "authorization_code"|"client_credentials"|"refresh_token"|"password"|"implicit"|non-empty-string
 */
class OAuth2Server
{
    /**
     * OAuthServer constructor.
     *
     * @param GrantRegistryInterface<TList> $grantRegistry
     * @param StreamFactoryInterface|null $streamFactory
     * @param ResponseFactoryInterface|null $responseFactory
     * @param UriFactoryInterface|null $uriFactory
     * @param ServerRequestFactoryInterface|null $serverRequestFactory
     */
    public function __construct(
        private GrantRegistryInterface         $grantRegistry = new GrantRegistry(),
        private ?StreamFactoryInterface        $streamFactory = null,
        private ?ResponseFactoryInterface      $responseFactory = null,
        private ?UriFactoryInterface           $uriFactory = null,
        private ?ServerRequestFactoryInterface $serverRequestFactory = null,
    ) {
    }

    /**
     * Get Stream Factory
     *
     * @return StreamFactoryInterface
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedDependencyException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getStreamFactory(): StreamFactoryInterface
    {
        if (!isset($this->streamFactory)) {
            $this->streamFactory = HttpFactoryClientResolver::getStreamFactory();
        }
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
     * Get Response Factory
     *
     * @return ResponseFactoryInterface|null
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedDependencyException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getResponseFactory(): ?ResponseFactoryInterface
    {
        if (!isset($this->responseFactory)) {
            $this->responseFactory = HttpFactoryClientResolver::getResponseFactory();
        }
        return $this->responseFactory;
    }

    /**
     * Set Response Factory
     *
     * @param ResponseFactoryInterface $responseFactory
     */
    public function setResponseFactory(ResponseFactoryInterface $responseFactory): void
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Get URI Factory
     *
     * @return UriFactoryInterface
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedDependencyException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getUriFactory(): UriFactoryInterface
    {
        if (!isset($this->uriFactory)) {
            $this->uriFactory = HttpFactoryClientResolver::getUriFactory();
        }
        return $this->uriFactory;
    }

    public function getServerRequestFactory(): ServerRequestFactoryInterface
    {
        if (!isset($this->serverRequestFactory)) {
            $this->serverRequestFactory = HttpFactoryClientResolver::getServerRequestFactory();
        }
        return $this->serverRequestFactory;
    }

    public function setServerRequestFactory(ServerRequestFactoryInterface $serverRequestFactory): void
    {
        $this->serverRequestFactory = $serverRequestFactory;
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
     * Set grant registry
     *
     * @param GrantRegistryInterface<TList> $grantRegistry
     * @return void
     */
    public function setGrantRegistry(GrantRegistryInterface $grantRegistry): void
    {
        $this->grantRegistry = $grantRegistry;
    }

    /**
     * Create a new server request from the global variables.
     *
     * This method will attempt to create a server request using the provided server request factory.
     * It will populate the request with the appropriate method, URI, headers, and body based on the global variables.
     *
     * @return ServerRequestInterface The created server request.
     * @throws OAuth2ResponseErrorException if the server request cannot be created from the globals.
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function createNewServerRequest(): ServerRequestInterface
    {
        try {
            $requestFactory = $this->getServerRequestFactory();
            if (method_exists($requestFactory, 'fromGlobals')) {
                return $requestFactory->fromGlobals($_SERVER);
            }
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            if (!empty($_SERVER['QUERY_STRING'])) {
                $uri .= '?' . $_SERVER['QUERY_STRING'];
            }
            if (isset($_SERVER['HTTP_HOST'])) {
                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $uri = $scheme . '://' . $_SERVER['HTTP_HOST'] . $uri;
            }
            try {
                $uri = $this->getUriFactory()->createUri($uri);
            } catch (Throwable) {
                // If URI creation fails, fallback to the original URI string
            }
            $uploadedFiles = [];
            $request = $requestFactory->createServerRequest(
                $method,
                $uri,
                $_SERVER,
            );
            $uploadedFileFactory = HttpFactoryClientResolver::getUploadedFileFactory();
            $streamFactory = $this->getStreamFactory();
            foreach ($_FILES as $file) {
                if (is_array($file['name'])) {
                    foreach ($file['name'] as $index => $name) {
                        /**
                         * @var string $name
                         */
                        $stream = $streamFactory->createStreamFromFile($file['tmp_name'][$index]);
                        $uploadedFiles[] = $uploadedFileFactory->createUploadedFile(
                            $stream,
                            $file['size'][$index] ?? null,
                            $file['error'][$index] ?? 0,
                            $name,
                            $file['type'][$index] ?? null
                        );
                    }
                } else {
                    $stream = $streamFactory->createStreamFromFile($file['tmp_name']);
                    $uploadedFiles[] = $uploadedFileFactory->createUploadedFile(
                        $stream,
                        is_int($file['size']) ? $file['size'] : null,
                        $file['error'] ?? 0,
                        $file['name'] ?? null,
                        $file['type'] ?? null
                    );
                }
            }
            return $request
                ->withParsedBody($_POST)
                ->withQueryParams($_GET)
                ->withCookieParams($_COOKIE)
                ->withUploadedFiles($uploadedFiles);
        } catch (Throwable) {
        }
        throw new OAuth2ResponseErrorException(
            ErrorType::SERVER_ERROR,
            'Failed to create server request from globals'
        );
    }

    /**
     * Get the definitions of the grant and parameters from the server request.
     * This method will iterate through the registered grants and find the first one that supports the current request.
     *
     * <code>
     * $oauthServer = new OAuthServer();
     * $responseFactory = $oauthServer->getResponseFactory();
     * $streamFactory = $oauthServer->getStreamFactory();
     * try {
     *     $definitions = $oauthServer->getDefinitions();
     *     $grant = $definitions['grant'];
     *     $parameters = $definitions['parameters'];
     *     // Process the grant and parameters as needed
     * } catch (OAuth2ResponseErrorException $exception) {
     *     // Handle the error, e.g., log it or return an error response
     *     error_log($exception->getMessage());
     *     // Send the response to the client
     *     $exception->intoResponse($responseFactory, $streamFactory);
     * }
     * </code>
     *
     * @return array{
     *     'grant': GrantParametersInterface<TList, non-empty-string, non-empty-string>,
     *     'parameters': array<non-empty-string, mixed>
     * }
     * @throws OAuth2ResponseErrorException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @see \TrayDigita\OAuth2\Abstracts\AbstractGrant
     */
    public function getDefinitions(?ServerRequestInterface $request = null): array
    {
        $request ??= $this->createNewServerRequest();
        $grant = null;
        $grantIsFound = false;
        foreach ($this->getGrantRegistry()->getGrants() as $grantHandler) {
            if (!$grantIsFound) {
                $findGrantType = $grantHandler->findGrantType($request);
                $grantIsFound = $findGrantType !== null;
            }
            if ($grantHandler->isSupportedRequest($request)) {
                $grant = $grantHandler;
                $grantIsFound = true;
                break;
            }
        }
        // default response if no grant type is supported
        if (!($grant instanceof GrantParametersInterface)) {
            if ($grantIsFound) {
                throw new OAuth2ResponseErrorException(
                    ErrorType::INVALID_REQUEST,
                    message: 'The request is not supported'
                );
            }
            throw new OAuth2ResponseErrorException(
                ErrorType::UNSUPPORTED_GRANT_TYPE
            );
        }
        $parameters = $grant->parseServerRequest($request);
        return [
            'grant' => $grant,
            'parameters' => $parameters
        ];
    }
}
