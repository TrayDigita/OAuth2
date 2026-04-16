<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters;

/**
 * The state parameter is an opaque value used by the client to maintain state between the request and callback.
 * It is included in the authorization request and returned in the authorization response.
 * The state parameter is used to prevent cross-site request forgery (CSRF) attacks by allowing the client
 * to verify that the response is from the expected authorization request.
 * The client should generate a unique and random value for the state parameter for each authorization request,
 * and validate the state parameter in the authorization response to ensure
 * that it matches the value sent in the authorization request.
 * The state parameter is optional, but it is recommended to use
 * it to enhance the security of the authorization process.
 * Required if the client includes the state parameter in the authorization request,
 * and the authorization server must include the state parameter in the response if it was included in the request.
 *
 * <code>
 *     Parameter usage location: authorization request, authorization response
 * </code>
 *
 * The "state" element is defined in Sections 4.1.1, 4.1.2, 4.1.2.1,
 * 4.2.1, 4.2.2, and 4.2.2.1:
 * <code>
 *      state = 1*VSCHAR
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.5
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2.2
 *
 * @meta state: string|null
 */
interface StateParameterInterface
{
    /**
     * The state parameter name as defined in RFC6749#section-4.1.1 and 4.1.2
     */
    public const STATE_PARAMETER_NAME = 'state';

    /**
     * Get state parameter as described on RFC6749#section-4.1.1, 4.1.2,
     *
     * @return ?string
     */
    public function getState(): ?string;
}
