<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Servers;

use Psr\Http\Message\UriInterface;
use TrayDigita\OAuth2\Exceptions\UnsatisfiedParameterException;
use function array_pop;
use function count;
use function explode;
use function implode;
use function is_string;
use function sprintf;

/**
 * Class ErrorUris
 *
 * This class is responsible for managing error URIs for different parameter types.
 * It allows adding error URIs for specific parameter types and retrieving the appropriate
 * error URI based on the parameter type. The search for error URIs is done by looking
 * for an exact match first, then by looking for parent types by removing the last part
 * of the parameter type until a match is found or there are no more parts to remove.
 */
class ErrorUris
{
    /**
     * @var array<non-empty-string, non-empty-string> $errorsURIs
     */
    private static array $errorsURIs = [];

    /**
     * Add error uri for a parameter type
     *
     * @param non-empty-string $parameterType
     * @param non-empty-string|UriInterface $uri
     * @return void
     */
    public static function addErrorUri(string $parameterType, string|UriInterface $uri): void
    {
        if ($parameterType === '') {
            throw new UnsatisfiedParameterException(
                'Parameter type cannot be empty'
            );
        }
        if ($uri instanceof UriInterface) {
            $uri = (string)$uri;
        }
        if ($uri === '') {
            throw new UnsatisfiedParameterException(
                'Uri cannot be empty'
            );
        }
        self::$errorsURIs[$parameterType] = (string)$uri;
    }

    /**
     * Search for error uri for the given parameter type.
     * The search is done by looking for the exact match first,
     * then by looking for the parent types by removing the last part of
     * the parameter type until a match is found or there are no more parts to remove.
     * For example, if the parameter type is "grant_type:authorization_code",
     * the search will look for "grant_type:authorization_code" first, then "grant_type",
     * and finally "" (empty string) if no match is found.
     *
     * @param string $parameterType
     * @return string|null the url error uri
     */
    public static function errorUriFor(string $parameterType): ?string
    {
        $errorUris = self::getErrorsURIs();
        if ([] === $errorUris) {
            return null;
        }
        if (isset($errorUris[$parameterType])) {
            return $errorUris[$parameterType];
        }
        $split = explode(':', $parameterType);
        if (count($split) < 2) {
            return null;
        }
        while (count($split) > 0) {
            array_pop($split);
            $type = implode(':', $split);
            if ($type === '') {
                return null;
            }
            if (isset($errorUris[$type])) {
                return $errorUris[$type];
            }
        }
        return null;
    }

    /**
     * @return array<non-empty-string, string>
     */
    public static function getErrorsURIs(): array
    {
        return self::$errorsURIs;
    }

    /**
     * Set error url list
     *
     * @param array<non-empty-string, non-empty-string|UriInterface> $uris
     * @return void
     */
    public static function setErrorUris(array $uris): void
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
                $uri = (string)$uri;
            }
            if (!is_string($uri)) {
                throw new UnsatisfiedParameterException(
                    sprintf(
                        'Uri for %s is invalid',
                        $key
                    )
                );
            }
            if ($uri === '') {
                throw new UnsatisfiedParameterException(
                    sprintf(
                        'Uri for %s is empty',
                        $key
                    )
                );
            }
            $urls[$key] = $uri;
        }
        self::$errorsURIs = $urls;
    }
}
