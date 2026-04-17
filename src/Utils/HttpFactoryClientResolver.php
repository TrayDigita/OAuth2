<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
declare(strict_types=1);

namespace TrayDigita\OAuth2\Utils;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedDependencyException;
use function class_exists;

class HttpFactoryClientResolver
{
    /**
     * ClientInterface instance used for making HTTP requests.
     * This can be set manually using the setClient method,
     * or it will be automatically resolved based on available implementations.
     *
     * @var ClientInterface $client
     */
    private static ClientInterface $client;

    /**
     * @var array<non-empty-string, mixed> $storage Storage for custom factory instances
     */
    private static array $storage = [
        StreamFactoryInterface::class => null,
        UriFactoryInterface::class => null,
        ResponseFactoryInterface::class => null,
        RequestFactoryInterface::class => null,
        ServerRequestFactoryInterface::class => null,
        UploadedFileFactoryInterface::class => null,
    ];

    /**
     * Cached instance of Nyholm\Psr7\Factory\Psr17Factory to avoid multiple instantiations
     * @var \Nyholm\Psr7\Factory\Psr17Factory $psr17Factory
     */
    private static \Nyholm\Psr7\Factory\Psr17Factory $psr17Factory;

    /**
     * Cached instance of GuzzleHttp\Psr7\HttpFactory to avoid multiple instantiations
     * @var \GuzzleHttp\Psr7\HttpFactory $guzzleHttpFactory
     */
    private static \GuzzleHttp\Psr7\HttpFactory $guzzleHttpFactory;

    /**
     * Set a custom StreamFactoryInterface implementation.
     *
     * @param StreamFactoryInterface $factory The factory instance to use for creating stream instances.
     */
    public static function setStreamFactory(StreamFactoryInterface $factory): void
    {
        self::$storage[StreamFactoryInterface::class] = $factory;
    }

    /**
     * Set a custom UriFactoryInterface implementation.
     *
     * @param UriFactoryInterface $factory The factory instance to use for creating URI instances.
     */
    public static function setUriFactory(UriFactoryInterface $factory): void
    {
        self::$storage[UriFactoryInterface::class] = $factory;
    }

    /**
     * Set a custom ResponseFactoryInterface implementation.
     *
     * @param ResponseFactoryInterface $factory The factory instance to use for creating response instances.
     */
    public static function setResponseFactory(ResponseFactoryInterface $factory): void
    {
        self::$storage[ResponseFactoryInterface::class] = $factory;
    }

    /**
     * Set a custom RequestFactoryInterface implementation.
     *
     * @param RequestFactoryInterface $factory The factory instance to use for creating request instances.
     */
    public static function setRequestFactory(RequestFactoryInterface $factory): void
    {
        self::$storage[RequestFactoryInterface::class] = $factory;
    }

    /**
     * Set a custom ServerRequestFactoryInterface implementation.
     *
     * @param ServerRequestFactoryInterface $factory The factory instance to use for creating server request instances.
     */
    public static function setServerRequestFactory(ServerRequestFactoryInterface $factory): void
    {
        self::$storage[ServerRequestFactoryInterface::class] = $factory;
    }

    /**
     * Set a custom UploadedFileFactoryInterface implementation.
     *
     * @param UploadedFileFactoryInterface $factory The factory instance to use for creating uploaded file instances.
     */
    public static function setUploadedFileFactory(UploadedFileFactoryInterface $factory): void
    {
        self::$storage[UploadedFileFactoryInterface::class] = $factory;
    }

