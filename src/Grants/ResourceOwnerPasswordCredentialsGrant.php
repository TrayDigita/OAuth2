<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Grants;

use TrayDigita\OAuth2\Abstracts\AbstractGrant;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedGrantParameterException;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\ResourceOwnerGrantInterface;

/**
 * The resource owner password credentials (i.e., username and password)
 * can be used directly as an authorization grant to obtain an access
 * token.  The credentials should only be used when there is a high
 * degree of trust between the resource owner and the client (e.g., the
 * client is part of the device operating system or a highly privileged
 * application), and when other authorization grant types are not
 * available (such as an authorization code).
 *
 * Even though this grant type requires direct client access to the
 * resource owner credentials, the resource owner credentials are used
 * for a single request and are exchanged for an access token.  This
 * grant type can eliminate the need for the client to store the
 * resource owner credentials for future use, by exchanging the
 * credentials with a long-lived access token or refresh token.
 *
 * ### Flow:
 *
 * <code>
 * +----------+
 * | Resource |
 * |  Owner   |
 * |          |
 * +----------+
 * v
 * |    Resource Owner
 * (A) Password Credentials
 * |
 * v
 * +---------+                                  +---------------+
 * |         |>--(B)---- Resource Owner ------->|               |
 * |         |         Password Credentials     | Authorization |
 * | Client  |                                  |     Server    |
 * |         |<--(C)---- Access Token ---------<|               |
 * |         |    (w/ Optional Refresh Token)   |               |
 * +---------+                                  +---------------+
 *
 * Resource Owner Password Credentials Flow
 * </code>
 *
 * ### Example Request:
 * <code>
 * POST /token HTTP/1.1
 * Host: server.example.com
 * Authorization: Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW
 * Content-Type: application/x-www-form-urlencoded
 *
 * grant_type=password&username=johndoe&password=A3ddj3w
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
 *   "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA",
 *   "example_parameter":"example_value"
 * }
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-1.3.3 Resource Owner Password Credentials (RFC 6749)
 * @see https://datatracker.ietf.org/doc/html/rfc6749#section-4.3.2
 * @see https://datatracker.ietf.org/doc/html/rfc6749#section-4.3
 *
 * @template-extends AbstractGrant<"password", "grant_type", "password">
 */
class ResourceOwnerPasswordCredentialsGrant extends AbstractGrant implements ResourceOwnerGrantInterface
{
    /**
     * @inheritdoc
     * @return "password"
     */
    public function getGrantType(): string
    {
        return self::TYPE;
    }

    /**
     * Assert that the required parameters are present in the given parameters array.
     *
     * @param array<string, mixed> $parameters The parameters to check.
     * @throws UnsatisfiedGrantParameterException if any required parameter is missing.
     */
    protected function assertRequiredParameters(RequestType $requestType, array $parameters): void
    {
        parent::assertRequiredParameters($requestType, $parameters);
        if (!isset($parameters['username'])
            || !is_string($parameters['username'])
            || $parameters['username'] === ''
        ) {
            throw new UnsatisfiedGrantParameterException(
                'username is required and must be a string'
            );
        }
        if (!isset($parameters['password'])
            || !is_string($parameters['password'])
            || $parameters['password'] === '' // strict!
        ) {
            throw new UnsatisfiedGrantParameterException(
                'password is required and must be a string'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getOptionalClientRequestParameters(RequestType $requestType): array
    {
        /**
         * Optional scope parameter, which is a space-delimited list of scopes that the client is requesting.
         * @link https://datatracker.ietf.org/doc/html/rfc6749#section-3.3
         */
        return [
            'scope',
            'state'
        ];
    }

    /**
     * @inheritdoc
     * @return list<non-empty-string>&list<'grant_type','username','password'>
     */
    public function getRequiredClientRequestParameters(RequestType $requestType): array
    {
        return [
            'grant_type',
            'username',
            'password'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getGrantTypeKey(): string
    {
        return 'grant_type';
    }
}
