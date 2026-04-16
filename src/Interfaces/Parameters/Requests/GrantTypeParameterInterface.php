<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Requests;

/**
 * Grant type parameter for authorization request and token request.
 * <code>
 *     Parameter usage location: token request
 * </code>
 * The "grant_type" element is defined in Sections 4.1.3, 4.3.2, 4.4.2,
 * 4.5, and 6:
 * <code>
 *     grant-type = grant-name / URI-reference
 *      grant-name = 1*name-char
 *      name-char  = "-" / "." / "_" / DIGIT / ALPHA
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.10
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-1.3
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.3
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.3.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.4.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.5
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-6
 *
 * @meta grant_type: non-empty-string
 *
 * @template-covariant GrantType of non-empty-string
 */
interface GrantTypeParameterInterface
{
    /**
     * The grant type parameter name as defined in RFC6749#section-2.3.1
     */
    public const GRANT_TYPE_PARAMETER_NAME = 'grant_type';

    /**
     * The grant type parameter is required in the token request and must be set to the value of the
     * "grant_type" parameter as defined in the authorization grant type being used.
     *
     * @return GrantType
     */
    public function getGrantType() : string;
}
