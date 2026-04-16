<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Grants;

use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Interfaces\Parameters\Requests\GrantTypeParameterInterface;

/**
 * @template-covariant GrantType of non-empty-string
 * @template-extends GrantTypeParameterInterface<GrantType>
 */
interface GrantRequestParametersInterface extends GrantTypeParameterInterface
{
    /**
     * The grant name for identifier
     *
     * @return GrantType
     */
    public function getName() : string;

    /**
     * The grant type parameter is required in the token request and must be set to the value of the
     * "grant_type" parameter as defined in the authorization grant type being used.
     *
     * @return GrantType
     */
    public function getGrantType() : string;

    /**
     * Supported request types for the grant type, which can be either "authorization" or "token".
     * The supported request types indicate whether the grant type can be used in the authorization endpoint,
     * the token endpoint, or both.
     * @return list<RequestType>
     */
    public function getSupportedRequestTypes(): array;

    /**
     * Required parameters for the grant type, including the "grant_type" parameter.
     * The "grant_type" parameter is required in the token request
     * and must be set to the value of the "grant_type" parameter as defined in
     * the authorization grant type being used.
     *
     * @param RequestType $requestType The request type (authorization or token)
     *
     * @return list<non-empty-string>&list<'grant_type'>
     */
    public function getRequiredParameters(RequestType $requestType): array;

    /**
     * Optional parameters for the grant type, excluding the "grant_type" parameter.
     * The "grant_type" parameter is required in the token request
     * and must be set to the value of the "grant_type" parameter
     * as defined in the authorization grant type being used.
     *
     * @param RequestType $requestType The request type (authorization or token)
     *
     * @return list<non-empty-string>
     */
    public function getOptionalParameters(RequestType $requestType): array;

    /**
     * Whether the grant type requires strict parameter checking.
     * If true, the authorization server MUST reject the request if any parameters are missing or invalid.
     * If false, the authorization server MAY ignore any missing or invalid parameters and proceed with the request.
     *
     * @return bool
     */
    public function isStrictParameter(): bool;

    /**
     * Whether the parameter is allowed for the grant type.
     *
     * @param RequestType $requestType The request type (authorization or token)
     * @param string $parameter The parameter name
     * @param mixed $value The parameter value
     * @return bool True if the parameter is allowed, false otherwise
     */
    public function isAllowedParameter(RequestType $requestType, string $parameter, mixed $value): bool;

    /**
     * Prepare parameter body
     *
     * @template TKey of string
     * @template TValue
     * @param RequestType $requestType The request type (authorization or token)
     * @param array<TKey, TValue> $parameters
     * @param array<TKey, TValue> $defaultParameters
     * @return array{
     *     "scope"?: non-empty-string,
     *     "state"?: non-empty-string,
     *     "redirect_uri"?: non-empty-string,
     *     ...<TKey, TValue>
     * }
     * @throws \TrayDigita\OAuth2\Exceptions\UnsatisfiedGrantParameterException
     *      if any required parameter is missing or invalid
     * @throws \TrayDigita\OAuth2\Exceptions\StrictParameterException
     *      if the parameters are strict and not satisfied
     * @throws \TrayDigita\OAuth2\Exceptions\OperationNotPermittedException
     *      if the grant type is not allowed to prepare parameters
     * @throws \TrayDigita\OAuth2\Exceptions\UnsupportedOperationException
     *      if the grant type is not allowed to prepare parameters
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function prepareParameters(
        RequestType $requestType,
        array $parameters,
        array $defaultParameters
    ): array;

    /**
     * Return the string representation of the grant type, which is the same as the grant type parameter value.
     * The string representation of the grant type is used for logging, debugging,
     * and other purposes where a human-readable format is needed.
     * @return GrantType
     */
    public function __toString(): string;
}
