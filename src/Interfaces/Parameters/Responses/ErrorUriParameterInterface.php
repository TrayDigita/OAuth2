<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Parameters\Responses;

/**
 * The "error_uri" parameter is used to provide a URI that identifies a human-readable web page
 * with information about the error that occurred during the authorization process.
 * This parameter is included in the authorization response when an error occurs,
 * and it provides a way for the client to access additional information about the error that occurred.
 * The "error_uri" parameter is intended to provide a way for the client to access additional information
 * about the error that occurred, such as troubleshooting steps or contact information for support.
 * The URI provided in the "error_uri" parameter should be a human-readable web page that provides
 * information about the error that occurred, and it should be accessible to the client.
 *
 * <code>
 *      Parameter usage location: authorization response, token response
 * </code>
 * The "error_uri" element is defined in Sections 4.1.2.1, 4.2.2.1, 5.2,
 * and 7.2:
 * <code>
 *     error-uri = URI-reference
 * </code>
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749#appendix-A.9
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.2.1
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-5.2
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-11.2.2
 *
 * @meta error_uri: string|null
 */
interface ErrorUriParameterInterface
{
    /**
     * The error_uri parameter name as defined in RFC6749#section-
     */
    public const ERROR_URI_PARAMETER_NAME = 'error_uri';

    /**
     * The "error_uri" parameter is used to provide a URI that identifies a human-readable web page
     * with information about the error that occurred during the authorization process.
     *
     * @return ?string
     */
    public function getErrorUri() : ?string;
}
