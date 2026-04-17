<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2;

use TrayDigita\OAuth2\Collections\FreezableCollections;
use TrayDigita\OAuth2\Exceptions\Clients\InvalidAccessTokenException;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use TrayDigita\OAuth2\Interfaces\AccessTokenInterface;
use TrayDigita\OAuth2\Interfaces\Collections\FreezableCollectionInterface;
use TrayDigita\OAuth2\Traits\BaseStorageResponseTrait;
use function array_filter;
use function in_array;
use function is_numeric;
use function is_string;
use function trim;
use const ARRAY_FILTER_USE_BOTH;

/**
 * AccessToken class
 *
 * This class represents an access token that can be used to authenticate with an OAuth2 server.
 * It implements the AccessTokenInterface and provides methods to get the access token, refresh token,
 * expiration time, and additional data.
 * @template TokenType of non-empty-string
 * @template TState of string
 * @template TScope of string
 * @template TRefreshToken of string
 * @template-implements AccessTokenInterface<TokenType, ?TState, ?TScope, ?TRefreshToken>
 */
class AccessToken implements AccessTokenInterface
{
    /**
     * @use BaseStorageResponseTrait<TState, TScope>
     */
    use BaseStorageResponseTrait {
        __construct as private baseConstruct;
    }

    /**
     * @var TokenType Access Token Type
     */
    protected string $tokenType;

    /**
     * @var non-empty-string Access token string
     */
    protected string $accessToken;

    /**
     * @var FreezableCollectionInterface<non-empty-string, mixed> Access token data
     */
    protected FreezableCollectionInterface $data;

    /**
     * @var ?TRefreshToken Refresh token string, null if not present
     */
    protected ?string $refreshToken = null;

    /**
     * @var ?string Resource owner ID, null if not present
     */
    protected ?string $resourceOwnerId = null;


    /**
     * AccessToken constructor.
     *
     * @param iterable<non-empty-string, mixed> $data Additional data associated with the access token
     * @throws UnsatisfiedParameterException
     *   if the required parameters for the access token are not satisfied
     * (e.g., missing token, invalid expiration time)
     * @throws InvalidAccessTokenException
     *  if the access token is invalid (e.g., missing token, invalid expiration time).
     */
    public function __construct(iterable $data = [])
    {
        /**
         * @var FreezableCollectionInterface<non-empty-string, mixed> $data
         */
        $data = new FreezableCollections($data);
        if (!isset($data['access_token']) || !is_string($data['access_token'])) {
            throw new InvalidAccessTokenException(
                'The access token is required and must be a string.'
            );
        }
        if ($data['access_token'] === '') {
            throw new InvalidAccessTokenException(
                'The access token cannot be an empty string.'
            );
        }
        $refreshToken = null;
        $resourceOwnerId = null;
        if (isset($data['refresh_token'])) {
            if (!is_string($data['refresh_token'])) {
                throw new UnsatisfiedParameterException('The refresh token must be a string if provided.');
            }
            $refreshToken = $data['refresh_token'];
        }
        if (isset($data['resource_owner_id'])) {
            if (!is_string($data['resource_owner_id']) && !is_numeric($data['resource_owner_id'])) {
                throw new UnsatisfiedParameterException(
                    'The resource_owner_id parameter must be a string or numeric value if provided.'
                );
            }
            $resourceOwnerId = (string)$data['resource_owner_id'];
        }
        if (!isset($data['token_type']) || !is_string($data['token_type']) || trim($data['token_type']) === '') {
            throw new UnsatisfiedParameterException(
                'The token type is required and must be a non-empty string.'
            );
        }
        $tokenType = $data['token_type'];
        /**
         * @var TokenType $tokenType
         */
        $this->tokenType = $tokenType;
        /**
         * @var TRefreshToken $refreshToken
         */
        $this->refreshToken = $refreshToken;
        $this->accessToken = $data['access_token'];
        $this->resourceOwnerId = $resourceOwnerId;
        $data->set('access_token', $this->accessToken);
        $data->set('resource_owner_id', $this->resourceOwnerId);
        $this->baseConstruct($data);
        // freeze the data collection to prevent modification after construction
        // this can modify the data collection to prevent modification after construction,
        // which can help ensure the integrity of the access token data and prevent accidental
        // changes that could lead to security issues or bugs.
        $this->data->freeze();
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @Inheritdoc
     * @return TokenType
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * @inheritdoc
     * @param TRefreshToken $refreshToken
     */
    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @inheritdoc
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @inheritdoc
     */
    public function getResourceOwnerId(): ?string
    {
        return $this->resourceOwnerId;
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->getAccessToken();
    }

    /**
     * @inheritdoc
     *
     * /**
     * @return array{
     *     'timestamp': positive-int,
     *     "access_token": string,
     *     "token_type": TokenType,
     *     "expires_in"?: int|null, // recommended depending lifetime
     *     "refresh_token"?: ?TRefreshToken,
     *     "scope"?: ?TScope, // required if scope not null
     *     "state"?: ?TState, // state is optional but recommended on some cases
     *     ...<non-empty-string, mixed>
     * }
     * @uses self::toArray() to get the array representation of the access token,
     *      then it can be used to serialize the access token to JSON
     */
    public function toArray(): array
    {
        $array = [
            ...$this->getAdditionalData(),
            'timestamp' => $this->timestamp,
            'access_token' => $this->getAccessToken(),
            'refresh_token' => $this->getRefreshToken(),
            'expires_in' => $this->getExpiresIn(),
            'resource_owner_id' => $this->getResourceOwnerId(),
            'scope' => $this->getScope(),
            'state' => $this->getState(),
        ];
        $optionals = ['refresh_token', 'expires', 'expires_in', 'resource_owner_id', 'scope', 'state'];
        /**
         * @var array{
         *      'timestamp': positive-int,
         *      "access_token": string,
         *      "token_type": TokenType,
         *      "expires_in"?: int|null, // recommended depending lifetime
         *      "refresh_token"?: TRefreshToken,
         *      "scope"?: ?TScope, // required if scope not null
         *      "state"?: ?TState, // state is optional but recommended on some cases
         *      ...<non-empty-string, mixed>
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
     * @return array{
     *     'timestamp': positive-int,
     *     "access_token": string,
     *     "token_type": TokenType,
     *     "expires_in"?: int|null, // recommended depending lifetime
     *     "refresh_token"?: ?TRefreshToken,
     *     "scope"?: ?TScope, // required if scope not null
     *     "state"?: ?TState, // state is optional but recommended on some cases
     *     ...<non-empty-string, mixed>
     * }
     * @uses self::toArray() to get the array representation of the access token,
     *      then it can be used to serialize the access token to JSON
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