    /**
     * Get a StreamFactoryInterface implementation.
     *
     * @return StreamFactoryInterface
     * @throws UnsatisfiedDependencyException
     */
    public static function getStreamFactory(): StreamFactoryInterface
    {
        if (isset(self::$storage[StreamFactoryInterface::class])) {
            return self::$storage[StreamFactoryInterface::class];
        }
        if (class_exists('GuzzleHttp\Psr7\HttpFactory')) {
            self::$guzzleHttpFactory ??= new \GuzzleHttp\Psr7\HttpFactory();
            return self::$storage[StreamFactoryInterface::class] = self::$guzzleHttpFactory;
        }
        if (class_exists('Nyholm\Psr7\Factory\Psr17Factory')) {
            self::$psr17Factory ??= new \Nyholm\Psr7\Factory\Psr17Factory();
            return self::$storage[StreamFactoryInterface::class] = self::$psr17Factory;
        }
        if (class_exists('Laminas\Diactoros\StreamFactory')) {
            return self::$storage[StreamFactoryInterface::class] = new \Laminas\Diactoros\StreamFactory();
        }
        throw new UnsatisfiedDependencyException(
            'No suitable StreamFactoryInterface implementation found'
        );
    }

    /**
     * Get a UriFactoryInterface implementation.
     *
     * @return UriFactoryInterface
     * @throws UnsatisfiedDependencyException
     */
    public static function getUriFactory(): UriFactoryInterface
    {
        if (isset(self::$storage[UriFactoryInterface::class])) {
            return self::$storage[UriFactoryInterface::class];
        }
        if (class_exists('GuzzleHttp\Psr7\HttpFactory')) {
            self::$guzzleHttpFactory ??= new \GuzzleHttp\Psr7\HttpFactory();
            return self::$storage[UriFactoryInterface::class] = self::$guzzleHttpFactory;
        }
        if (class_exists('Nyholm\Psr7\Factory\Psr17Factory')) {
            self::$psr17Factory ??= new \Nyholm\Psr7\Factory\Psr17Factory();
            return self::$storage[StreamFactoryInterface::class] = self::$psr17Factory;
        }
        if (class_exists('Laminas\Diactoros\UriFactory')) {
            return self::$storage[UriFactoryInterface::class] = new \Laminas\Diactoros\UriFactory();
        }
        throw new UnsatisfiedDependencyException(
            'No suitable UriFactoryInterface implementation found'
        );
    }

    /**
     * Get a UriFactoryInterface implementation.
     *
     * @return ResponseFactoryInterface
     * @throws UnsatisfiedDependencyException
     */
    public static function getResponseFactory(): ResponseFactoryInterface
    {
        if (isset(self::$storage[ResponseFactoryInterface::class])) {
            return self::$storage[ResponseFactoryInterface::class];
        }
        if (class_exists('GuzzleHttp\Psr7\HttpFactory')) {
            self::$guzzleHttpFactory ??= new \GuzzleHttp\Psr7\HttpFactory();
            return self::$storage[ResponseFactoryInterface::class] = self::$guzzleHttpFactory;
        }
        if (class_exists('Nyholm\Psr7\Factory\Psr17Factory')) {
            self::$psr17Factory ??= new \Nyholm\Psr7\Factory\Psr17Factory();
            return self::$storage[ResponseFactoryInterface::class] = self::$psr17Factory;
        }
        if (class_exists('Laminas\Diactoros\ResponseFactory')) {
            return self::$storage[ResponseFactoryInterface::class] = new \Laminas\Diactoros\ResponseFactory();
        }
        throw new UnsatisfiedDependencyException(
            'No suitable UriFactoryInterface implementation found'
        );
    }

    /**
     * Get a ResponseFactoryInterface implementation.
     *
     * @return RequestFactoryInterface
     * @throws UnsatisfiedDependencyException
     */
    public static function getRequestFactory(): RequestFactoryInterface
    {
        if (isset(self::$storage[RequestFactoryInterface::class])) {
            return self::$storage[RequestFactoryInterface::class];
        }
        if (class_exists('GuzzleHttp\Psr7\HttpFactory')) {
            self::$guzzleHttpFactory ??= new \GuzzleHttp\Psr7\HttpFactory();
            return self::$storage[RequestFactoryInterface::class] = self::$guzzleHttpFactory;
        }
        if (class_exists('Nyholm\Psr7\Factory\Psr17Factory')) {
            self::$psr17Factory ??= new \Nyholm\Psr7\Factory\Psr17Factory();
            return self::$storage[RequestFactoryInterface::class] = self::$psr17Factory;
        }
        if (class_exists('Laminas\Diactoros\RequestFactory')) {
            return self::$storage[RequestFactoryInterface::class] = new \Laminas\Diactoros\RequestFactory();
        }
        throw new UnsatisfiedDependencyException(
            'No suitable RequestFactoryInterface implementation found'
        );
    }

