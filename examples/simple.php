<?php
declare(strict_types=1);

use TrayDigita\OAuth2\Exceptions\Response\OAuth2ResponseErrorException;
use TrayDigita\OAuth2\Interfaces\Requests\Grants\AuthorizationCodeGrantInterface;
use TrayDigita\OAuth2\OAuth2Server;

require __DIR__ . '/../vendor/autoload.php';

function response_dispatcher($response): void
{
    header(sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase()));
    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }
    echo $response->getBody();
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }
}

$oauthServer = new OAuth2Server();
try {
    $request = $oauthServer->createNewServerRequest();
    $uri = $request->getUri();
    if (preg_match('~^/authorize/*$~', $uri->getPath()) || preg_match('~^/token/*$~', $uri->getPath())) {
        ['grant' => $grant, 'parameters' => $parameters] = $oauthServer->getDefinitions($request);
        $response = $oauthServer
            ->getResponseFactory()
            ->createResponse(200)
            ->withHeader('content-type', 'application/json');
        // do any validation
        $body = null;
        $streamFactory = $oauthServer->getStreamFactory();
        if ($grant instanceof AuthorizationCodeGrantInterface) {
            $body = [
                $grant->getGrantTypeValue() => base64_encode(random_bytes(64)) // just random example
            ];
            if (isset($parameters['state'])) {
                $body['state'] = $parameters['state'];
            }
        } else {
            // do any process
            $body = null;
        }
        $response = $response->withBody($streamFactory->createStream(json_encode($body)));
        response_dispatcher($response);
        // do process
    } else {
        throw new OAuth2ResponseErrorException(
            'endpoint_not_found',
            httpStatusCode: 404,
            message: 'Not Found'
        );
    }
} catch (OAuth2ResponseErrorException $e) {
    $response = $e->intoResponse(
        $oauthServer->getResponseFactory(),
        $oauthServer->getStreamFactory()
    );
    response_dispatcher($response);
}
