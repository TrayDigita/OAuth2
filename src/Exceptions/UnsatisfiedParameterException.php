<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Exceptions;

use InvalidArgumentException;
use TrayDigita\OAuth2\Interfaces\Exceptions\ExceptionInterface;

/**
 * This exception is thrown when a required parameter for a grant is missing or invalid.
 */
class UnsatisfiedParameterException extends InvalidArgumentException implements ExceptionInterface
{
}
