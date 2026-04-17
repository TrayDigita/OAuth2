<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Grants;

use Psr\Http\Message\ServerRequestInterface;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Interfaces\Parameters\Requests\GrantTypeParameterInterface;
use TrayDigita\OAuth2\Interfaces\Requests\OAuth2RequestInterface;

/**
 * @template-covariant GrantType of non-empty-string
 * @template-covariant GrantTypeKey of non-empty-string
 * @template-covariant GrantTypeValue of non-empty-string
 * @template-extends GrantTypeParameterInterface<GrantType>
 */
interface GrantParametersInterface extends GrantTypeParameterInterface
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
     * Grant type key (query parameter key) to send
     *
     * @return GrantTypeKey key for query parameter : eg: grant_type
     */
    public function getGrantTypeKey() : string;

    /**
     * Grant type value (query parameter value) to send
     *
     * @return GrantTypeValue value for query parameter : eg: authorization_code
     */
    public function getGrantTypeValue(): string;

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
    public function getRequiredClientRequestParameters(RequestType $requestType): array;

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
    public function getOptionalClientRequestParameters(RequestType $requestType): array;

    /**
     * Whether the grant type requires strict parameter checking.
     * If true, the authorization server MUST reject the request if any parameters are missing or invalid.
     * If false, the authorization server MAY ignore any missing or invalid parameters and proceed with the request.
     *
     * @return bool
     */
    public function isStrictClientRequestParameter(): bool;

    /**
     * Whether the parameter is allowed for the grant type.
     *
     * @param RequestType $requestType The request type (authorization or token)
     * @param string $parameter The parameter name
     * @param mixed $value The parameter value
     * @return bool True if the parameter is allowed, false otherwise
     */
    public function isClientParameterSatisfied(RequestType $requestType, string $parameter, mixed $value): bool;

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
    public function prepareClientRequestParameters(
        RequestType $requestType,
        array $parameters,
        array $defaultParameters
    ): array;

    /**
     * check if grant type valid
     *
     * @param string $grantTypeRequest
     * @return bool
     * @phpstan-return ($grantTypeRequest is GrantTypeValue ? true : false)
     */
    public function isGrantTypeRequestValid(string $grantTypeRequest) : bool;

    /**
     * Get the request type from the server request.
     *
     * @param ServerRequestInterface $request The incoming server request.
     * @return RequestType The request type (authorization or token).
     * @throws \TrayDigita\OAuth2\Exceptions\UnsupportedOperationException
     * if the request type is not supported by the grant type.
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getServerRequestType(ServerRequestInterface $request) : RequestType;

    /**
     * Parse the incoming request,
     * convert into client request parameter.
     * This method can be used as
     * @param ServerRequestInterface $request
     * @return OAuth2RequestInterface<GrantType, GrantTypeKey, GrantTypeValue> parsed required response
     * @throws \TrayDigita\OAuth2\Exceptions\Response\OAuth2ResponseErrorException
     * if the request is invalid or any required parameter is missing
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function parseServerRequest(
        ServerRequestInterface $request
    ) : OAuth2RequestInterface;

    /**
     * Check if the incoming request is supported by the grant type.
     *
     * @param ServerRequestInterface $request The incoming server request to check.
     * @return bool True if the request is supported, false otherwise.
     */
    public function isSupportedRequest(ServerRequestInterface $request) : bool;

    /**
     * Find the grant type from the incoming request.
     *
     * @param ServerRequestInterface $request The incoming server request to check.
     * @return GrantType|null The grant type if found, null otherwise.
     */
    public function findGrantType(ServerRequestInterface $request) : ?string;

    /**
     * Return the string representation of the grant type, which is the same as the grant type parameter value.
     * The string representation of the grant type is used for logging, debugging,
     * and other purposes where a human-readable format is needed.
     * @return GrantType
     */
    public function __toString(): string;
}
