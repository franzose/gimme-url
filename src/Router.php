<?php
declare(strict_types=1);

namespace GimmeUrl;

use League\Route\Route;
use League\Route\Router as LeaguerRoter;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Extended router gathers route metadata used by URL generator.
 */
class Router extends LeaguerRoter
{
    /**
     * @var array|RouteMetadata[]
     */
    private $metadata = [];

    public function getRouteMetadata(Route $route): RouteMetadata
    {
        return $this->metadata[$this->getMetadataKey($route)];
    }

    protected function prepRoutes(ServerRequestInterface $request): void
    {
        parent::prepRoutes($request);

        $this->createMetadata();
    }

    private function createMetadata(): void
    {
        // Pass parameterized routes data only
        $extractor = new RouteParametersExtractor($this->getData()[1] ?? []);

        $routes = array_merge($this->routes, $this->namedRoutes);

        foreach ($routes as $route) {
            $key = $this->getMetadataKey($route);
            $required = $extractor->getRequired($route);
            $optional = $extractor->getOptional($route);

            $this->metadata[$key] = new RouteMetadata($route, $required, $optional);
        }
    }

    private function getMetadataKey(Route $route): string
    {
        return $route->getName() ?? spl_object_hash($route);
    }
}
