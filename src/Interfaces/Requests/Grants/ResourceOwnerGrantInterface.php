<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Grants;

use TrayDigita\OAuth2\Enums\RequestType;

/**
 *  The resource owner password credentials (i.e., username and password)
 *  can be used directly as an authorization grant to obtain an access
 *  token.  The credentials should only be used when there is a high
 *  degree of trust between the resource owner and the client (e.g., the
 *  client is part of the device operating system or a highly privileged
 *  application), and when other authorization grant types are not
 *  available (such as an authorization code).
 *
 *  Even though this grant type requires direct client access to the
 *  resource owner credentials, the resource owner credentials are used
 *  for a single request and are exchanged for an access token.  This
 *  grant type can eliminate the need for the client to store the
 *  resource owner credentials for future use, by exchanging the
 *  credentials with a long-lived access token or refresh token.
 *
 *  ### Flow:
 *
 *  <code>
 *  +----------+
 *  | Resource |
 *  |  Owner   |
 *  |          |
 *  +----------+
 *  v
 *  |    Resource Owner
 *  (A) Password Credentials
 *  |
 *  v
 *  +---------+                                  +---------------+
 *  |         |>--(B)---- Resource Owner ------->|               |
 *  |         |         Password Credentials     | Authorization |
 *  | Client  |                                  |     Server    |
 *  |         |<--(C)---- Access Token ---------<|               |
 *  |         |    (w/ Optional Refresh Token)   |               |
 *  +---------+                                  +---------------+
 *
 *  Resource Owner Password Credentials Flow
 *  </code>
 *
 *  ### Example Request:
 *  <code>
 *  POST /token HTTP/1.1
 *  Host: server.example.com
 *  Authorization: Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW
 *  Content-Type: application/x-www-form-urlencoded
 *
 *  grant_type=password&username=johndoe&password=A3ddj3w
 *  </code>
 *
 *  ### Example Response:
 *  <code>
 *  HTTP/1.1 200 OK
 *  Content-Type: application/json;charset=UTF-8
 *  Cache-Control: no-store
 *  Pragma: no-cache
 *
 *  {
 *    "access_token":"2YotnFZFEjr1zCsicMWpAA",
 *    "token_type":"example",
 *    "expires_in":3600,
 *    "refresh_token":"tGzv3JOkF0XG5Qx2TlKWIA",
 *    "example_parameter":"example_value"
 *  }
 *  </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-1.3.3 Resource Owner Password Credentials (RFC 6749)
 * @see https://datatracker.ietf.org/doc/html/rfc6749#section-4.3.2
 * @see https://datatracker.ietf.org/doc/html/rfc6749#section-4.3
 *
 * @template-extends GrantTypeTokenInterface<"password", "grant_type", "password">
 */
interface ResourceOwnerGrantInterface extends GrantTypeTokenInterface
{
    /**
     * Grant name constant
     * @var "password"
     */
    public const TYPE = 'password';

    /**
     * @inheritdoc
     * @return "grant_type"
     */
    public function getGrantTypeKey(): string;

    /**
     * @inheritdoc
     * @return list<'grant_type','username','password'>&list<non-empty-string>
     */
    public function getRequiredClientRequestParameters(RequestType $requestType): array;

    /**
     * @inheritdoc
     *
     * @template TKey of string
     * @template TValue
     *
     * @param array<TKey, TValue> $parameters
     * @param array<TKey, TValue> $defaultParameters
     * @return array{
     *     "grant_type" : "password",
     *     "username" : string,
     *     "password" : string,
     *      "scope"?: non-empty-string,
     *      "state"?: non-empty-string,
     *      "redirect_uri"?: non-empty-string,
     *     ...<TKey, TValue>
     * }
     */
    public function prepareClientRequestParameters(
        RequestType $requestType,
        array $parameters,
        array $defaultParameters
    ): array;
}
