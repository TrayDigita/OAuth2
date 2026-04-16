<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Responses;

use JsonSerializable;
use TrayDigita\OAuth2\Interfaces\Parameters\Responses\ErrorDescriptionParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Responses\ErrorParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\Responses\ErrorUriParameterInterface;
use TrayDigita\OAuth2\Interfaces\Parameters\StateParameterInterface;

interface ResponseErrorInterface extends
    ErrorParameterInterface,
    ErrorDescriptionParameterInterface,
    ErrorUriParameterInterface,
    StateParameterInterface,
    JsonSerializable
{
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
    public function jsonSerialize(): array;
}
