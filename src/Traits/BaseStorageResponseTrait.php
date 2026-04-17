<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Traits;

use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use TrayDigita\OAuth2\Interfaces\Collections\FreezableCollectionInterface;
use function is_numeric;
use function is_string;
use function time;

/**
 * @template TokenType of non-empty-string
 * @template TState of string
 * @template TScope of string
 */
trait BaseStorageResponseTrait
{
    /**
     * @var string Access token string
     */
    protected string $accessToken;

    /**
     * @var TokenType Access Token Type
     */
    protected string $tokenType;

    /**
     * @var ?TScope Scope string, null if not present
     */
    protected ?string $scope = null;

    /**
     * @var ?TState State string, null if not present
     */
    protected ?string $state = null;

    /**
     * @var ?int Expiration time in seconds from the time of token issuance, null if not present
     */
    protected ?int $expiresIn = null;

    /**
     * @var positive-int Timestamp when the access token was created
     */
    protected int $timestamp;

    /**
     * @var ?int Expiration time in seconds, null if not present
     */
    protected ?int $expires = null;

    /**
     * @var FreezableCollectionInterface<non-empty-string, mixed> $data
     */
    protected FreezableCollectionInterface $data;

    /**
     * AccessToken constructor.
     *
     * @param FreezableCollectionInterface<non-empty-string, mixed> $data
     *  Additional data associated with the flow
     * @throws UnsatisfiedParameterException
     *   if the required parameters for the flow are not satisfied
     * (e.g., missing token, invalid expiration time)
     */
    private function __construct(FreezableCollectionInterface $data)
    {
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
        if (!isset($data['token_type']) || !is_string($data['token_type'])) {
            throw new UnsatisfiedParameterException(
                'The token type is required and must be a string.'
            );
        }
        $tokenType = $data['token_type'];
        /**
         * @var TokenType $tokenType
         */
        $this->tokenType = $tokenType;
        if (isset($data['expires_in'])) {
            if (!is_numeric($data['expires_in'])) {
                throw new UnsatisfiedParameterException(
                    'The expires_in parameter must be a numeric value if provided.'
                );
            }
            $expires = (int)$data['expires_in'];
            $this->expiresIn = $expires;
            $this->expires = $expires !== 0 ? $this->timestamp + $expires : 0;
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
        $this->data = $data;
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
    abstract public function toArray();

    /**
     * @inheritdoc
     */
    public function getAdditionalData(): array
    {
        return $this->getData()->all();
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
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @inheritdoc
     */
    public function isExpired(): bool
    {
        if (!$this->expires) {
            throw new UnsatisfiedParameterException(
                'The access token does not have an expiration time.'
            );
        }
        return time() >= $this->expires;
    }
}
