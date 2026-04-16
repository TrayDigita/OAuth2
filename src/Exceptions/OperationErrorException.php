<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Exceptions;

use RuntimeException;
use TrayDigita\OAuth2\Interfaces\Exceptions\ExceptionInterface;

/**
 * Exception raised when an error occurs during the operation.
 */
class OperationErrorException extends RuntimeException implements ExceptionInterface
{
}
