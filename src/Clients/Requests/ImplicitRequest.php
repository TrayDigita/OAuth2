<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Clients\Requests;

use Psr\Http\Message\ServerRequestInterface;
use TrayDigita\OAuth2\Abstracts\AbstractAuthorizationRequest;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ImplicitGrantInterface;

/**
 * This class represents an implicit grant request in the OAuth2 authorization flow.
 * It implements the AuthorizationOwnerResourceBasedOnCredentialsRequestInterface, which means
 * it is used to request an access token on behalf of a resource owner using their credentials.
 *
 * The constructor validates the required parameters and throws an UnsatisfiedParameterException
 * if any of the required parameters are empty.
 * @template ClientId of non-empty-string
 * @template-extends AbstractAuthorizationRequest<ClientId, "implicit", "response_type", "token">
 */
class ImplicitRequest extends AbstractAuthorizationRequest
{
    /**
     * ImplicitRequest constructor.
     * @param ServerRequestInterface $serverRequest
     * @param RequestType $requestType
     * @param ImplicitGrantInterface<ClientId> $grant
     * @param ClientId $clientId
     * @param string $redirectUri
     * @param string|null $scope
     * @param string|null $state
     * @param iterable<non-empty-string, mixed> $additionalData
     */
    public function __construct(
        ImplicitGrantInterface $grant,
        RequestType            $requestType,
        ServerRequestInterface $serverRequest,
        string                 $clientId,
        string                 $redirectUri,
        ?string                $scope = null,
        ?string                $state = null,
        iterable               $additionalData = []
    ) {
        parent::__construct(
            $grant,
            $requestType,
            $serverRequest,
            $clientId,
            $redirectUri,
            $scope,
            $state,
            $additionalData
        );
        $this->data->freeze(); // freeze the data collection to prevent further modifications
    }
}
