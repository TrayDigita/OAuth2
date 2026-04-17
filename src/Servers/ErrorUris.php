<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Servers;

use Psr\Http\Message\UriInterface;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use function is_string;
use function sprintf;

class ErrorUris
{
    /**
     * @var array<string, string> $errorsURIs
     */
    private static array $errorsURIs = [];

    public static function addErrorUri(string $parameterType, string|UriInterface $uri) : void
    {
        self::$errorsURIs[$parameterType] = (string) $uri;
    }

    /**
     * @param string $parameterType
     * @return string|null the url error uri
     */
    public static function errorUriFor(string $parameterType) : ?string
    {
        return self::getErrorsURIs()[$parameterType]??null;
    }

    /**
     * @return array<string, string>
     */
    public static function getErrorsURIs(): array
    {
        return self::$errorsURIs;
    }

    /**
     * Set error url list
     *
     * @param array<string, string|UriInterface> $uris
     * @return void
     */
    public static function setErrorUris(array $uris) : void
    {
        $urls = [];
        $offset = 0;
        foreach ($uris as $key => $uri) {
            $offset++;
            if (!is_string($key)) {
                throw new UnsatisfiedParameterException(
                    sprintf(
                        'Uri key is not valid for %d',
                        $offset - 1
                    )
                );
            }
            if ($uri instanceof UriInterface) {
                $uri = (string) $uri;
            }
            if (!is_string($uri)) {
                throw new UnsatisfiedParameterException(
                    sprintf(
                        'Uri for %s is invalid',
                        $key
                    )
                );
            }
            $urls[$key] = $uri;
        }
        self::$errorsURIs = $urls;
    }
}
