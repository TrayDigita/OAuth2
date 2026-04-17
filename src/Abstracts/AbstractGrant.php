<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Abstracts;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use TrayDigita\OAuth2\Enums\ErrorType;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Exceptions\AccessDeniedException;
use TrayDigita\OAuth2\Exceptions\OperationNotPermittedException;
use TrayDigita\OAuth2\Exceptions\Response\OAuth2ResponseErrorException;
use TrayDigita\OAuth2\Exceptions\StrictParameterException;
use TrayDigita\OAuth2\Exceptions\UnauthorizedException;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedGrantParameterException;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use TrayDigita\OAuth2\Exceptions\UnsupportedOperationException;
use TrayDigita\OAuth2\Interfaces\Exceptions\ExceptionInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantParametersInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantTypeAuthorizationInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantTypeTokenInterface;
use TrayDigita\OAuth2\Interfaces\Requests\OAuth2RequestInterface;
use TrayDigita\OAuth2\Servers\ErrorUris;
use function array_fill_keys;
use function array_key_exists;
use function array_unshift;
use function gettype;
use function in_array;
use function is_array;
use function is_string;
use function sprintf;

/**
 * Authorization Grant
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-1.3 Authorization Grant Types
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section
 *
 * @template GrantType of non-empty-string
 * @template GrantTypeKey of non-empty-string
 * @template GrantTypeValue of non-empty-string
 * @template-implements GrantParametersInterface<GrantType, GrantTypeKey, GrantTypeValue>
 */
abstract class AbstractGrant implements GrantParametersInterface
{
    /**
     * @var list<RequestType> $grantTypeSupportedRequestTypes
     * The request types that this grant type supports (e.g., authorization, token).
     */
    private array $grantTypeSupportedRequestTypes;

    /**
     * @inheritdoc
     * @return GrantType
     */
    abstract public function getGrantType(): string;

    /**
     * @inheritdoc
     * @return GrantTypeKey
     */
    abstract public function getGrantTypeKey(): string;

    /**
     * @inheritdoc
     * @return GrantTypeValue
     */
    public function getGrantTypeValue(): string
    {
        /**
         * @var GrantTypeValue $value
         */
        $value = $this->getGrantType();
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->getGrantType();
    }

    /**
     * @inheritdoc
     */
    public function getSupportedRequestTypes(): array
    {
        if (isset($this->grantTypeSupportedRequestTypes)) {
            return $this->grantTypeSupportedRequestTypes;
        }
        $types = [];
        if ($this instanceof GrantTypeAuthorizationInterface) {
            $types[] = RequestType::AUTHORIZATION;
        }
        if ($this instanceof GrantTypeTokenInterface) {
            $types[] = RequestType::TOKEN;
        }
        return $this->grantTypeSupportedRequestTypes = $types;
    }

    /**
     * @inheritdoc
     * @return list<non-empty-string>&list<'grant_type'>
     */
    public function getRequiredClientRequestParameters(RequestType $requestType): array
    {
        return [
            'grant_type'
        ];
    }