    /**
     * Get a ServerRequestFactoryInterface implementation.
     *
     * @return ServerRequestFactoryInterface
     * @throws UnsatisfiedDependencyException
     */
    public static function getServerRequestFactory(): ServerRequestFactoryInterface
    {
        if (isset(self::$storage[ServerRequestFactoryInterface::class])) {
            return self::$storage[ServerRequestFactoryInterface::class];
        }
        if (class_exists('GuzzleHttp\Psr7\HttpFactory')) {
            self::$guzzleHttpFactory ??= new \GuzzleHttp\Psr7\HttpFactory();
            return self::$storage[ServerRequestFactoryInterface::class] = self::$guzzleHttpFactory;
        }
        if (class_exists('Nyholm\Psr7\Factory\Psr17Factory')) {
            self::$psr17Factory ??= new \Nyholm\Psr7\Factory\Psr17Factory();
            return self::$storage[ServerRequestFactoryInterface::class] = self::$psr17Factory;
        }
        if (class_exists('Laminas\Diactoros\ServerRequestFactory')) {
            return self::$storage[ServerRequestFactoryInterface::class] = new \Laminas\Diactoros\ServerRequestFactory();
        }
        throw new UnsatisfiedDependencyException(
            'No suitable ServerRequestFactoryInterface implementation found'
        );
    }

    /**
     * Get a UploadedFileFactoryInterface implementation.
     *
     * @return UploadedFileFactoryInterface
     * @throws UnsatisfiedDependencyException
     */
    public static function getUploadedFileFactory(): UploadedFileFactoryInterface
    {
        if (isset(self::$storage[UploadedFileFactoryInterface::class])) {
            return self::$storage[UploadedFileFactoryInterface::class];
        }
        if (class_exists('GuzzleHttp\Psr7\HttpFactory')) {
            self::$guzzleHttpFactory ??= new \GuzzleHttp\Psr7\HttpFactory();
            return self::$storage[UploadedFileFactoryInterface::class] = self::$guzzleHttpFactory;
        }
        if (class_exists('Nyholm\Psr7\Factory\Psr17Factory')) {
            self::$psr17Factory ??= new \Nyholm\Psr7\Factory\Psr17Factory();
            return self::$storage[UploadedFileFactoryInterface::class] = self::$psr17Factory;
        }
        if (class_exists('Laminas\Diactoros\UploadedFileFactory')) {
            return self::$storage[UploadedFileFactoryInterface::class] = new \Laminas\Diactoros\UploadedFileFactory();
        }
        throw new UnsatisfiedDependencyException(
            'No suitable UploadedFileFactoryInterface implementation found'
        );
    }

    /**
     * Set a custom ClientInterface implementation.
     *
     * @param ClientInterface $client
     */
    public static function setClient(ClientInterface $client): void
    {
        self::$client = $client;
    }

    /**
     * Get a ClientInterface implementation.
     *
     * @return ClientInterface
     * @throws UnsatisfiedDependencyException
     */
    public static function getClient(): ClientInterface
    {
        if (isset(self::$client)) {
            return self::$client;
        }
        if (class_exists('GuzzleHttp\Client')) {
            return self::$client = new \GuzzleHttp\Client();
        }
        if (class_exists('Symfony\Component\HttpClient\Psr18Client')) {
            return self::$client = new \Symfony\Component\HttpClient\Psr18Client(
                responseFactory: self::getResponseFactory(),
                streamFactory: self::getStreamFactory()
            );
        }
        throw new UnsatisfiedDependencyException(
            'No suitable HTTP client implementation found'
        );
    }
}
