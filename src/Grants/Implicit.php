<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Grants;

use Psr\Http\Message\ServerRequestInterface;
use TrayDigita\OAuth2\Abstracts\AbstractGrant;
use TrayDigita\OAuth2\Clients\Requests\ImplicitRequest;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Exceptions\StrictParameterException;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedGrantParameterException;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ImplicitGrantInterface;
use function is_string;

/**
 * ## The implicit grant type is used to obtain access tokens
 * (it does not support the issuance of refresh tokens) and is optimized for public
 * clients known to operate a particular redirection URI.
 * These clients are typically implemented in a browser using a scripting language
 * such as JavaScript.
 *
 * ### Flow:
 * <code>
 * +----------+
 * | Resource |
 * |  Owner   |
 * |          |
 * +----------+
 * ^
 * |
 * (B)
 * +----|-----+          Client Identifier     +---------------+
 * |         -+----(A)-- & Redirection URI --->|               |
 * |  User-   |                                | Authorization |
 * |  Agent  -|----(B)-- User authenticates -->|     Server    |
 * |          |                                |               |
 * |          |<---(C)--- Redirection URI ----<|               |
 * |          |          with Access Token     +---------------+
 * |          |            in Fragment
 * |          |                                +---------------+
 * |          |----(D)--- Redirection URI ---->|   Web-Hosted  |
 * |          |          without Fragment      |     Client    |
 * |          |                                |    Resource   |
 * |     (F)  |<---(E)------- Script ---------<|               |
 * |          |                                +---------------+
 * +-|--------+
 * |    |
 * (A)  (G) Access Token
 * |    |
 * ^    v
 * +---------+
 * |         |
 * |  Client |
 * |         |
 * +---------+
 *
 * Note: The lines illustrating steps (A) and (B) are broken into two
 * parts as they pass through the user-agent.
 *
 * Implicit Grant Flow
 * </code>
 *
 * ### Example Request:
 * <code>
 * GET /authorize?response_type=token&client_id=s6BhdRkqt3&state=xyz
 * &redirect_uri=https%3A%2F%2Fclient%2Eexample%2Ecom%2Fcb
 * HTTP/1.1
 * Host: server.example.com
 * </code>
 *
 * ### Example Response:
 * <code>
 * HTTP/1.1 302 Found
 * Location: http://client.example.com/cb#access_token=2YotnFZFEjr1zCsicMWpAA
 * &state=xyz&token_type=example&expires_in=3600
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2 Implicit Grant (RFC 6749, §4.2)
 *
 * @template-covariant ClientId of non-empty-string
 * @template-extends AbstractGrant<"implicit", "response_type", "token">
 * @template-implements ImplicitGrantInterface<ClientId>
 *
 * @note
 * Implicit does not require `grant_type` and replaced with `response_type`,
 * so it will remove `grant_type` from `prepareParameters()`
 */
class Implicit extends AbstractGrant implements ImplicitGrantInterface
{
    /**
     * @inheritdoc
     * @return "implicit"
     */
    public function getGrantType(): string
    {
        return self::TYPE;
    }

    /**
     * @inheritdoc
     * @return list<non-empty-string>&list<'client_id','response_type'>
     * /
     */
    public function getRequiredClientRequestParameters(RequestType $requestType): array
    {
        /**
         * Required grant_type parameter with the value "token".
         * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2.1
         */
        return [
            'client_id',
            'response_type'
        ];
    }

    /**
     * Assert that the required parameters are present in the given parameters array.
     *
     * @param array<string, mixed> $parameters The parameters to check.
     * @throws UnsatisfiedGrantParameterException if any required parameter is missing.
     */
    protected function assertRequiredParameters(RequestType $requestType, array $parameters): void
    {
        parent::assertRequiredParameters($requestType, $parameters);
        if (!isset($parameters['client_id'])
            || !is_string($parameters['client_id'])
            || $parameters['client_id'] === ''
        ) {
            throw new UnsatisfiedGrantParameterException(
                'client_id is required and must be a non-empty string'
            );
        }
        if (!isset($parameters['response_type'])
            || $parameters['response_type'] !== 'token'
        ) {
            throw new StrictParameterException(
                'response_type is required and must be "token"'
            );
        }
    }

