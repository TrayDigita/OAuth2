<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Enums;

/**
 * Enum for the request type of the OAuth2 flow.
 *
 * @link https://datatracker.ietf.org/doc/html/rfc6749
 */
enum RequestType: string
{
    /**
     * The client initiates the flow by requesting authorization from the resource owner.
     * The authorization server authenticates the resource owner and obtains authorization.
     * The authorization server then redirects the user-agent back to the client with an
     * authorization code or access token.
     */
    case AUTHORIZATION = 'authorization';

    /**
     * The client makes a request to the token endpoint by including the
     * authorization grant and any required parameters.
     */
    case TOKEN = 'token';

    /**
     * Get the HTTP request method for the request type.
     *
     * @return RequestMethod
     */
    public function getRequestMethod(): RequestMethod
    {
        return match ($this) {
            self::AUTHORIZATION => RequestMethod::GET,
            self::TOKEN => RequestMethod::POST,
        };
    }
}
