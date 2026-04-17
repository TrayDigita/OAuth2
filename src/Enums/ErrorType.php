<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Enums;

use function strtolower;
use function trim;

/**
 * Error type
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.2.1
 */
enum ErrorType: string
{
    /**
     * The request is missing a required parameter, includes an
     * unsupported parameter value (other than grant type),
     * repeats a parameter, includes multiple credentials,
     * utilizes more than one mechanism for authenticating the
     * client, or is otherwise malformed.
     */
    case INVALID_REQUEST = 'invalid_request';

    /**
     * Client authentication failed (e.g., unknown client, no
     * client authentication included, or unsupported
     * authentication method).  The authorization server MAY
     * return an HTTP 401 (Unauthorized) status code to indicate
     * which HTTP authentication schemes are supported.  If the
     * client attempted to authenticate via the "Authorization"
     * request header field, the authorization server MUST
     * respond with an HTTP 401 (Unauthorized) status code and
     * include the "WWW-Authenticate" response header field
     * matching the authentication scheme used by the client.
     */
    case INVALID_CLIENT = 'invalid_client';

    /**
     * The authenticated client is not authorized to use this
     * authorization grant type
     */
    case UNAUTHORIZED_CLIENT = 'unauthorized_client';

    /**
     * The resource owner or authorization server denied the
     * request.
     */
    case ACCESS_DENIED = 'access_denied';

    /**
     * The authorization server does not support obtaining an
     * authorization code using this method.
     */
    case UNSUPPORTED_RESPONSE_TYPE = 'unsupported_response_type';

    /**
     * The authorization grant type is not supported by the
     * authorization server.
     */
    case UNSUPPORTED_GRANT_TYPE = 'unsupported_grant_type';

    /**
     * The requested scope is invalid, unknown, malformed, or
     * exceeds the scope granted by the resource owner.
     */
    case INVALID_SCOPE = 'invalid_scope';

    /**
     * The requested state is invalid, unknown, malformed, or
     * exceeds the scope granted by the resource owner.
     */
    case INVALID_STATE = 'invalid_state';

    /**
     * The authorization server encountered an unexpected
     * condition that prevented it from fulfilling the request.
     * (This error code is needed because a 500 Internal Server
     * Error HTTP status code cannot be returned to the client
     * via an HTTP redirect.)
     */
    case SERVER_ERROR = 'server_error';

    /**
     * The authorization server is currently unable to handle
     * the request due to a temporary overloading or maintenance
     * of the server.  (This error code is needed because a 503
     * Service Unavailable HTTP status code cannot be returned
     * to the client via an HTTP redirect.)
     */
    case TEMPORARILY_UNAVAILABLE = 'temporarily_unavailable';

    /**
     * The provided authorization grant (e.g., authorization
     * code, resource owner credentials) or refresh token is
     * invalid, expired, revoked, does not match the redirection
     * URI used in the authorization request, or was issued to
     * another client.
     */
    case INVALID_GRANT = 'invalid_grant';

    /**
     * Custom error code (not-standard)
     */
    case OTHER = 'uncategorized_error';

    /**
     * Convert from `error` key to ErrorTye
     *
     * @param string $error
     * @return self
     */
    public static function fromError(string $error) : self
    {
        $error = strtolower(trim($error));
        return match ($error) {
            ErrorType::INVALID_REQUEST->value => ErrorType::INVALID_REQUEST,
            ErrorType::UNAUTHORIZED_CLIENT->value => ErrorType::UNAUTHORIZED_CLIENT,
            ErrorType::ACCESS_DENIED->value => ErrorType::ACCESS_DENIED,
            ErrorType::UNSUPPORTED_RESPONSE_TYPE->value => ErrorType::UNSUPPORTED_RESPONSE_TYPE,
            ErrorType::INVALID_SCOPE->value => ErrorType::INVALID_SCOPE,
            ErrorType::SERVER_ERROR->value => ErrorType::SERVER_ERROR,
            ErrorType::TEMPORARILY_UNAVAILABLE->value => ErrorType::TEMPORARILY_UNAVAILABLE,
            ErrorType::INVALID_CLIENT->value => ErrorType::INVALID_CLIENT,
            ErrorType::UNSUPPORTED_GRANT_TYPE->value => ErrorType::UNSUPPORTED_GRANT_TYPE,
            ErrorType::INVALID_STATE->value => ErrorType::INVALID_STATE,
            default => ErrorType::OTHER,
        };
    }

    /**
     * Get the human-readable description of the error type
     *
     * @return string
     */
    public function getDescription() : string
    {
        return match ($this) {
            self::INVALID_REQUEST           => 'Invalid Request',
            self::UNAUTHORIZED_CLIENT       => 'Unauthorized Client',
            self::ACCESS_DENIED             => 'Access Denied',
            self::UNSUPPORTED_RESPONSE_TYPE => 'Unsupported Response Type',
            self::INVALID_SCOPE             => 'Invalid Scope',
            self::SERVER_ERROR              => 'Internal Server Error',
            self::TEMPORARILY_UNAVAILABLE   => 'Resource Temporary Unavailable',
            self::INVALID_GRANT             => 'Invalid Grant',
            self::INVALID_CLIENT            => 'Client Authentication Failed',
            self::UNSUPPORTED_GRANT_TYPE    => 'Unsupported Grant Type',
            self::INVALID_STATE             => 'Invalid State',
            self::OTHER                     => 'Unknown Error',
        };
    }

    /**
     * Get the corresponding HTTP status code for the error type
     *
     * @return int|null
     */
    public function getHttpStatusCode() : ?int
    {
        return match ($this) {
            self::INVALID_REQUEST,
            self::UNSUPPORTED_RESPONSE_TYPE,
            self::UNSUPPORTED_GRANT_TYPE,
            self::INVALID_STATE,
            self::INVALID_SCOPE, self::INVALID_GRANT => 400,
            self::UNAUTHORIZED_CLIENT,
            self::ACCESS_DENIED, self::INVALID_CLIENT => 401,
            self::SERVER_ERROR => 500,
            self::TEMPORARILY_UNAVAILABLE => 503,
            self::OTHER => null, // 520 is unknown
        };
    }
}
