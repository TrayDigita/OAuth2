<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Exceptions\Clients;

use RuntimeException;
use TrayDigita\OAuth2\Interfaces\Exceptions\ExceptionInterface;

class InvalidAccessTokenException extends RuntimeException implements ExceptionInterface
{
}
