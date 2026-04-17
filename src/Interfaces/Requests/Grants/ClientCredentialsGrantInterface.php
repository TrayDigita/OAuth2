<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Grants;

/**
 * ## The client credentials (or other forms of client authentication)
 * can be used as an authorization grant when the authorization scope is
 * limited to the protected resources under the control of the client,
 * or to protected resources previously arranged with the authorization
 * server.  Client credentials are used as an authorization grant
 * typically when the client is acting on its own behalf (the client is
 * also the resource owner) or is requesting access to protected
 * resources based on an authorization previously arranged with the
 * authorization server.
 *
 * ### Flow:
 * <code>
 * +---------+                                  +---------------+
 * |         |                                  |               |
 * |         |>--(A)- Client Authentication --->| Authorization |
 * | Client  |                                  |     Server    |
 * |         |<--(B)---- Access Token ---------<|               |
 * |         |                                  |               |
 * +---------+                                  +---------------+
 *
 * Client Credentials Flow
 * </code>
 *
 * ### Example Request:
 * <code>
 * POST /token HTTP/1.1
 * Host: server.example.com
 * Authorization: Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW
 * Content-Type: application/x-www-form-urlencoded
 *
 * grant_type=client_credentials
 * </code>
 *
 * ### Example Response:
 * <code>
 * HTTP/1.1 200 OK
 * Content-Type: application/json;charset=UTF-8
 * Cache-Control: no-store
 * Pragma: no-cache
 *
 * {
 *   "access_token":"2YotnFZFEjr1zCsicMWpAA",
 *   "token_type":"example",
 *   "expires_in":3600,
 *   "example_parameter":"example_value"
 * }
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.4 Client Credentials Grant (RFC 6749, §4.4)
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-1.3.4
 *
 * @template-extends GrantTypeTokenInterface<"client_credentials", "grant_type", "client_credentials">
 */
interface ClientCredentialsGrantInterface extends GrantTypeTokenInterface
{
    /**
     * Grant name constant
     * @var "client_credentials"
     */
    public const TYPE = 'client_credentials';

    /**
     * @inheritdoc
     * @return "grant_type"
     */
    public function getGrantTypeKey(): string;
}
