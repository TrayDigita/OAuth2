<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Exceptions;

use RuntimeException;
use TrayDigita\OAuth2\Interfaces\Exceptions\ExceptionInterface;

class UnauthorizedException extends RuntimeException implements ExceptionInterface
{
}
