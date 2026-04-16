<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Exceptions;

/**
 * Unsupported Operation Exception
 * This exception is thrown when an operation is not supported by the server.
 */
class UnsupportedOperationException extends OperationNotPermittedException
{
}
