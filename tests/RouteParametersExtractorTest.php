<?php
declare(strict_types=1);

namespace GimmeUrl\Tests;

use FastRoute\DataGenerator;
use FastRoute\DataGenerator\CharCountBased;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\DataGenerator\GroupPosBased;
use FastRoute\DataGenerator\MarkBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use GimmeUrl\RouteParametersExtractor;
use League\Route\Route;
use PHPUnit\Framework\TestCase;

final class RouteParametersExtractorTest extends TestCase
{
    /**
     * @dataProvider requiredAndOptionalDataProvider
     *
     * @param string $path
     * @param array $required
     * @param array $optional
     */
    public function testGetRequiredAndOptional(string $path, array $required, array $optional): void
    {
        $generators = [
            new CharCountBased(),
            new GroupCountBased(),
            new GroupPosBased(),
            new MarkBased()
        ];

        $route = new Route('GET', $path, null);

        foreach ($generators as $generator) {
            $data = static::data($route, $generator);
            $extractor = new RouteParametersExtractor($data[1] ?? []);

            static::assertEquals($required, $extractor->getRequired($route));
            static::assertEquals($optional, $extractor->getOptional($route));
        }
    }

    public function requiredAndOptionalDataProvider(): array
    {
        return [
            ['/{foo}/{bar}', ['foo', 'bar'], []],
            ['/{foo}[/{bar}]', ['foo'], ['bar']],
            ['/foo/bar', [], []],
        ];
    }

    private static function data(Route $route, DataGenerator $generator): array
    {
        $collector = new RouteCollector(new Std(), $generator);
        $collector->addRoute($route->getMethod(), $route->getPath(), $route);

        return $collector->getData();
    }
}
