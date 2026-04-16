<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Clients;

use TrayDigita\OAuth2\Interfaces\Requests\Grants\AuthorizationCodeGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ClientCredentialsGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ExtensionsGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantRequestParametersInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ImplicitGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\RefreshTokenGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ResourceOwnerGrantInterface;

/**
 * Grant Registry Interface
 *
 * This interface defines the contract for a grant registry that manages the various grant types
 * and their associated request parameters in an OAuth2 server implementation.
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1
 */
interface GrantRegistryInterface
{
    /**
     * Get the authorization code grant request parameters.
     *
     * @return AuthorizationCodeGrantInterface
     */
    public function getAuthorizationCodeGrant(): AuthorizationCodeGrantInterface;

    /**
     * Set the authorization code grant request parameters.
     *
     * @param AuthorizationCodeGrantInterface $authorizationCodeGrant
     */
    public function setAuthorizationCodeGrant(AuthorizationCodeGrantInterface $authorizationCodeGrant): void;

    /**
     * Get the client credentials grant request parameters.
     *
     * @return ClientCredentialsGrantInterface
     */
    public function getClientCredentialGrant(): ClientCredentialsGrantInterface;

    /**
     * Set the client credentials grant request parameters.
     *
     * @param ClientCredentialsGrantInterface $clientCredentialGrant
     */
    public function setClientCredentialGrant(ClientCredentialsGrantInterface $clientCredentialGrant): void;

    /**
     * Get the refresh token grant request parameters.
     *
     * @return RefreshTokenGrantInterface
     */
    public function getRefreshTokenGrant(): RefreshTokenGrantInterface;

    /**
     * Set the refresh token grant request parameters.
     *
     * @param RefreshTokenGrantInterface $refreshTokenGrant
     */
    public function setRefreshTokenGrant(RefreshTokenGrantInterface $refreshTokenGrant): void;

    /**
     * Get the resource owner password credentials grant request parameters.
     *
     * @return ResourceOwnerGrantInterface
     */
    public function getResourceOwnerPasswordCredentialsGrant(): ResourceOwnerGrantInterface;

    /**
     * Set the resource owner password credentials grant request parameters.
     *
     * @param ResourceOwnerGrantInterface $resourceOwnerPasswordCredentialsGrant
     */
    public function setResourceOwnerPasswordCredentialsGrant(
        ResourceOwnerGrantInterface $resourceOwnerPasswordCredentialsGrant
    ): void;

    /**
     * Get the implicit grant request parameters.
     *
     * @return ImplicitGrantInterface<non-empty-string>
     */
    public function getImplicitGrant(): ImplicitGrantInterface;

    /**
     * Set the implicit grant request parameters.
     *
     * @param ImplicitGrantInterface<non-empty-string> $implicitGrant
     */
    public function setImplicitGrant(ImplicitGrantInterface $implicitGrant): void;

    /**
     * Get the extension grants request parameters.
     *
     * @return array<non-empty-string, ExtensionsGrantInterface<non-empty-string>>
     */
    public function getExtensionGrants(): array;

    /**
     * Check if the extension grant request parameters for the specified grant type exist.
     * The grant type is identified by a URN (Uniform Resource Name) that identifies the
     * extension grant type.
     *
     * @param string $urn
     * @return bool True if the extension grant request parameters for the specified grant type exist, false otherwise.
     */
    public function hasExtensionGrant(string $urn): bool;

    /**
     * Remove the extension grant request parameters for the specified grant type.
     * The grant type is identified by a URN (Uniform Resource Name) that identifies the extension grant type.
     * @param string $urn
     * @return void
     * @throws \TrayDigita\OAuth2\Exceptions\GrantNotFoundException if the specified grant type is not found.
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function removeExtensionGrant(string $urn): void;

    /**
     * Add the extension grant request parameters for the specified grant type.
     * The grant type is identified by a URN (Uniform Resource Name) that identifies the extension grant type.
     * @template Urn of non-empty-string the grant type URN (Uniform Resource Name)
     * that identifies the extension grant type.
     *
     * @param ExtensionsGrantInterface<Urn> $extensionGrant The extension grant request parameters to add.
     * @return void
     * @throws \TrayDigita\OAuth2\Exceptions\UnsupportedGrantException
     *   if the specified grant type is not supported or if the grant type URN is empty.
      * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function addExtensionGrant(ExtensionsGrantInterface $extensionGrant): void;

    /**
     * @template Urn of non-empty-string the grant type
     *      URN (Uniform Resource Name) that identifies the extension grant type.
     * @param Urn $urn
     * @return ExtensionsGrantInterface<Urn>
     * @throws \TrayDigita\OAuth2\Exceptions\GrantNotFoundException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getExtensionGrant(string $urn): ExtensionsGrantInterface;

    /**
     * Get the grant request parameters for the specified grant type.
     *
     * @template T of "authorization_code"|"client_credentials"|"refresh_token"|"password"|"implicit"|non-empty-string
     * @phpstan-param T $grantType
     * @phpstan-return GrantRequestParametersInterface<T>
     * @throws \TrayDigita\OAuth2\Exceptions\GrantNotFoundException
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getGrant(string $grantType): GrantRequestParametersInterface;
}
