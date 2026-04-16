<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Responses;

/**
 * The token type is a string that indicates the type of the access token issued.
 * The most common token type is "Bearer", which indicates that the access token is a
 * bearer token that can be used to access protected resources without any additional authentication.
 *
 * <code>
 *     Parameter usage location: authorization response, token response
 * </code>
 *
 * The "token_type" element is defined in Sections 4.2.2, 5.1, and 8.1:
 * <code>
 *      token-type = type-name / URI-reference
 *      type-name  = 1*name-char
 *      name-char  = "-" / "." / "_" / DIGIT / ALPHA
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.13
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-5.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-8.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 *
 * @meta token_type: string
 */
interface TokenTypeParameterInterface
{
    /**
     * The token type parameter name as defined in RFC6749#section-4.2.2 and 5.1
     */
    public const TOKEN_TYPE_PARAMETER_NAME = 'token_type';

    /**
     * The token type as described on RFC6749#appendix-A.13
     * @return string
     */
    public function getTokenType(): string;
}
