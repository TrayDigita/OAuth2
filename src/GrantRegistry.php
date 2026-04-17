<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2;

use ArrayIterator;
use IteratorAggregate;
use Traversable;
use TrayDigita\OAuth2\Exceptions\GrantNotFoundException;
use TrayDigita\OAuth2\Exceptions\UnsupportedGrantException;
use TrayDigita\OAuth2\Grants\AuthorizationCodeGrant;
use TrayDigita\OAuth2\Grants\ClientCredentialsGrant;
use TrayDigita\OAuth2\Grants\Implicit;
use TrayDigita\OAuth2\Grants\RefreshTokenGrant;
use TrayDigita\OAuth2\Grants\ResourceOwnerPasswordCredentialsGrant;
use TrayDigita\OAuth2\Interfaces\Clients\GrantRegistryInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\AuthorizationCodeGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ClientCredentialsGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ExtensionsGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantParametersInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ImplicitGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\RefreshTokenGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ResourceOwnerGrantInterface;
use function sprintf;

/**
 * Grant Registry
 *
 * This class implements the GrantRegistryInterface and serves as a central registry for managing
 * the various grant types and their associated request parameters in an OAuth2 server implementation.
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1
 *
 * @template GrantReg of GrantParametersInterface
 * @template TList of "authorization_code"|"client_credentials"|"refresh_token"|"password"|"implicit"|non-empty-string
 * @template-implements IteratorAggregate<TList, GrantReg<TList, non-empty-string, non-empty-string>>
 * @template-implements GrantRegistryInterface<TList>
 */
class GrantRegistry implements GrantRegistryInterface, IteratorAggregate
{
    /**
     * @var array{
     *     'authorization_code'?: AuthorizationCodeGrantInterface,
     *     'client_credentials'?: ClientCredentialsGrantInterface,
     *     'refresh_token'?: RefreshTokenGrantInterface,
     *     'password'?: ResourceOwnerGrantInterface,
     *     'implicit'?: ImplicitGrantInterface<non-empty-string>,
     * } $grants
     * The array keys represent the standard grant types, and the values are their corresponding grant instances.
     */
    protected array $grants = [];

    /**
     * Grant for extension
     *
     * @var array<non-empty-string, ExtensionsGrantInterface<non-empty-string>> $extensionGrants
     * The array keys represent the URNs of the extension grant types,
     * and the values are their corresponding grant instances.
     */
    protected array $extensionGrants = [];

    /**
     * @inheritdoc
     */
    public function getAuthorizationCodeGrant(): AuthorizationCodeGrantInterface
    {
        return $this->grants['authorization_code'] ??= new AuthorizationCodeGrant();
    }

    /**
     * @inheritdoc
     */
    public function setAuthorizationCodeGrant(AuthorizationCodeGrantInterface $authorizationCodeGrant): void
    {
        $this->grants['authorization_code'] = $authorizationCodeGrant;
    }

    /**
     * @inheritdoc
     */
    public function getClientCredentialGrant(): ClientCredentialsGrantInterface
    {
        return $this->grants['client_credentials'] ??= new ClientCredentialsGrant();
    }

    /**
     * @inheritdoc
     */
    public function setClientCredentialGrant(ClientCredentialsGrantInterface $clientCredentialGrant): void
    {
        $this->grants['client_credentials'] = $clientCredentialGrant;
    }

    /**
     * @inheritdoc
     */
    public function getRefreshTokenGrant(): RefreshTokenGrantInterface
    {
        return $this->grants['refresh_token'] ??= new RefreshTokenGrant();
    }

    /**
     * @inheritdoc
     */
    public function setRefreshTokenGrant(RefreshTokenGrantInterface $refreshTokenGrant): void
    {
        $this->grants['refresh_token'] = $refreshTokenGrant;
    }

    /**
     * @inheritdoc
     */
    public function getResourceOwnerPasswordCredentialsGrant(): ResourceOwnerGrantInterface
    {
        return $this->grants['resource_owner'] ??= new ResourceOwnerPasswordCredentialsGrant();
    }

    /**
     * @inheritdoc
     */
    public function setResourceOwnerPasswordCredentialsGrant(
        ResourceOwnerGrantInterface $resourceOwnerPasswordCredentialsGrant
    ): void {
        $this->grants['resource_owner'] = $resourceOwnerPasswordCredentialsGrant;
    }

