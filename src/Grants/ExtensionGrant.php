<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Grants;

use TrayDigita\OAuth2\Abstracts\AbstractGrant;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedGrantParameterException;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ExtensionsGrantInterface;
use function array_values;
use function gettype;
use function in_array;
use function is_array;
use function is_string;
use function sprintf;

/**
 * ## The client uses an extension grant type
 * by specifying the grant type using an absolute URI (defined by the authorization server) as the
 * value of the "grant_type" parameter of the token endpoint, and by
 * adding any additional parameters necessary.
 *
 * ### Example Request:
 * <code>
 * POST /token HTTP/1.1
 * Host: server.example.com
 * Content-Type: application/x-www-form-urlencoded
 *
 * grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Asaml2-
 * bearer&assertion=PEFzc2VydGlvbiBJc3N1ZUluc3RhbnQ9IjIwMTEtMDU
 * [...omitted for brevity...]aG5TdGF0ZW1lbnQ-PC9Bc3NlcnRpb24-
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.5
 *
 * @template GrantUri of non-empty-string
 * @template GrantTypeValue of non-empty-string
 * @template-extends AbstractGrant<GrantUri, "grant_type", non-empty-string>
 * @template-implements ExtensionsGrantInterface<GrantUri>
 */
class ExtensionGrant extends AbstractGrant implements ExtensionsGrantInterface
{
    /**
     * @var GrantUri $type
     */
    protected string $type;

    /**
     * @var list<non-empty-string> $requiredParameters
     */
    protected array $requiredParameters;

    /**
     * @var list<non-empty-string> $optionalParameters
     */
    protected array $optionalParameters;

    /**
     * @var bool $strict
     */
    protected bool $strict;

    /**
     * @var callable(RequestType, array<string, mixed>, array<string, mixed>): array<string, mixed>|null
     */
    protected $parameterPreparation;

    /**
     * @var callable(string): bool|null $grantTypeCallback
     */
    protected $grantTypeCallback;

    /**
     * @var "grant_type" $grantTypeKey
     */
    protected string $grantTypeKey = 'grant_type';

    /**
     * @var GrantTypeValue $grantTypeValue
     */
    protected string $grantTypeValue;

    /**
     * Create new extension grant instance
     * @param GrantUri $grantUri
     * The key used to identify the grant type in the request parameters, default is "grant_type"
     * @param string|null $grantTypeValue
     * The expected value of the grant type parameter, if null, it will be the same
     * @param list<non-empty-string> $requiredParameters
     * @param list<non-empty-string> $optionalParameters
     * @param ?callable(RequestType, array<string, mixed>, array<string, mixed>): array<string, mixed> $prepareCallback
     * @param ?callable(string): bool $grantTypeCallback
     * @param bool $strict
     * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.5 Extension Grant (RFC 6749, §4.5)
     */
    public function __construct(
        string    $grantUri,
        array     $requiredParameters = ['grant_type'], // default accept
        array     $optionalParameters = ['scope', 'state'], // default accept
        bool      $strict = false,
        ?string   $grantTypeValue = null,
        ?callable $prepareCallback = null,
        ?callable $grantTypeCallback = null
    ) {
        $this->type = $grantUri;
        foreach ($requiredParameters as $parameter) {
            if (!is_string($parameter)) {
                throw new UnsatisfiedGrantParameterException(
                    sprintf(
                        'Required parameter must be a string, %s given',
                        gettype($parameter)
                    )
                );
            }
            if ($parameter === '') {
                throw new UnsatisfiedGrantParameterException(
                    'Required parameter must be a non-empty string'
                );
            }
        }
        foreach ($optionalParameters as $parameter) {
            if (!is_string($parameter)) {
                throw new UnsatisfiedGrantParameterException(
                    sprintf(
                        'Optional parameter must be a string, %s given',
                        gettype($parameter)
                    )
                );
            }
            if ($parameter === '') {
                throw new UnsatisfiedGrantParameterException(
                    'Optional parameter must be a non-empty string'
                );
            }
        }
        if (!in_array('grant_type', $requiredParameters)) {
            $requiredParameters[] = 'grant_type'; // always
        }
        $this->requiredParameters = array_values($requiredParameters);
        $this->optionalParameters = array_values($optionalParameters);
        $this->strict = $strict;
        $this->parameterPreparation = $prepareCallback;
        $this->grantTypeCallback = $grantTypeCallback;
        /**
         * @var GrantTypeValue $grantTypeValue
         */
        $grantTypeValue = $grantTypeValue ?? $grantUri;
        $this->grantTypeValue = $grantTypeValue;
    }

    /**
     * @inheritdoc
     */
    public function getGrantTypeKey(): string
    {
        return $this->grantTypeKey;
    }

    /**
     * @inheritdoc
     * @return GrantUri
     */
    public function getGrantType(): string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     * @return list<non-empty-string>&list<'grant_type'>
     */
    public function getRequiredClientRequestParameters(RequestType $requestType): array
    {
        /**
         * @var list<non-empty-string>&list<'grant_type'> $list
         */
        $list = $this->requiredParameters;
        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getOptionalClientRequestParameters(RequestType $requestType): array
    {
        /**
         * @var list<non-empty-string> $list
         */
        $list = $this->optionalParameters;
        return $list;
    }

    /**
     * @inheritdoc
     */
    public function prepareClientRequestParameters(
        RequestType $requestType,
        array $parameters,
        array $defaultParameters
    ): array {
        $defaultParameters['grant_type'] = $this->getGrantType();
        $this->assertRequiredParameters($requestType, [...$defaultParameters, ...$parameters]);
        if (isset($this->parameterPreparation)) {
            $result = ($this->parameterPreparation)($requestType, $parameters, $defaultParameters);
            if (is_array($result)) {
                $result['grant_type'] = $this->getGrantType();
                return $result;
            }
        }
        // fallback to default preparation
        $result = [...$defaultParameters, ...$parameters];
        $result['grant_type'] = $this->getGrantType();
        return $result;
    }

    /**
     * @inheritdoc
     * @return GrantTypeValue
     */
    public function getGrantTypeValue(): string
    {
        return $this->grantTypeValue;
    }

    /**
     * @inheritdoc
     * @return bool
     * @phpstan-return ($grantTypeRequest is GrantTypeValue ? true : false)
     * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.5 Extension Grant (RFC 6749, §4.5)
     */
    public function isGrantTypeRequestValid(string $grantTypeRequest): bool
    {
        return isset($this->grantTypeCallback)
            ? ($this->grantTypeCallback)($grantTypeRequest) === true
            : $grantTypeRequest === $this->getGrantTypeValue();
    }
}