    /**
     * @Inheritdoc
     * @return list<non-empty-string>&list<'scope','state'>
     */
    public function getOptionalClientRequestParameters(RequestType $requestType): array
    {
        /**
         * Optional scope parameter, which is a space-delimited list of scopes that the client is requesting.
         * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.4.2
         */
        return [
            'scope',
            'state'
        ];
    }

    /**
     * @inheritdoc
     *
     * @template TKey of string
     * @template TValue
     * @param RequestType $requestType The request type (authorization or token)
     * @param array<TKey, TValue> $parameters
     * @param array<TKey, TValue> $defaultParameters
     * @return array{
     *      "scope"?: non-empty-string,
     *      "state"?: non-empty-string,
     *      "redirect_uri"?: non-empty-string,
     *      "response_type": "token",
     *      "client_id": ClientId,
     *      ...<TKey, TValue>
     *  }
     */
    public function prepareClientRequestParameters(
        RequestType $requestType,
        array       $parameters,
        array       $defaultParameters
    ): array {
        $parameters['response_type'] = 'token';
        $parameters['grant_type'] = $this->getGrantType();
        $parameters = parent::prepareClientRequestParameters($requestType, $parameters, $defaultParameters);

        // replace
        $parameters['response_type'] = 'token';
        if (isset($parameters['grant_type'])) {
            unset($parameters['grant_type']);
        }
        /**
         * @var array{
         *     "scope"?: non-empty-string,
         *     "state"?: non-empty-string,
         *     "redirect_uri"?: non-empty-string,
         *     "response_type": "token",
         *     "client_id": ClientId,
         *     ...<TKey, TValue>
         * } $parameters
         */
        return $parameters;
    }

    /**
     * @inheritdoc
     * @return "response_type"
     */
    public function getGrantTypeKey(): string
    {
        return 'response_type';
    }

    /**
     * @inheritdoc
     * @return "token"
     */
    public function getGrantTypeValue(): string
    {
        return 'token';
    }

    /**
     * @inheritdoc
     * @return ImplicitRequest<ClientId>
     * @throws StrictParameterException if the request type is not authorization request
     * @throws UnsatisfiedGrantParameterException if any required parameter is missing or invalid
     */
    protected function convertOAuth2Request(
        ServerRequestInterface $request,
        RequestType            $requestType,
        array                  $parameters
    ): ImplicitRequest {
        if ($requestType !== RequestType::AUTHORIZATION) {
            throw new StrictParameterException(
                'Implicit grant type only supports authorization request'
            );
        }
        $clientId = $parameters['client_id'] ?? null;
        if (!is_string($clientId) || $clientId === '') {
            throw new UnsatisfiedGrantParameterException(
                'client_id is required and must be a non-empty string'
            );
        }
        /** @noinspection DuplicatedCode */
        $redirectUri = $parameters['redirect_uri'] ?? null;
        if (isset($redirectUri) && (!is_string($redirectUri) || $redirectUri === '')) {
            throw new UnsatisfiedGrantParameterException(
                'redirect_uri must be a non-empty string if provided'
            );
        }
        $scope = $parameters['scope'] ?? null;
        if (isset($scope) && !is_string($scope)) {
            throw new UnsatisfiedGrantParameterException(
                'scope must be a string if provided'
            );
        }
        $state = $parameters['state'] ?? null;
        if (isset($state) && !is_string($state)) {
            throw new UnsatisfiedGrantParameterException(
                'state must be a string if provided'
            );
        }
        return new ImplicitRequest(
            $this,
            $requestType,
            $request,
            $clientId,
            $redirectUri,
            $scope,
            $state,
            $parameters
        );
    }
}
