<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Requests;

use Psr\Http\Message\ServerRequestInterface;
use TrayDigita\OAuth2\Enums\RequestType;
use TrayDigita\OAuth2\Interfaces\Collections\CollectionInterface;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\GrantParametersInterface;

/**
 * Interface OAuth2RequestInterface
 *
 * This interface represents a generic OAuth2 request,
 * providing methods to access the request data and grant parameters.
 *
 * @template-covariant GrantType of non-empty-string
 * @template-covariant GrantTypeKey of non-empty-string
 * @template-covariant GrantTypeValue of non-empty-string
 * @template Grant of GrantParametersInterface<GrantType, GrantTypeKey, GrantTypeValue>
 */
interface OAuth2RequestInterface
{
    /**
     * Get the server request
     *
     * @return ServerRequestInterface
     */
    public function getServerRequest() : ServerRequestInterface;

    /**
     * Get the request type
     *
     * @return RequestType
     */
    public function getRequestType() : RequestType;

    /**
     * Get all data
     *
     * @return CollectionInterface<non-empty-string, mixed>
     */
    public function getData() : CollectionInterface;

    /**
     * Get grant
     *
     * @return Grant
     */
    public function getGrant() : GrantParametersInterface;
}
