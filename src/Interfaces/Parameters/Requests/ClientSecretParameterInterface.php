<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Requests;

/**
 * Client Secret Request Interface defines the contract for requests that include a client secret.
 * The client secret is a confidential string used by the client to authenticate itself
 * to the authorization server during the token request.
 * It is typically issued to the client during the registration process and must be kept
 * secure to prevent unauthorized access to the client's resources.
 *
 * <code>
 *     Parameter usage location: token request
 * </code>
 * The "client_secret" element is defined in Section 2.3.1:
 * <code>
 *      client-secret = *VSCHAR
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.1
 *
 * @meta client_secret: non-empty-string
 */
interface ClientSecretParameterInterface
{
    /**
     * The client secret parameter name as defined in RFC6749#section-2.3.1
     */
    public const CLIENT_SECRET_PARAMETER_NAME = 'client_secret';

    /**
     * The client identifier as described on RFC6749#section-A.2
     *
     * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.2
     * @return non-empty-string
     */
    public function getClientSecret() : string;
}
