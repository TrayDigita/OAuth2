<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Clients\Requests;

use Psr\Http\Message\ServerRequestInterface;
use TrayDigita\OAuth2\Abstracts\AbstractAuthorizationRequest;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use TrayDigita\OAuth2\Interfaces\Parameters\CodeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Requests\ClientSecretParameterInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\AuthorizationCodeGrantInterface;

/**
 * @template ClientId of non-empty-string
 * @template-extends AbstractAuthorizationRequest<ClientId, "authorization_code", "response_type", "code">
 */
class AuthorizationCodeRequest extends AbstractAuthorizationRequest implements
    CodeParameterInterface,
    ClientSecretParameterInterface
{
    /**
     * @var string|null
     */
    protected ?string $clientSecret;

    /**
     * @var string|null
     */
    protected ?string $code;

    /**
     * AuthorizationCodeRequest constructor.
     *
     * @phpstan-type ClientSecret ($requestType is RequestType::AuthorizationCode ? non-empty-string : null)
     * @phpstan-type ClientCode ($requestType is RequestType::AuthorizationCode ? non-empty-string : null)
     * @param AuthorizationCodeGrantInterface<ClientId> $grant
     * @param RequestType $requestType
     * @param ServerRequestInterface $serverRequest
     * @param ClientId $clientId
     * @param string $redirectUri
     * @param ClientSecret $clientSecret
     * @param ClientCode $code
     * @param string|null $scope
     * @param string|null $state
     * @param iterable<non-empty-string, mixed> $additionalData
     * @throws UnsatisfiedParameterException if any of the required parameters are empty
     */
    public function __construct(
        AuthorizationCodeGrantInterface $grant,
        RequestType            $requestType,
        ServerRequestInterface $serverRequest,
        string $clientId,
        string $redirectUri,
        ?string $clientSecret = null,
        ?string $code = null,
        ?string $scope = null,
        ?string $state = null,
        iterable $additionalData = []
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
        if ($requestType === RequestType::TOKEN) {
            if (!$clientSecret) {
                throw new UnsatisfiedParameterException(
                    'Client secret is required for token requests'
                );
            }
            if (!$code) {
                throw new UnsatisfiedParameterException(
                    'Code is required for token requests'
                );
            }
            $this->code = $code;
            $this->clientSecret = $clientSecret;
            $this->data->set('client_secret', $clientSecret);
            $this->data->set('code', $code);
        } else {
            $this->clientSecret = null;
            $this->code = null;
        }
        $this->data->freeze(); // freeze the data collection to prevent further modifications
    }

    /**
     * @inheritdoc
     * @throws UnsatisfiedParameterException if the code parameter is not set
     */
    public function getCode(): string
    {
        if (empty($this->code)) {
            throw new UnsatisfiedParameterException(
                'Code parameter is not set'
            );
        }
        return $this->code;
    }

    /**
     * @inheritdoc
     * @throws UnsatisfiedParameterException if the client secret parameter is not set
     */
    public function getClientSecret(): string
    {
        if (empty($this->clientSecret)) {
            throw new UnsatisfiedParameterException(
                'Client secret parameter is not set'
            );
        }
        return $this->clientSecret;
    }
}
