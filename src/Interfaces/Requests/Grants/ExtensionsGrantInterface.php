<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests\Grants;

/**
 * ## The client uses an extension grant type
 * by specifying the grant type using an absolute URI (defined by the authorization server) as the
 * value of the "grant_type" parameter of the token endpoint, and by
 * adding any additional parameters necessary.
 *
 * ### Example Request:
 * <code>
 * POST /token HTTP/1.1
 * Host: server.example.com
 * Content-Type: application/x-www-form-urlencoded
 *
 * grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Asaml2-
 * bearer&assertion=PEFzc2VydGlvbiBJc3N1ZUluc3RhbnQ9IjIwMTEtMDU
 * [...omitted for brevity...]aG5TdGF0ZW1lbnQ-PC9Bc3NlcnRpb24-
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.5
 *
 * @template GrantType of non-empty-string
 * @template-extends GrantTypeTokenInterface<GrantType, "grant_type", non-empty-string>
 */
interface ExtensionsGrantInterface extends GrantTypeTokenInterface
{
    /**
     * @inheritdoc
     * @return "grant_type"
     */
    public function getGrantTypeKey(): string;
}
