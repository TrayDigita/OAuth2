<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters;

/**
 * The Scope Parameter Interface defines the contract for requests that include a scope parameter.
 * The scope parameter is used in the authorization request and token request to specify
 * the level of access that the client is requesting from the resource owner.
 * It is a space-delimited list of permissions that the client is requesting,
 * and it is typically defined by the authorization server to control access to protected resources.
 * The scope parameter allows the client to request specific permissions,
 * and the authorization server can use this information to determine whether to grant
 * or deny the request based on the client's registered permissions and the resource owner's consent.
 * <code>
 *     Parameter usage location: authorization request, authorization response, token request, token response
 * </code>
 *
 * The "scope" element is defined in Section 3.3:
 * <code>
 *      scope = scope-token *( SP scope-token )
 *      scope-token = 1*NQCHAR
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.4
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-3.3
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.1
 *
 * @meta scope: string|null
 */
interface ScopeParameterInterface
{
    /**
     * The scope parameter name as defined in RFC6749#section-3.3
     */
    public const SCOPE_PARAMETER_NAME = 'scope';

    /**
     * The response type as described on RFC6749#section-4.1.1
     * Optional, but if included, it must be a space-delimited list of permissions that the client is requesting.
     *
     * @return ?string space-delimited list of permissions that the client is requesting
     */
    public function getScope(): ?string;
}
