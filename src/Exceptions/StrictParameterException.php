<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Exceptions;

use InvalidArgumentException;
use TrayDigita\OAuth2\Interfaces\Exceptions\ExceptionInterface;

class StrictParameterException extends InvalidArgumentException implements ExceptionInterface
{
}
