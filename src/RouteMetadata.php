<?php
declare(strict_types=1);

namespace GimmeUrl;

use League\Route\Route;

/**
 * Route metadata holds information missing from the original League route.
 */
final class RouteMetadata
{
    private $name;
    private $path;
    private $required;
    private $optional;

    /**
     * @param Route $route The route
     * @param array $required Required parameters
     * @param array $optional Optional parameters
     */
    public function __construct(
        Route $route,
        array $required,
        array $optional
    ) {
        $this->name = $route->getName();
        $this->path = $route->getPath();
        $this->required = $required;
        $this->optional = $optional;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRequiredParameters(): array
    {
        return $this->required;
    }

    public function isParameterRequired(string $parameter): bool
    {
        return in_array($parameter, $this->getRequiredParameters(), true);
    }

    public function getOptionalParameters(): array
    {
        return $this->optional;
    }

    public function isParameterOptional(string $parameter): bool
    {
        return in_array($parameter, $this->getOptionalParameters(), true);
    }

    /**
     * Returns parameters that aren't either required
     * or optional and should go to the query string.
     *
     * @param array $given
     *
     * @return array
     */
    public function getQueryStringParameters(array $given): array
    {
        $all = array_flip(array_merge($this->required, $this->optional));

        return array_diff_key($given, $all);
    }
}
