<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Exceptions\Clients;

use RuntimeException;
use TrayDigita\OAuth2\Interfaces\Exceptions\ExceptionInterface;

/**
 * Provider Already Exists Exception
 *
 * This exception is thrown when attempting to add a client provider to the registry
 * that already exists with the same name.
 */
class ProviderAlreadyExistsException extends RuntimeException implements ExceptionInterface
{
}
