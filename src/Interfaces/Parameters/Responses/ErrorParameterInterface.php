<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Responses;

/**
 * Error Parameter Interface defines the contract for parameters that indicate
 * an error occurred during the authorization process.
 * The "error" parameter is included in the authorization response when an error occurs,
 * and it provides a human-readable
 * error code that can be used to identify the type of error that occurred
 * . The client can use this error code to determine the appropriate action to take in response to the error.
 * <code>
 *     Parameter usage location: authorization response, token response
 * </code>
 * The "error" element is defined in Sections 4.1.2.1, 4.2.2.1, 5.2,
 * 7.2, and 8.5:
 * <code>
 *     error = 1*NQSCHAR
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.7
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.2.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.2.2.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-5.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 *
 * @meta error: string
 */
interface ErrorParameterInterface
{
    /**
     * The error parameter name as defined in RFC6749#section-
     */
    public const ERROR_PARAMETER_NAME = 'error';

    /**
     * Error parameter is used to indicate that an error occurred during the authorization process.
     * It is included in the authorization response when an error occurs, and it provides a human
     * @linkhttps://datatracker.ietf.org/doc/html/rfc6749#appendix-A.7
     *
     * @return string
     */
    public function getError(): string;
}
