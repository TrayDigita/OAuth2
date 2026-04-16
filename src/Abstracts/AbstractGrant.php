<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Abstracts;

use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Exceptions\StrictParameterException;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedGrantParameterException;
use TrayDigita\OAuth2\Exceptions\UnsupportedOperationException;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantRequestParametersInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantTypeAuthorizationRequest;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantTypeTokenRequest;
use function array_key_exists;
use function array_unshift;
use function gettype;
use function in_array;
use function is_string;
use function sprintf;

/**
 * Authorization Grant
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-1.3 Authorization Grant Types
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section
 *
 * @template GrantType of non-empty-string
 * @template-implements GrantRequestParametersInterface<GrantType>
 */
abstract class AbstractGrant implements GrantRequestParametersInterface
{
    /**
     * @var list<RequestType> $grantTypeSupportedRequestTypes
     * The request types that this grant type supports (e.g., authorization, token).
     */
    private array $grantTypeSupportedRequestTypes;

    /**
     * @inheritdoc
     */
    abstract public function getGrantType(): string;

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
        if ($this instanceof GrantTypeAuthorizationRequest) {
            $types[] = RequestType::AUTHORIZATION;
        }
        if ($this instanceof GrantTypeTokenRequest) {
            $types[] = RequestType::TOKEN;
        }
        return $this->grantTypeSupportedRequestTypes = $types;
    }

    /**
     * @inheritdoc
     * @return list<non-empty-string>&list<'grant_type'>
     */
    public function getRequiredParameters(RequestType $requestType): array
    {
        return [
            'grant_type'
        ];
    }

    /**
     * @inheritdoc
     */
    public function isStrictParameter(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getOptionalParameters(RequestType $requestType): array
    {
        return [
            'scope',
            'state'
        ];
    }

    /**
     * @inheritdoc
     */
    public function isAllowedParameter(RequestType $requestType, string $parameter, mixed $value): bool
    {
        return $parameter === 'grant_type' || in_array(
            $parameter,
            $this->getRequiredParameters($requestType),
            true
        ) || in_array(
            $parameter,
            $this->getOptionalParameters($requestType),
            true
        );
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
        $requiredParameters = $this->getRequiredParameters($requestType);
        if (!in_array('grant_type', $parameters)) {
            array_unshift($requiredParameters, 'grant_type');
        }
        $isStrict = $this->isStrictParameter();
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
                if (!$this->isAllowedParameter($requestType, $parameter, $value)) {
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
    public function prepareParameters(RequestType $requestType, array $parameters, array $defaultParameters): array
    {
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
    public function __toString(): string
    {
        return $this->getGrantType();
    }
}
