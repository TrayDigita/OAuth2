<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Scratches;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * Interface PreparationRequestInterface
 *
 * This interface defines a contract for preparing an HTTP request before it is sent to the server.
 * Implementing classes can modify the request, add necessary headers, or perform any required transformations.
 */
interface PreparationRequestInterface
{
    /**
     * Prepare the request before sending it to the server.
     *
     * @param RequestInterface $request The original request to be prepared.
     * @param StreamFactoryInterface $streamFactory Factory for creating stream instances.
     * @param UriFactoryInterface $uriFactory Factory for creating URI instances.
     * @return RequestInterface The prepared request ready to be sent to the server.
     */
    public function prepareRequest(
        RequestInterface $request,
        StreamFactoryInterface $streamFactory,
        UriFactoryInterface $uriFactory
    ) : RequestInterface;
}
