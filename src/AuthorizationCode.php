<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2;

use TrayDigita\OAuth2\Collections\FreezableCollections;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use TrayDigita\OAuth2\Interfaces\AuthorizationCodeInterface;
use TrayDigita\OAuth2\Interfaces\Collections\FreezableCollectionInterface;
use TrayDigita\OAuth2\Traits\BaseStorageResponseTrait;
use function array_filter;
use function in_array;
use function is_string;
use function trim;
use const ARRAY_FILTER_USE_BOTH;

/**
 * @template TState of string
 * @template TScope of string
 * @template-implements AuthorizationCodeInterface<?TState, ?TScope>
 */
class AuthorizationCode implements AuthorizationCodeInterface
{
    /**
     * @use BaseStorageResponseTrait<TState, TScope>
     */
    use BaseStorageResponseTrait {
        __construct as private baseConstruct;
    }

    /**
     * @var FreezableCollectionInterface<non-empty-string, mixed> Access token data
     */
    protected FreezableCollectionInterface $data;

    /**
     * @var non-empty-string Authorization code string
     */
    protected string $code;

    /**
     * AccessToken constructor.
     *
     * @param iterable<non-empty-string, mixed> $data Additional data associated with the access token
     * @throws UnsatisfiedParameterException
     *   if the required parameters for the access token are not satisfied
     * (e.g., missing token, invalid expiration time)
     * @throws \TrayDigita\OAuth2\Exceptions\Clients\InvalidAccessTokenException
     *  if the access token is invalid (e.g., missing token, invalid expiration time).
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function __construct(iterable $data = [])
    {
        /**
         * @var FreezableCollectionInterface<non-empty-string, mixed> $data
         */
        $data = new FreezableCollections($data);

        if (!isset($data['code']) || !is_string($data['code']) || trim($data['code']) === '') {
            throw new UnsatisfiedParameterException(
                'The "code" parameter is required and must be a non-empty string.'
            );
        }
        /**
         * @var non-empty-string $code
         */
        $code = trim($data['code']);
        $this->code = $code;
        $this->baseConstruct($data);
        $this->data->freeze();
    }

    /**
     * Get the authorization code string.
     *
     * @return non-empty-string The authorization code string.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get additional parameters response
     * This can be useful for client side verification
     *
     * @return array{
     *      "code": non-empty-string,
     *      "timestamp": positive-int,
     *      "expires_in"?: int|null, // recommended depending lifetime
     *      "state"?: TState, // state is required if client sending state
     *      "scope"?: TScope,
     *      ...<string, mixed>
     * }
     * @see self::getData()
     */
    public function toArray(): array
    {
        $array = [
            ...$this->getAdditionalData(),
            'code' => $this->getCode(),
            'timestamp' => $this->timestamp,
            'expires_in' => $this->getExpiresIn(),
            'scope' => $this->getScope(),
            'state' => $this->getState(),
        ];
        $optionals = ['expires', 'expires_in', 'scope', 'state'];
        /**
         * @var array{
         *       "code": non-empty-string,
         *       "timestamp": positive-int,
         *       "expires_in"?: int|null, // recommended depending lifetime
         *       "state"?: TState, // state is required if client sending state
         *       "scope"?: TScope,
         *       ...<string, mixed>
         *  } $array
         */
        $array = array_filter(
            $array,
            fn($element, $key) => !(!in_array($key, $optionals) || $element === null),
            ARRAY_FILTER_USE_BOTH
        );
        return $array;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->getCode();
    }
}
