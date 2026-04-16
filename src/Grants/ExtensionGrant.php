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
 * @template-extends AbstractGrant<GrantUri>
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
     * Create new extension grant instance
     * @param GrantUri $grantUri
     * @param list<non-empty-string> $requiredParameters
     * @param list<non-empty-string> $optionalParameters
     * @param ?callable(RequestType, array<string, mixed>, array<string, mixed>): array<string, mixed> $callback
     * @param bool $strict
     * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.5 Extension Grant (RFC 6749, §4.5)
     */
    public function __construct(
        string    $grantUri,
        array     $requiredParameters = ['grant_type'], // default accept
        array     $optionalParameters = ['scope', 'state'], // default accept
        bool      $strict = false,
        ?callable $callback = null
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
        $this->parameterPreparation = $callback;
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
    public function getRequiredParameters(RequestType $requestType): array
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
    public function getOptionalParameters(RequestType $requestType): array
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
    public function prepareParameters(RequestType $requestType, array $parameters, array $defaultParameters): array
    {
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
}
