<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Enums;

/**
 * HTTP Request Method
 * OAuth2 only use GET and POST method,
 * @link https://datatracker.ietf.org/doc/html/rfc6749
 */
enum RequestMethod: string
{
    /**
     * The client initiates the flow by requesting authorization from the resource owner.
     * The authorization server authenticates the resource owner and obtains authorization.
     * The authorization server then redirects the user-agent back to the client with an
     * authorization code or access token.
     */
    case GET = 'GET';

    /**
     * The client makes a request to the token endpoint by including the
     * authorization grant and any required parameters.
     */
    case POST = 'POST';
}
