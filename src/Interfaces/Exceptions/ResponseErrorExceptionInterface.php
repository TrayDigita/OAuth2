<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Exceptions;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Throwable;
use TrayDigita\OAuth2\Enums\ErrorType;
use TrayDigita\OAuth2\Interfaces\Responses\ResponseErrorInterface;

/**
 * The error response
 * @link https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.2.1
 */
interface ResponseErrorExceptionInterface extends ExceptionInterface, ResponseErrorInterface
{
    /**
     * The default HTTP status code to use if the exception does not have a specific status code
     */
    public const DEFAULT_STATUS_CODE = 520; // unknown error

    /**
     * Get the HTTP status code
     * The HTTP status code is a three-digit integer that indicates the result of the HTTP request.
     * The HTTP status code is intended to be used
     * by the client to determine the cause of the error and take appropriate action.
     *
     * @return int|null
     */
    public function getHttpStatusCode(): ?int;

    /**
     * Get the error code
     * The error code is a single ASCII string that identifies the error type.
     * The error code is intended to be used by the client
     * to determine the cause of the error and take appropriate action.
     * @return string
     *
     * @see https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.2.1 Error Response
     */
    public function getError(): string;

    /**
     * Get the error type
     * The error type is a single ASCII string that identifies the error type.
     * The error type is intended to be used by the client to determine
     * the cause of the error and take appropriate action.
     * @return ErrorType
     *
     * @see https://datatracker.ietf.org/doc/html/rfc6749#section-4.1.2.1 Error Response
     */
    public function getErrorType(): ErrorType;

    /**
     * Get the error description
     * The error description is a human-readable ASCII string that provides additional information about the error.
     * The error description is intended to be used by the client to provide a more detailed explanation of the error.
     *
     * On OAuth2 error is : `error_description`
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Get the error URI
     * The error URI is a URI that identifies a human-readable web page with information about the error.
     * The error URI is intended to be used by the client to provide additional information about the error.
     *
     * On OAuth2 error is : `error_uri`
     *
     * @return string|null
     */
    public function getErrorUri(): ?string;

    /**
     * Get the state parameter
     * The state parameter is a string that is used to maintain state between the request and the callback.
     * The state parameter is intended to be used by the client to prevent cross-site request forgery (CSRF) attacks.
     *
     * REQUIRED if a "state" parameter was present in the client
     * authorization request.  The exact value received from the
     * client.
     * On OAuth2 error is : `state`
     *
     * @return string|null
     */
    public function getState(): ?string;

    /**
     * Create an instance of the exception from a PSR-7 response
     *
     * @param ResponseInterface $response The PSR-7 response containing the error information
     * @param Throwable|null $previous The previous throwable used for exception chaining (optional, defaults to null)
     * @return self An instance of the exception
     */
    public static function fromResponse(
        ResponseInterface $response,
        ?Throwable        $previous = null
    ): ResponseErrorExceptionInterface;

    /**
     * Create an instance of the exception from a PSR-18 request exception
     *
     * @param RequestExceptionInterface $requestException The PSR-18 request exception containing the error information
     * @return self An instance of the exception
     */
    public static function fromRequestException(
        RequestExceptionInterface $requestException
    ): ResponseErrorExceptionInterface;

    /**
     * Render into a PSR-7 response
     * The method should create a PSR-7 response with
     * the appropriate status code and body content based on the error information contained in the exception.
     * @param ResponseFactoryInterface $responseFactory
     * @param StreamFactoryInterface $streamFactory
     * @param int $defaultStatusCode The default HTTP status code to use if the exception
     *      does not have a specific status code (optional, defaults to 520 - unknown error)
     * @return ResponseInterface
     * @throws \TrayDigita\OAuth2\Exceptions\OperationErrorException
     * if an error occurs while creating the response
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function intoResponse(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        int $defaultStatusCode = self::DEFAULT_STATUS_CODE // unknown error
    ) : ResponseInterface;
}
