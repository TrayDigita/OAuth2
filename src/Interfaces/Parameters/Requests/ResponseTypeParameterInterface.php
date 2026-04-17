<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Requests;

/**
 * Response Type Request Interface defines the contract for requests that include a response type parameter.
 * The response type parameter is used in the authorization request
 * to specify the desired authorization processing flow.
 * The most response type must "code",
 * which indicates that the client is requesting an authorization code that can be exchanged for an access token.
 * <code>
 *     Parameter usage location: authorization request
 * </code>
 *
 * The "response_type" element is defined in Sections 3.1.1 and 8.4:
 * <code>
 *      response-type = response-name *( SP response-name )
 *      response-name = 1*response-char
 *      response-char = "_" / DIGIT / ALPHA
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.3
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-3.1.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-8.4
 *
 * @meta response_type: "code"|"token"
 * @template TResponse of "code"|"token"
 */
interface ResponseTypeParameterInterface
{
    /**
     * The response type parameter name as defined in RFC6749#section-3.1.1
     */
    public const RESPONSE_TYPE_PARAMETER_NAME = 'response_type';

    /**
     * The response type as described on RFC6749#section-4.1.1
     * The most response type must "code",
     * @return TResponse
     */
    public function getResponseType() : string;
}
