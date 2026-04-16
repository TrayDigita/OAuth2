<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Clients;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantRequestParametersInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantTypeAuthorizationRequest;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantTypeTokenRequest;
use TrayDigita\OAuth2\Interfaces\Responses\AccessTokenResponseInterface;
use TrayDigita\OAuth2\Interfaces\Responses\AuthorizationResponseInterface;

/**
 * Client Provider
 *
 * This interface defines the methods required for a client provider to create
 * authorization and token requests in an OAuth2 implementation.
 * Implementing classes should provide the necessary logic to generate the appropriate
 * URLs and create PSR-7 compliant requests based on the provided parameters.
 */
interface ClientProviderInterface
{
    /**
     * Get the name of the client provider.
     *
     * @return string The name of the client provider.
     */
    public function getName() : string;

    /**
     * Check if the client provider is operating in sandbox mode.
     *
     * @return bool True if the client provider is in sandbox mode, false otherwise.
     */
    public function isSandbox(): bool;

    /**
     * Get the authorization endpoint URL.
     *
     * @return non-empty-string
     */
    public function getAuthorizationEndpoint(): string;

    /**
     * Get the token endpoint URL.
     *
     * @return non-empty-string
     */
    public function getTokenEndpoint(): string;

    /**
     * Check if the client provider supports the specified grant type.
     *
     * @param GrantRequestParametersInterface<TGrant> $grant The grant request parameters to check.
     * @return bool True if the grant type is supported, false otherwise.
     * @template TGrant of non-empty-string
     */
    public function grantIsSupported(GrantRequestParametersInterface $grant): bool;

    /**
     * Authorize the client for the specified grant type and parameters.
     *
     * @param GrantTypeAuthorizationRequest<TGrant> $grant The parameters for the grant request.
     * @param array<string, mixed> $additionalParameters
     * Additional parameters to include in the authorization process.
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param UriFactoryInterface $uriFactory
     * @return RequestInterface
     * @template TGrant of non-empty-string
     * @throws \TrayDigita\OAuth2\Exceptions\UnsupportedOperationException
     *   if the grant type or request method is not supported by the client provider.
     * @throws \TrayDigita\OAuth2\Exceptions\OperationNotPermittedException
     *  if the operation is not permitted by the client provider,
     *  such as if the grant type is not allowed for the client.
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedGrantParameterException
     *  if the required parameters for the grant type are not satisfied,
     *  such as missing client credentials or authorization code.
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException
     *  if the required parameters for the token request are not satisfied,
     *  such as missing scope or redirect URI.
     * @throws \TrayDigita\OAuth2\Interfaces\Exceptions\ExceptionInterface
     *  if any other error occurs during the creation of the token request,
     *  such as an invalid parameter value or a failure to generate the request.
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @see self::grantIsSupported() for checking if the grant type is supported before calling this method.
     */
    public function createAuthorizeRequest(
        GrantTypeAuthorizationRequest $grant,
        array                         $additionalParameters,
        RequestFactoryInterface       $requestFactory,
        StreamFactoryInterface        $streamFactory,
        UriFactoryInterface           $uriFactory
    ): RequestInterface;

    /**
     * Create a token request for the specified grant type and parameters.
     *
     * @param GrantTypeTokenRequest<TGrant> $parameters The parameters for the token request.
     * @param array<string, mixed> $additionalParameters
     * Additional parameters to include in the token request.
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param UriFactoryInterface $uriFactory
     * @return RequestInterface
     * @template TGrant of non-empty-string
     * @throws \TrayDigita\OAuth2\Exceptions\UnsupportedOperationException
     *  if the grant type or request method is not supported by the client provider.
     * @throws \TrayDigita\OAuth2\Exceptions\OperationNotPermittedException
     * if the operation is not permitted by the client provider,
     * such as if the grant type is not allowed for the client.
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedGrantParameterException
     * if the required parameters for the grant type are not satisfied,
     * such as missing client credentials or authorization code.
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException
     * if the required parameters for the token request are not satisfied,
     * such as missing scope or redirect URI.
     * @throws \TrayDigita\OAuth2\Interfaces\Exceptions\ExceptionInterface
     * if any other error occurs during the creation of the token request,
     * such as an invalid parameter value or a failure to generate the request.
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @see self::grantIsSupported() for checking if the grant type is supported before calling this method.
     */
    public function createRequestToken(
        GrantTypeTokenRequest   $parameters,
        array                   $additionalParameters,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface  $streamFactory,
        UriFactoryInterface     $uriFactory
    ): RequestInterface;

    /**
     * Store the access token in the cache pool.
     *
     * @param GrantTypeTokenRequest<non-empty-string> $grantRequestParameters
     *      The parameters of the grant request associated with the access token.
     * @param AccessTokenResponseInterface<non-empty-string, ?string, ?string, ?string> $accessToken
     * The access token to store.
     * @param CacheItemPoolInterface|null $cachePool The cache pool to use for storing the access token.
     * If null, the client provider should use its default cache pool.
     * @return void
     */
    public function storeAccessToken(
        GrantTypeTokenRequest $grantRequestParameters,
        AccessTokenResponseInterface $accessToken,
        ?CacheItemPoolInterface $cachePool = null
    ) : void;

    /**
     * Store the authorization code in the cache pool.
     *
     * @param GrantTypeAuthorizationRequest<non-empty-string> $authorizationRequest
     *      The parameters of the authorization request associated with the authorization code.
     * @param AuthorizationResponseInterface<non-empty-string, ?string, ?string> $authorizationResponse
     * The authorization response containing the authorization code to store.
     * @param CacheItemPoolInterface|null $cachePool The cache pool to use for storing the authorization code.
     * If null, the client provider should use its default cache pool.
     * @return void
     */
    public function storeAuthorizationCode(
        GrantTypeAuthorizationRequest $authorizationRequest,
        AuthorizationResponseInterface $authorizationResponse,
        ?CacheItemPoolInterface $cachePool = null
    ) : void;
}
