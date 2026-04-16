<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Requests;

/**
 * Client Identifier Request Interface defines the contract for requests that include a client identifier.
 * <code>
 *     Parameter usage location: authorization request, token request
 * </code>
 *
 * The "client_id" element is defined in Section 2.3.1:
 * <code>
 *  client-id = *VSCHAR
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-2.3.1
 *
 * @meta client_id: non-empty-string
 */
interface ClientIdParameterInterface
{
    /**
     * The client id parameter name as defined in RFC6749#section-2.3.1
     */
    public const CLIENT_ID_PARAMETER_NAME = 'client_id';

    /**
     * The client id as described on RFC6749#section-2.2
     *
     * @link https://datatracker.ietf.org/doc/html/rfc6749#section-2.2
     * @return non-empty-string
     */
    public function getClientId() : string;
}
