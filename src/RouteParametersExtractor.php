<?php
declare(strict_types=1);

namespace GimmeUrl;

use League\Route\Route;

/**
 * Extractor retrieves required and optional route parameters
 * from the internal data structure used by FastRoute.
 *
 * @see \FastRoute\DataGenerator\CharCountBased
 * @see \FastRoute\DataGenerator\GroupCountBased
 * @see \FastRoute\DataGenerator\GroupPosBased
 * @see \FastRoute\DataGenerator\MarkBased
 */
final class RouteParametersExtractor
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getRequired(Route $route): array
    {
        return array_keys($this->getRouteData($route)[0][1] ?? []);
    }

    public function getOptional(Route $route): array
    {
        $all = array_keys($this->getRouteData($route)[1][1] ?? []);

        return array_values(array_diff($all, $this->getRequired($route)));
    }

    private function getRouteData(Route $route): array
    {
        $map = $this->data[$route->getMethod()][0]['routeMap'] ?? [];

        $filter = static function ($data) use ($route): bool {
            return $data[0] instanceof Route && $data[0]->getName() === $route->getName();
        };

        return array_values(array_filter($map, $filter));
    }
}
