<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Requests;

/**
 * Redirect URI Parameter Interface defines the contract for requests that include a redirect URI.
 * The redirect URI is used in the authorization request to specify the URL
 * to which the authorization server will redirect the user-agent after the authorization process is complete.
 * It is typically registered by the client during the client registration process and must be included
 * in the authorization request to ensure that the authorization server can validate
 * the redirect URI and prevent unauthorized redirection attacks.
 * The redirect URI is an important security measure that helps to ensure that the authorization response
 * is sent to the correct client and prevents malicious actors from intercepting the authorization
 * response and gaining unauthorized access to the client's resources.
 * The redirect URI is optional in the authorization request, but if it is included,
 * it must be a valid URL and must match one of the redirect URIs registered for the client.
 * If the redirect URI is not included in the authorization
 * request, the authorization server will use the default redirect URI registered for the client,
 * if one exists. If no default redirect URI is registered and the redirect
 * URI is not included in the authorization request, the authorization server will reject the request with an error.
 *
 * <code>
 *    Parameter usage location: authorization request, token request
 * </code>
 * The "redirect_uri" element is defined in Sections 4.1.1, 4.1.3,
 * and 4.2.1:
 * <code>
 *     redirect_uri = URI-reference
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.6
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-3.1.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.3
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2.1
 *
 * @meta redirect_uri: string|null
 */
interface RedirectUriParameterInterface
{
    /**
     * The redirect URI parameter name as defined in RFC6749#section-3.1.2
     */
    public const REDIRECT_URI_PARAMETER_NAME = 'redirect_uri';

    /**
     * The Redirect URL, commonly optional, but if included,
     * it must be a valid URL and must match one of the redirect URIs registered for the client.
     * Optional in the authorization request, but if it is included,
     * it must be a valid URL and must match one of the redirect URIs registered for the client.
     * If the redirect URI is not included in the authorization request,
     * the authorization server will use the default redirect URI registered for the client,
     *
     * @return ?string the redirect URI
     */
    public function getRedirectUri() : ?string;
}
