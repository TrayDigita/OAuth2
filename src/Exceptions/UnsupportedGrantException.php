<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Exceptions;

use RuntimeException;
use TrayDigita\OAuth2\Interfaces\Exceptions\ExceptionInterface;

/**
 * This exception is thrown when an unsupported grant type is requested.
 */
class UnsupportedGrantException extends RuntimeException implements ExceptionInterface
{
}
