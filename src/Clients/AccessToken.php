<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Clients;

use TrayDigita\OAuth2\Collections\FreezableCollections;
use TrayDigita\OAuth2\Exceptions\Clients\InvalidAccessTokenException;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use TrayDigita\OAuth2\Interfaces\AccessTokenInterface;
use TrayDigita\OAuth2\Interfaces\Collections\FreezableCollectionInterface;
use function array_filter;
use function in_array;
use function is_numeric;
use function is_string;
use function time;
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
 *
 */
class AccessToken implements AccessTokenInterface
{
    /**
     * @var FreezableCollectionInterface<non-empty-string, mixed> Access token data
     */
    protected FreezableCollectionInterface $data;

    /**
     * @var string Access token string
     */
    protected string $accessToken;

    /**
     * @var TokenType Access Token Type
     */
    protected string $tokenType;

    /**
     * @var ?TRefreshToken Refresh token string, null if not present
     */
    protected ?string $refreshToken = null;

    /**
     * @var ?TScope Scope string, null if not present
     */
    protected ?string $scope = null;

    /**
     * @var ?TState State string, null if not present
     */
    protected ?string $state = null;

    /**
     * @var ?int Expiration time in seconds, null if not present
     */
    protected ?int $expires = null;

    /**
     * @var ?int Expiration time in seconds from the time of token issuance, null if not present
     */
    protected ?int $expiresIn = null;

    /**
     * @var ?string Resource owner ID, null if not present
     */
    protected ?string $resourceOwnerId = null;

    /**
     * @var positive-int Timestamp when the access token was created
     */
    protected int $timestamp;

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
        if (isset($data['timestamp'])) {
            if (!is_numeric($data['timestamp'])) {
                throw new UnsatisfiedParameterException(
                    'The timestamp parameter must be a numeric value if provided.'
                );
            }
            $timestamp = (int)$data['timestamp'];
            if ($timestamp <= 0) {
                throw new UnsatisfiedParameterException(
                    'The timestamp parameter must be a positive integer if provided.'
                );
            }
        } else {
            $timestamp = time();
        }
        /**
         * @var positive-int $timestamp
         */
        $this->timestamp = $timestamp;
        if (!isset($data['access_token']) || !is_string($data['access_token'])) {
            throw new InvalidAccessTokenException(
                'The access token is required and must be a string.'
            );
        }
        if (!isset($data['token_type']) || !is_string($data['token_type'])) {
            throw new InvalidAccessTokenException(
                'The token type is required and must be a string.'
            );
        }
        $tokenType = $data['token_type'];
        /**
         * @var TokenType $tokenType
         */
        $this->tokenType = $tokenType;
        if (isset($data['refresh_token'])) {
            if (!is_string($data['refresh_token'])) {
                throw new UnsatisfiedParameterException('The refresh token must be a string if provided.');
            }
            $refreshToken = $data['refresh_token'];
            /**
             * @var TRefreshToken $refreshToken
             */
            $this->refreshToken = $refreshToken;
        }
        if (isset($data['expires_in'])) {
            if (!is_numeric($data['expires_in'])) {
                throw new UnsatisfiedParameterException(
                    'The expires_in parameter must be a numeric value if provided.'
                );
            }
            $expires = (int)$data['expires_in'];
            $this->expiresIn = $expires;
            $this->expires = $expires !== 0 ? $this->timestamp + $expires : 0;
            $data->set('expires_in', $this->expiresIn);
        } elseif (isset($data['expires'])) {
            if (!is_numeric($data['expires'])) {
                throw new UnsatisfiedParameterException('The expires parameter must be a numeric value if provided.');
            }
            $expires = (int)$data['expires'];
            // Some providers supply the seconds until expiration rather than
            // the exact timestamp. Take the best guess at which we received.
            $oauth2InceptionDate = 1349067600; // 2012-10-01
            // If the expires value is less than the inception date,
            // assume it's a duration in seconds from the draft date
            if ($expires < $oauth2InceptionDate) {
                $expires = $this->timestamp + $expires;
            }
            $this->expires = $expires;
            $this->expiresIn = $expires !== 0 ? $expires - $this->timestamp : 0;
            $data->set('expires_in', $this->expiresIn);
        }
        if (isset($data['resource_owner_id'])) {
            if (!is_string($data['resource_owner_id']) && !is_numeric($data['resource_owner_id'])) {
                throw new UnsatisfiedParameterException(
                    'The resource_owner_id parameter must be a string or numeric value if provided.'
                );
            }
            $this->resourceOwnerId = (string)$data['resource_owner_id'];
            $data->set('resource_owner_id', $this->resourceOwnerId);
        }
        if (isset($data['scope'])) {
            if (!is_string($data['scope'])) {
                throw new UnsatisfiedParameterException('The scope parameter must be a string if provided.');
            }
            $scope = $data['scope'];
            /**
             * @var TScope $scope
             */
            $this->scope = $scope;
        }
        if (isset($data['state'])) {
            if (!is_string($data['state'])) {
                throw new UnsatisfiedParameterException('The state parameter must be a string if provided.');
            }
            $state = $data['state'];
            /**
             * @var TState $state
             */
            $this->state = $state;
        }
        $this->accessToken = $data['access_token'];
        $data->set('access_token', $this->accessToken);
        $this->data = $data;
        // freeze the data collection to prevent modification after construction
        // this can modify the data collection to prevent modification after construction,
        // which can help ensure the integrity of the access token data and prevent accidental
        // changes that could lead to security issues or bugs.
        $this->data->freeze();
    }

    /**
     * @Inheritdoc
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @inheritdoc
     */
    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    /**
     * @inheritdoc
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @inheritdoc
     */
    public function getState(): ?string
    {
        return $this->state;
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
    public function getExpires(): ?int
    {
        return $this->expires;
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @inheritdoc
     */
    public function isExpired(): bool
    {
        if (!$this->expires) {
            throw new InvalidAccessTokenException(
                'The access token does not have an expiration time.'
            );
        }
        return time() >= $this->expires;
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
     * @return FreezableCollectionInterface<non-empty-string, mixed>
     */
    public function getData(): FreezableCollectionInterface
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalData(): array
    {
        return $this->getData()->all();
    }

    /**
     * @inheritdoc

    /**
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
            fn ($element, $key) => !(!in_array($key, $optionals) || $element === null),
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
