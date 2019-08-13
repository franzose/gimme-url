<?php
declare(strict_types=1);

namespace GimmeUrl;

use FastRoute\RouteParser\Std;
use GimmeUrl\Exception\RouteParameterMissingException;

final class UrlGenerator implements UrlGeneratorInterface
{
    private $router;
    private $context;

    public function __construct(Router $router, RequestContext $context)
    {
        $this->router = $router;
        $this->context = $context;
    }

    public function relative(string $route, array $parameters = []): string
    {
        $metadata = $this->router->getRouteMetadata($this->router->getNamedRoute($route));

        $path = static::buildPath($metadata, $parameters);

        return $path . static::buildQueryString($metadata, $parameters);
    }

    public function absolute(string $route, array $parameters = []): string
    {
        return $this->context->buildBaseUrl() . $this->relative($route, $parameters);
    }

    private static function buildPath(RouteMetadata $metadata, array $parameters): string
    {
        // Replace each route parameter by the corresponding value
        // Idea was borrowed from here:
        // https://github.com/Xocotlah/SmartController/blob/1e47baf3808251029418df05fb56d5b2436e7391/src/SmartController.php#L77
        $result = preg_replace_callback(
            sprintf('/%s/x', Std::VARIABLE_REGEX),
            static::replacer($metadata, $parameters),
            $metadata->getPath()
        );

        // replace all redundant slashes and square brackets
        return rtrim(preg_replace('/(?:\[(\/)|(\/){2,}|\])/', '\\1\\2', $result), '/');
    }

    private static function buildQueryString(RouteMetadata $metadata, array $parameters): string
    {
        $query = $metadata->getQueryStringParameters($parameters);

        return empty($query) ? '' : '?' . http_build_query($query);
    }

    /**
     * Replaces route parameters by the corresponding values.
     *
     * @param RouteMetadata $metadata
     * @param array $given Route parameters passed to the URL generator
     *
     * @return callable
     */
    private static function replacer(RouteMetadata $metadata, array $given): callable
    {
        return static function ($matches) use ($metadata, $given) {
            if (!array_key_exists($matches[1], $given) &&
                $metadata->isParameterRequired($matches[1])) {
                throw RouteParameterMissingException::forRoute($metadata, $matches[1]);
            }

            return $given[$matches[1]] ?? '';
        };
    }
}