    /**
     * @inheritdoc
     */
    public function getImplicitGrant(): ImplicitGrantInterface
    {
        return $this->grants['implicit'] = new Implicit();
    }

    /**
     * @inheritdoc
     */
    public function setImplicitGrant(ImplicitGrantInterface $implicitGrant): void
    {
        $this->grants['implicit'] = $implicitGrant;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionGrants(): array
    {
        return $this->extensionGrants;
    }

    /**
     * @inheritdoc
     */
    public function hasExtensionGrant(string $urn): bool
    {
        return isset($this->extensionGrants[$urn]);
    }

    /**
     * @inheritdoc
     */
    public function removeExtensionGrant(string $urn): void
    {
        if (isset($this->extensionGrants[$urn])) {
            unset($this->extensionGrants[$urn]);
        }
    }

    /**
     * @inheritdoc
     */
    public function addExtensionGrant(ExtensionsGrantInterface $extensionGrant): void
    {
        $urn = $extensionGrant->getGrantType();
        if ($urn === '') {
            throw new UnsupportedGrantException(
                'Extension grant type cannot be empty'
            );
        }
        $this->extensionGrants[$urn] = $extensionGrant;
    }

    /**
     * @template Urn of non-empty-string the grant type
     *      URN (Uniform Resource Name) that identifies the extension grant type.
     * @param Urn $urn
     * @return ExtensionsGrantInterface<Urn>
     * @throws GrantNotFoundException
     */
    public function getExtensionGrant(string $urn): ExtensionsGrantInterface
    {
        if (!isset($this->extensionGrants[$urn])) {
            throw new GrantNotFoundException(
                sprintf('Grant type "%s" not found', $urn)
            );
        }
        /**
         * @var ExtensionsGrantInterface<Urn> $extension
         */
        $extension = $this->extensionGrants[$urn];
        return $extension;
    }

    /**
     * @inheritdoc
     *
     * Get the grant request parameters for the specified grant type.
     *
     * @template T of "authorization_code"|"client_credentials"|"refresh_token"|"password"|"implicit"|non-empty-string
     * @phpstan-param T $grantType
     * @phpstan-return GrantParametersInterface<T, non-empty-string, non-empty-string>
     * @throws GrantNotFoundException
     */
    public function getGrant(string $grantType): GrantParametersInterface
    {
        /**
         * @var GrantParametersInterface<T, non-empty-string, non-empty-string> $result
         */
        // this is always return GrantRequestParametersInterface<T> because of the type constraint on T
        $result = match ($grantType) { // @phpstan-ignore-line
            'authorization_code' => $this->getAuthorizationCodeGrant(),
            'client_credentials' => $this->getClientCredentialGrant(),
            'refresh_token' => $this->getRefreshTokenGrant(),
            'password' => $this->getResourceOwnerPasswordCredentialsGrant(),
            'implicit' => $this->getImplicitGrant(),
            default => $this->getExtensionGrant($grantType)
        };
        return $result;
    }

    /**
     * @inheritdoc
     * @return non-empty-array<non-empty-string, GrantReg<TList, non-empty-string, non-empty-string>>
     * /
     */
    public function getGrants(): array
    {
        $grants = [
            'authorization_code' => $this->getAuthorizationCodeGrant(),
            'client_credentials' => $this->getClientCredentialGrant(),
            'refresh_token' => $this->getRefreshTokenGrant(),
            'password' => $this->getResourceOwnerPasswordCredentialsGrant(),
            'implicit' => $this->getImplicitGrant(),
        ];
        foreach ($this->extensionGrants as $urn => $extensionGrant) {
            if (isset($grants[$urn])) {
                continue; // skip if the grant type already exists in the standard grants
            }
            $grants[$urn] = $extensionGrant;
        }
        /**
         * @var non-empty-array<non-empty-string, GrantReg<TList, non-empty-string, non-empty-string>> $grants
         */
        return $grants;
    }

    /**
     * @return Traversable<TList, GrantReg<TList, non-empty-string, non-empty-string>>
     */
    public function getIterator(): Traversable
    {
        /**
         * @var Traversable<TList, GrantReg<TList, non-empty-string, non-empty-string>> $result
         */
        $result = new ArrayIterator($this->getGrants());
        return $result;
    }
}
