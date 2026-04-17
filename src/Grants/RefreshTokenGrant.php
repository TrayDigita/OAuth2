<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Grants;

use Psr\Http\Message\ServerRequestInterface;
use TrayDigita\OAuth2\Abstracts\AbstractGrant;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Exceptions\OperationNotPermittedException;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedGrantParameterException;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\RefreshTokenGrantInterface;
use TrayDigita\OAuth2\Interfaces\Requests\OAuth2RequestInterface;

/**
 * ## Refresh tokens are credentials used to obtain access tokens.
 *
 * Refresh tokens are issued to the client by the authorization server and are
 * used to obtain a new access token when the current access token
 * becomes invalid or expires, or to obtain additional access tokens
 * with identical or narrower scope (access tokens may have a shorter
 * lifetime and fewer permissions than authorized by the resource
 * owner).  Issuing a refresh token is optional at the discretion of the
 * authorization server.
 *
 * ### Flow:
 * <code>
 * +--------+                                           +---------------+
 * |        |--(A)------- Authorization Grant --------->|               |
 * |        |                                           |               |
 * |        |<-(B)----------- Access Token -------------|               |
 * |        |               & Refresh Token             |               |
 * |        |                                           |               |
 * |        |                            +----------+   |               |
 * |        |--(C)---- Access Token ---->|          |   |               |
 * |        |                            |          |   |               |
 * |        |<-(D)- Protected Resource --| Resource |   | Authorization |
 * | Client |                            |  Server  |   |     Server    |
 * |        |--(E)---- Access Token ---->|          |   |               |
 * |        |                            |          |   |               |
 * |        |<-(F)- Invalid Token Error -|          |   |               |
 * |        |                            +----------+   |               |
 * |        |                                           |               |
 * |        |--(G)----------- Refresh Token ----------->|               |
 * |        |                                           |               |
 * |        |<-(H)----------- Access Token -------------|               |
 * +--------+           & Optional Refresh Token        +---------------+
 *
 * </code>
 *
 * ### Example Request:
 * <code>
 * POST /token HTTP/1.1
 * Host: server.example.com
 * Content-Type: application/x-www-form-urlencoded
 *
 * grant_type=refresh_token&refresh_token=tGzv3JOkF0XG5Qx2TlKWIA
 * &client_id=s6BhdRkqt3&client_secret=7Fjfp0ZBr1KtDRbnfVdmIw
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-1.5 Introduction about Refresh Token
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-6 Refreshing an Access Token (RFC 6749, §6)
 * @see https://datatracker.ietf.org/doc/html/rfc6749#section-2.3.1 Example Password Authentication
 *
 * @template-extends AbstractGrant<"refresh_token", "grant_type", "refresh_token">
 */
class RefreshTokenGrant extends AbstractGrant implements RefreshTokenGrantInterface
{
    /**
     * @inheritdoc
     * @return "refresh_token"
     */
    public function getGrantType(): string
    {
        return self::TYPE;
    }

    /**
     * @inheritdoc
     */
    public function getOptionalClientRequestParameters(RequestType $requestType): array
    {
        /**
         * The scope of the access request as described by
         * Section 3.3.  The requested scope MUST NOT include any scope
         * not originally granted by the resource owner, and if omitted is
         * treated as equal to the scope originally granted by the
         * resource owner.
         * @link https://datatracker.ietf.org/doc/html/rfc6749#section-6
         */
        return [
            'scope',
            'state'
        ];
    }

    /**
     * @inheritdoc
     * Assert that the required parameters are present in the given parameters array.
     *
     * @param array<string, mixed> $parameters The parameters to check.
     * @throws UnsatisfiedGrantParameterException if any required parameter is missing.
     */
    protected function assertRequiredParameters(RequestType $requestType, array $parameters): void
    {
        parent::assertRequiredParameters($requestType, $parameters);
        if (!isset($parameters['refresh_token'])
            || !is_string($parameters['refresh_token'])
            || $parameters['refresh_token'] === ''
        ) {
            throw new UnsatisfiedGrantParameterException(
                'The "refresh_token" parameter is required and must be a non-empty string.'
            );
        }
    }

    /**
     * @inheritdoc
     * @return list<non-empty-string>&list<'grant_type','refresh_token'>
     */
    public function getRequiredClientRequestParameters(RequestType $requestType): array
    {
        return [
            'grant_type',
            'refresh_token'
        ];
    }

    /**
     * @inheritdoc
     * @return "grant_type"
     */
    public function getGrantTypeKey(): string
    {
        return 'grant_type';
    }

    /**
     * @inheritdoc
     */
    protected function convertOAuth2Request(
        ServerRequestInterface $request,
        RequestType $requestType,
        array $parameters
    ): OAuth2RequestInterface {
        // TODO: Implement convertOAuth2Request() method.
        throw new OperationNotPermittedException(
            'Conversion not yet implemented'
        );
    }
}