    /**
     * @inheritdoc
     */
    public function isStrictClientRequestParameter(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     * @phpstan-return ($grantTypeRequest is GrantTypeValue ? true : false)
     */
    public function isGrantTypeRequestValid(string $grantTypeRequest): bool
    {
        return $grantTypeRequest === $this->getGrantTypeValue();
    }

    /**
     * @inheritdoc
     */
    public function getOptionalClientRequestParameters(RequestType $requestType): array
    {
        return [
            'scope',
            'state'
        ];
    }

    /**
     * @inheritdoc
     */
    public function isClientParameterSatisfied(RequestType $requestType, string $parameter, mixed $value): bool
    {
        if ($parameter === $this->getGrantTypeKey()) {
            return is_string($value) && $this->isGrantTypeRequestValid($value);
        }
        if ($parameter === 'grant_type') { // client use grant type
            return $value === $this->getGrantType();
        }
        if (in_array(
            $parameter,
            $this->getRequiredClientRequestParameters($requestType),
            true
        )) {
            return is_string($value);
        }
        if (in_array(
            $parameter,
            $this->getOptionalClientRequestParameters($requestType),
            true
        )) {
            return is_string($value);
        }
        return !$this->isStrictClientRequestParameter() && is_string($value);
    }

    /**
     * Assert that the required parameters are present in the given parameters array.
     *
     * @param RequestType $requestType The request type (authorization or token)
     * @param array<string, mixed> $parameters The parameters to check.
     * @throws UnsatisfiedGrantParameterException if any required parameter is missing.
     * @throws UnsupportedOperationException if any required parameter is missing.
     * @throws StrictParameterException n if the parameters are strict and not satisfied
     * @must be called by the child class to check the required parameters and strict parameters.
     */
    protected function assertRequiredParameters(RequestType $requestType, array $parameters): void
    {
        if (!in_array($requestType, $this->getSupportedRequestTypes(), true)) {
            throw new UnsupportedOperationException(sprintf(
                'Unsupported request type: %s for %s',
                $requestType->getRequestMethod()->value,
                $this->getGrantType()
            ));
        }
        $requiredParameters = $this->getRequiredClientRequestParameters($requestType);
        if (!in_array('grant_type', $parameters)) {
            array_unshift($requiredParameters, 'grant_type');
        }
        $isStrict = $this->isStrictClientRequestParameter();
        foreach ($requiredParameters as $parameter) {
            if (!array_key_exists($parameter, $parameters)) {
                throw new UnsatisfiedGrantParameterException(sprintf('Missing required parameter: %s', $parameter));
            }
            unset($parameters[$parameter]); // skip for checking unexpected parameters
        }
        if (!isset($parameters['grant_type'])) {
            throw new UnsatisfiedGrantParameterException(
                'Missing required parameter: ' . 'grant_type'
            );
        }
        if ($parameters['grant_type'] !== $this->getGrantType()) {
            throw new UnsatisfiedGrantParameterException(sprintf(
                'Invalid grant type: %s, expected: %s',
                is_string($parameters['grant_type'])
                    ? $parameters['grant_type']
                    : gettype($parameters['grant_type']),
                $this->getGrantType()
            ));
        }
        if ($isStrict) {
            foreach ($parameters as $parameter => $value) {
                if (!$this->isClientParameterSatisfied($requestType, $parameter, $value)) {
                    throw new StrictParameterException(sprintf('Unexpected parameter: %s', $parameter));
                }
            }
        }
        if (isset($parameters['scope']) && !is_string($parameters['scope'])) {
            throw new UnsatisfiedGrantParameterException('The "scope" parameter must be a string.');
        }
        if (isset($parameters['state']) && !is_string($parameters['state'])) {
            throw new UnsatisfiedGrantParameterException('The "state" parameter must be a string.');
        }
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
     *      ...<TKey, TValue>
     *  }
     */
    public function prepareClientRequestParameters(
        RequestType $requestType,
        array $parameters,
        array $defaultParameters
    ): array {
        $defaultParameters['grant_type'] = $this->getGrantType();
        $parameters = [...$defaultParameters, ...$parameters];
        $this->assertRequiredParameters($requestType, $parameters);
        /**
         * @var array{
         *       "grant_type" : GrantType,
         *       "scope"?: non-empty-string,
         *       "state"?: non-empty-string,
         *       "redirect_uri"?: non-empty-string,
         *       ...<TKey, TValue>
         *   } $parameters
         */
        return $parameters;
    }

    /**
     * @inheritdoc
     */
    public function getServerRequestType(ServerRequestInterface $request) : RequestType
    {
        $method = $request->getMethod();
        foreach ($this->getSupportedRequestTypes() as $requestType) {
            if ($requestType->getRequestMethod()->value === $method) {
                return $requestType;
            }
        }
        throw new UnsupportedOperationException(sprintf(
            'Unsupported request method: %s for %s',
            $method,
            $this->getGrantType()
        ));
    }

    /**
     * @inheritdoc
     * @return array<GrantTypeKey, GrantTypeValue>&array{
     *     state?: string,
     *     scope?: string,
     *     ...<string, mixed>,
     * }
     * @throws OAuth2ResponseErrorException
     * @return OAuth2RequestInterface<GrantType, GrantTypeKey, GrantTypeValue>
     */
    public function parseServerRequest(
        ServerRequestInterface $request
    ): OAuth2RequestInterface {
        try {
            $requestType = $this->getServerRequestType($request);
        } catch (UnsupportedOperationException $e) {
            throw new OAuth2ResponseErrorException(
                ErrorType::INVALID_CLIENT,
                errorUri: ErrorUris::errorUriFor(sprintf('grant_type:%s', $this->getGrantType())),
                previous: $e
            );
        }
        if (!$this->isSupportedRequest($request)) {
             throw new OAuth2ResponseErrorException(
                 ErrorType::INVALID_CLIENT,
                 errorUri: ErrorUris::errorUriFor(sprintf('grant_type:%s', $this->getGrantType())),
             );
        }

        $method = $request->getMethod();
        if ($requestType->getRequestMethod()->value !== $method
            || !in_array($requestType, $this->getSupportedRequestTypes())
        ) {
            throw new OAuth2ResponseErrorException(
                ErrorType::INVALID_CLIENT,
                errorUri: ErrorUris::errorUriFor(sprintf('request_type:%s', $requestType->value)),
            );
        }

        // start here, we can be sure that the request method is valid and supported by the grant type
        $params = $method === 'POST'
            ? $request->getParsedBody()
            : $request->getQueryParams();
        $state = is_array($params)
            ? $params['state']
            : null;
        $errState = is_string($state) ? $state : null;
        $scope = is_array($params)
            ? $params['scope']
            : null;
        if (!is_array($params)) {
            throw new OAuth2ResponseErrorException(
                ErrorType::INVALID_REQUEST,
                errorUri: ErrorUris::errorUriFor(sprintf('invalid_parameters:%s', $requestType->value)),
                state: $errState
            );
        }
        if (isset($params['state'])) {
            if ($state !== null && !is_string($state)) {
                throw new OAuth2ResponseErrorException(
                    ErrorType::INVALID_STATE,
                    errorUri: ErrorUris::errorUriFor('parameter:state'),
                    state: $errState
                );
            }
        }
        if (isset($params['scope'])) {
            if ($scope !== null && !is_string($scope)) {
                throw new OAuth2ResponseErrorException(
                    ErrorType::INVALID_SCOPE,
                    errorUri: ErrorUris::errorUriFor('parameter:scope'),
                );
            }
        }
        /**
         * @var array{'grant_type': mixed, ...<string, mixed>} $parameters
         */
        $parameters = array_fill_keys($this->getRequiredClientRequestParameters($requestType), null);
        unset($parameters['grant_type']);
        $parameters[$this->getGrantTypeKey()] = null;
        foreach ($parameters as $key => $item) {
            if (!isset($params[$key])) {
                throw new OAuth2ResponseErrorException(
                    ErrorType::INVALID_REQUEST,
                    errorUri: ErrorUris::errorUriFor(sprintf('parameter:%s', $key)),
                    state: $errState,
                    message: sprintf('Parameter "%s" is required', $key)
                );
            }
            if ($key === $this->getGrantTypeKey()) {
                if (!$this->isGrantTypeRequestValid($params[$key])) {
                    throw new OAuth2ResponseErrorException(
                        ErrorType::INVALID_GRANT,
                        errorUri: ErrorUris::errorUriFor(sprintf('parameter:%s', $key)),
                        state: $errState,
                    );
                }
            }
            if (!$this->isClientParameterSatisfied($requestType, $key, $params[$key])) {
                throw new OAuth2ResponseErrorException(
                    ErrorType::INVALID_REQUEST,
                    errorUri: ErrorUris::errorUriFor(sprintf('parameter:%s', $key)),
                    state: $errState,
                    message: sprintf('Parameter "%s" is not valid', $key)
                );
            }
            $parameters[$key] = $params[$key];
        }

        foreach ($this->getOptionalClientRequestParameters($requestType) as $key) {
            if (isset($parameters[$key])) {
                continue; // skip
            }
            if (!isset($params[$key])) {
                $parameters[$key] = null; // setup
                continue;
            }
            if (!$this->isClientParameterSatisfied($requestType, $key, $params[$key])) {
                throw new OAuth2ResponseErrorException(
                    ErrorType::INVALID_REQUEST,
                    errorUri: ErrorUris::errorUriFor(sprintf('parameter:%s', $key)),
                    state: $errState,
                    message: sprintf('Parameter "%s" is not valid', $key)
                );
            }
            $parameters[$key] = $params[$key];
        }
        try {
            /**
             * @var array<GrantTypeKey, GrantTypeValue>&array{
             *      state?: string,
             *      scope?: string,
             *      ...<string, mixed>,
             *  } $parameters
             */
            return $this->convertOAuth2Request(
                $request,
                $requestType,
                $parameters
            );
        } catch (Throwable $exception) {
            if ($exception instanceof OAuth2ResponseErrorException) {
                throw $exception;
            }
            /**
             * @var array<class-string<ExceptionInterface>, ErrorType> $types
             */
            $types = [
                UnsatisfiedParameterException::class => ErrorType::INVALID_REQUEST,
                OperationNotPermittedException::class => ErrorType::UNAUTHORIZED_CLIENT,
                StrictParameterException::class => ErrorType::INVALID_REQUEST,
                UnauthorizedException::class => ErrorType::UNAUTHORIZED_CLIENT,
                AccessDeniedException::class => ErrorType::ACCESS_DENIED,
                ExceptionInterface::class => ErrorType::UNAUTHORIZED_CLIENT, // last sort
            ];
            foreach ($types as $class => $type) {
                if ($exception instanceof $class) {
                    throw new OAuth2ResponseErrorException(
                        $type,
                        errorUri: ErrorUris::errorUriFor(sprintf('request_type:%s', $requestType->value)),
                        state: $state,
                        message: $exception->getMessage(),
                        previous: $exception
                    );
                }
            }
            throw new OAuth2ResponseErrorException(
                ErrorType::SERVER_ERROR,
                errorUri: ErrorUris::errorUriFor(sprintf('request_type:%s', $requestType->value)),
                state: $state,
                previous: $exception
            );
        }
    }

    /**
     * Create an OAuth2RequestInterface instance from the given parameters.
     *
     * @param ServerRequestInterface $request The original server request.
     * @param RequestType $requestType The type of the request (authorization or token).
     * @param array<GrantTypeKey, GrantTypeValue>&array{
     *      state?: string,
     *      scope?: string,
     *      ...<string, mixed>,
     *  } $parameters
     * @return OAuth2RequestInterface<GrantType, GrantTypeKey, GrantTypeValue>
     *     The created OAuth2RequestInterface instance.
     * @throws OAuth2ResponseErrorException
     * If the parameters are invalid or cannot be used to create a valid request,
     * an OAuth2ResponseErrorException should be thrown with an appropriate error type and message.
     * @throws Throwable
     * @see self::parseServerRequest()
     */
    abstract protected function convertOAuth2Request(
        ServerRequestInterface $request,
        RequestType $requestType,
        array $parameters
    ): OAuth2RequestInterface;

    /**
     * @inheritdoc
     */
    public function findGrantType(ServerRequestInterface $request): ?string
    {
        $method = $request->getMethod();
        // method only support GET or POST
        if ($method !== 'GET' && $method !== 'POST') {
            return null;
        }

        $params = $method === 'POST'
            ? $request->getParsedBody()
            : $request->getQueryParams();
        if (!is_array($params)) {
            return null;
        }
        $grant = $params[$this->getGrantTypeKey()] ?? null;
        if (!is_string($grant)) {
            return null;
        }
        /**
         * @var GrantType $grant
         */
        return $this->isGrantTypeRequestValid($grant) ? $grant : null;
    }

    /**
     * @inheritdoc
     */
    public function isSupportedRequest(ServerRequestInterface $request) : bool
    {
        $method = $request->getMethod();
        // method only support GET or POST
        if ($method !== 'GET' && $method !== 'POST') {
            return false;
        }
        $satisfy = false;
        foreach ($this->getSupportedRequestTypes() as $requestType) {
            if ($requestType->getRequestMethod()->value === $method) {
                $satisfy = true;
                break;
            }
        }
        if (!$satisfy) {
            return false;
        }
        $params = $method === 'POST'
            ? $request->getParsedBody()
            : $request->getQueryParams();
        if (!is_array($params)) {
            return false;
        }
        $grant = $params[$this->getGrantTypeKey()] ?? null;
        if (!is_string($grant)) {
            return false;
        }
        return $this->isGrantTypeRequestValid($grant);
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->getGrantType();
    }
}
