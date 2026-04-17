<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Abstracts;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use TrayDigita\OAuth2\Collections\FreezableCollections;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use TrayDigita\OAuth2\Interfaces\Collections\CollectionInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\CodeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Requests\ClientSecretParameterInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Base\AuthorizationRequestInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantTypeAuthorizationInterface;
use function http_build_query;
use function sprintf;
use const PHP_QUERY_RFC3986;

/**
 * @template ClientId of non-empty-string
 * @template TResponse of "code"|"token"
 * @template-covariant GrantType of non-empty-string
 * @template-covariant GrantTypeKey of non-empty-string
 * @template-covariant GrantTypeValue of non-empty-string
 * @template-implements AuthorizationRequestInterface<TResponse, GrantType, GrantTypeKey, GrantTypeValue>
 */
abstract class AbstractAuthorizationRequest implements AuthorizationRequestInterface
{
    /**
     * @var CollectionInterface<non-empty-string, mixed>
     */
    protected CollectionInterface $data;

    /**
     * ImplicitRequest constructor.
     *
     * @param GrantTypeAuthorizationInterface<GrantType, GrantTypeKey, GrantTypeValue> $grant
     * @param RequestType $requestType
     * @param ServerRequestInterface $serverRequest
     * @param ClientId $clientId
     * @param string $redirectUri
     * @param string|null $scope
     * @param string|null $state
     * @param iterable<non-empty-string, mixed> $additionalData
     * @throws UnsatisfiedParameterException if any of the required parameters are empty
     */
    public function __construct(
        protected GrantTypeAuthorizationInterface $grant,
        protected RequestType                     $requestType,
        protected ServerRequestInterface          $serverRequest,
        protected string                          $clientId,
        protected string                          $redirectUri,
        protected ?string                         $scope = null,
        protected ?string                         $state = null,
        iterable                                  $additionalData = []
    ) {
        if (empty($clientId)) {
            throw new UnsatisfiedParameterException(
                'Client ID cannot be empty'
            );
        }
        if (empty($redirectUri)) {
            throw new UnsatisfiedParameterException(
                'Redirect URI cannot be empty'
            );
        }
        $data = new FreezableCollections($additionalData);
        $data->set('client_id', $clientId);
        $data->set('redirect_uri', $redirectUri);
        $data->set('scope', $scope);
        $data->set('state', $state);
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function getServerRequest(): ServerRequestInterface
    {
        return $this->serverRequest;
    }

    /**
     * @inheritdoc
     */
    public function getRequestType(): RequestType
    {
        return $this->requestType;
    }

    /**
     * @inheritdoc
     *
     * @return GrantTypeAuthorizationInterface<GrantType, GrantTypeKey, GrantTypeValue>
     */
    public function getGrant(): GrantTypeAuthorizationInterface
    {
        return $this->grant;
    }

    /**
     * Get all data
     *
     * @return CollectionInterface<non-empty-string, mixed>
     */
    public function getData(): CollectionInterface
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     * @return ClientId
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @inheritdoc
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @inheritdoc
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @inheritdoc
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @inheritdoc
     */
    public function getResponseType(): string
    {
        return $this->getGrant()->getGrantTypeValue();
    }

    /**
     * @inheritdoc
     * Using standard HTTP Basic authentication for client authentication as recommended in RFC6749 section 2.3.1
     */
    public function prepareRequest(
        RequestInterface       $request,
        StreamFactoryInterface $streamFactory,
        UriFactoryInterface    $uriFactory
    ): RequestInterface {
        $parameters = [
            'client_id' => $this->getClientId(),
            // response_type is required and should be "token" for implicit grant
            // and "code" for authorization code grant
            'response_type' => $this->getResponseType(),
            'redirect_uri' => $this->getRedirectUri(),
        ];
        if ($this->getScope() !== null) {
            $parameters['scope'] = $this->getScope();
        }
        if ($this->getState() !== null) {
            $parameters['state'] = $this->getState();
        }
        $parameters = $this->grant->prepareClientRequestParameters(
            RequestType::AUTHORIZATION,
            $parameters,
            [],
        );
        $uri = $request->getUri();
        $query = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
        if ($this->getRequestType() === RequestType::TOKEN) {
            if (!$this instanceof CodeParameterInterface) {
                throw new UnsatisfiedParameterException(
                    'Code parameter is required for token requests'
                );
            }
            if (!$this instanceof ClientSecretParameterInterface) {
                throw new UnsatisfiedParameterException(
                    'Client secret parameter is required for token requests'
                );
            }
            $authorizationHeader = sprintf('Basic %s', base64_encode(
                sprintf('%s:%s', $this->getClientId(), $this->getClientSecret())
            ));
            $stream = $streamFactory->createStream($query);
            return $request
                ->withBody($stream)
                ->withHeader('Authorization', $authorizationHeader)
                ->withHeader('Content-Type', 'application/x-www-form-urlencoded');
        } else {
            return $request->withUri($uri->withQuery($query));
        }
    }
}
