<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Responses;

/**
 * Error Description Parameter Interface defines the contract for parameters that provide
 * a human-readable explanation of an error that occurred during the authorization process.
 *
 * <code>
 *     Parameter usage location: authorization response, token response
 * </code>
 * The "error_description" element is defined in Sections 4.1.2.1,
 * 4.2.2.1, 5.2, and 7.2:
 * <code>
 *     error-description = 1*NQSCHAR
 * </code>
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.8
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.2.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 *
 * @meta error_description: string
 */
interface ErrorDescriptionParameterInterface
{
    /**
     * The error description parameter name as defined in RFC6749#section-
     */
    public const ERROR_DESCRIPTION_PARAMETER_NAME = 'error_description';

    /**
     * Error description parameter provides a human-readable explanation
     * of the error that occurred during the authorization process.
     * It is included in the authorization response when an error occurs, and it provides a human
     *
     * @return string
     */
    public function getDescription(): string;
}
