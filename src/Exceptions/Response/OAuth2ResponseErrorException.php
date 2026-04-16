<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Exceptions\Response;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;
use Throwable;
use TrayDigita\OAuth2\Enums\ErrorType;
use TrayDigita\OAuth2\Exceptions\OperationErrorException;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use TrayDigita\OAuth2\Interfaces\Exceptions\ResponseErrorExceptionInterface;
use function is_string;
use function json_encode;
use function json_last_error_msg;
use function method_exists;
use const JSON_THROW_ON_ERROR;

/**
 * OAuth2ResponseErrorException is an exception that represents an error response from an OAuth2 server.
 * It implements the OAuth2ResponseErrorExceptionInterface and provides methods to access the error information
 * returned by the server, such as the error code, error description, and error URI.
 */
class OAuth2ResponseErrorException extends RuntimeException implements
    ResponseErrorExceptionInterface
{
    /**
     * The error type associated with the error code
     */
    private readonly ErrorType $errorType;

    /**
     * The HTTP status code associated with the error code
     */
    private readonly ?int $httpStatusCode;

    /**
     * OAuth2ResponseException constructor.
     *
     * @param string $error The error code
     * @param string|null $errorUri The error URI
     * @param string|null $state The state parameter from the request
     * @param int|null $httpStatusCode The HTTP status code
     * (optional, defaults to the one associated with the error type)
     * @param string $message The exception message
     * (optional, defaults to the description of the error type)
     * @param int $code The exception code (optional, defaults to 0)
     * @param Throwable|null $previous The previous throwable used for exception chaining
     * (optional, defaults to null)
     */
    public function __construct(
        private readonly string  $error,
        private readonly ?string $errorUri = null,
        private readonly ?string $state = null,
        ?int                     $httpStatusCode = null,
        string                   $message = "",
        int                      $code = 0,
        ?Throwable               $previous = null
    ) {
        $this->errorType = ErrorType::fromError($this->error);
        $this->httpStatusCode = $httpStatusCode ?? $this->errorType->getHttpStatusCode();
        $message = $message ?: $this->errorType->getDescription();
        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritdoc
     */
    public static function fromRequestException(
        RequestExceptionInterface $requestException,
    ): ResponseErrorExceptionInterface {
        // detect if the exception has a response and try to parse it if available
        // eg: guzzlehttp RequestException has a getResponse() method that returns the response if available
        if (method_exists($requestException, 'getResponse')) {
            $response = $requestException->getResponse();
            if ($response instanceof ResponseInterface) {
                return self::fromResponse($response, $requestException);
            }
        }
        return new self(
            error: 'request_exception',
            errorUri: null,
            state: null,
            httpStatusCode: null,
            message: $requestException->getMessage(),
            code: $requestException->getCode(),
            previous: $requestException
        );
    }

    /**
     * @inheritdoc
     */
    public function intoResponse(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface   $streamFactory,
        int                      $defaultStatusCode = self::DEFAULT_STATUS_CODE // unknown error
    ): ResponseInterface {
        try {
            $content = json_encode($this, JSON_THROW_ON_ERROR);
            if (!is_string($content)) {
                throw new OperationErrorException(
                    "Failed to encode error response as JSON: " . json_last_error_msg(),
                );
            }
        } catch (OperationErrorException $throwable) {
            throw $throwable;
        } catch (Throwable $throwable) {
            throw new OperationErrorException(
                "Failed to encode error response as JSON: " . $throwable->getMessage(),
                $throwable->getCode(),
                $throwable
            );
        }
        $defaultStatusCode = $defaultStatusCode < 400 ? self::DEFAULT_STATUS_CODE : $defaultStatusCode;
        $httpCode = $this->getHttpStatusCode() ?? ($this->getErrorType()->getHttpStatusCode() ?? $defaultStatusCode);
        // fallback
        $httpCode = $httpCode < 400 ? $defaultStatusCode : $httpCode;
        $response = $responseFactory->createResponse($httpCode)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $stream = $streamFactory->createStream($content);
        return $response->withBody($stream);
    }

    /**
     * @inheritdoc
     */
    public static function fromResponse(
        ResponseInterface $response,
        ?Throwable        $previous = null
    ): ResponseErrorExceptionInterface {
        $body = $response->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }
        $content = $body->getContents();
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new OperationErrorException(
                "Failed to parse JSON response: " . json_last_error_msg()
            );
        }
        $error = $data['error'] ?? 'unknown_error';
        if (!is_string($error)) {
            throw new UnsatisfiedParameterException(
                "Invalid error code in response: " . var_export($error, true)
            );
        }
        $error_uri = $data['error_uri'] ?? null;
        if ($error_uri !== null && !is_string($error_uri)) {
            throw new UnsatisfiedParameterException(
                "Invalid error URI in response: " . var_export($error_uri, true)
            );
        }
        $state = $data['state'] ?? null;
        if ($state !== null && !is_string($state)) {
            throw new UnsatisfiedParameterException(
                "Invalid state parameter in response: " . var_export($state, true)
            );
        }
        $error_description = $data['error_description'] ?? $data['message'] ?? '';
        if (!is_string($error_description)) {
            throw new UnsatisfiedParameterException(
                "Invalid error description in response: " . var_export($error_description, true)
            );
        }
        return new self(
            error: $error,
            errorUri: $error_uri,
            state: $state,
            httpStatusCode: $response->getStatusCode(),
            message: $error_description,
            previous: $previous
        );
    }

    /**
     * @inheritdoc
     */
    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }

    /**
     * @inheritdoc
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @inheritdoc
     */
    public function getErrorType(): ErrorType
    {
        return $this->errorType;
    }

    /**
     * @inheritdoc
     */
    public function getErrorUri(): ?string
    {
        return $this->errorUri;
    }

    /**
     * @inheritdoc
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return $this->getMessage();
    }

    /**
     * JSON serialize Implementation
     *
     * @return array{
     *     error: string,
     *     error_description: string,
     *     error_uri?: string,
     *     state?: string
     * }
     */
    public function jsonSerialize(): array
    {
        $error = [
            'error' => $this->getError(),
            'error_description' => $this->getDescription(),
        ];
        $error_uri = $this->getErrorUri();
        $state = $this->getState();
        if ($error_uri !== null) {
            $error['error_uri'] = $error_uri;
        }
        if ($state !== null) {
            $error['state'] = $state;
        }
        return $error;
    }
}
