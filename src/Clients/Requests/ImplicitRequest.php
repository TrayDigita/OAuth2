<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Clients\Requests;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use TrayDigita\OAuth2\Interfaces\Requests\AuthorizationRequestInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ImplicitGrantInterface;

/**
 * Class ImplicitRequest
 *
 * This class represents an implicit grant request in the OAuth2 authorization flow.
 * It implements the AuthorizationOwnerResourceBasedOnCredentialsRequestInterface, which means
 * it is used to request an access token on behalf of a resource owner using their credentials.
 *
 * The constructor validates the required parameters and throws an UnsatisfiedParameterException
 * if any of the required parameters are empty.
 * @template ClientId of non-empty-string
 * @template-implements AuthorizationRequestInterface<"token">
 */
class ImplicitRequest implements AuthorizationRequestInterface
{
    /**
     * @param ImplicitGrantInterface<ClientId> $grant
     * @param ClientId $clientId
     * @param string $redirectUri
     * @param string|null $scope
     * @param string|null $state
     */
    public function __construct(
        public readonly ImplicitGrantInterface $grant,
        private readonly string $clientId,
        private readonly string $redirectUri,
        private readonly ?string $scope = null,
        private readonly ?string $state = null,
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
        return $this->grant->getGrantTypeValue();
    }

    /**
     * @inheritdoc
     */
    public function prepareRequest(
        RequestInterface $request,
        StreamFactoryInterface $streamFactory,
        UriFactoryInterface $uriFactory
    ): RequestInterface {
        $parameters = [
            'client_id' => $this->getClientId(),
            'response_type' => 'token', // response_type is required and should be "token" for implicit grant
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
        $uri = $uri->withQuery($query);
        return $request->withUri($uri);
    }
}
