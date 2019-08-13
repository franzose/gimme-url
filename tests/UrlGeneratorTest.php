<?php
declare(strict_types=1);

namespace GimmeUrl\Tests;

use GimmeUrl\Exception\RouteParameterMissingException;
use GimmeUrl\RequestContext;
use GimmeUrl\RouteMetadata;
use GimmeUrl\Router;
use GimmeUrl\UrlGenerator;
use League\Route\Route;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

final class UrlGeneratorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @dataProvider relativeDataProvider
     * @param string $name
     * @param array $parameters
     * @param array $required
     * @param array $optional
     * @param string $actualPath
     * @param string $generatedPath
     */
    public function testRelative(
        string $name,
        array $parameters,
        array $required,
        array $optional,
        string $actualPath,
        string $generatedPath
    ): void {
        $route = new Route('GET', $actualPath, '');
        $route->setName($name);

        $router = static::router($route, $required, $optional);

        $generator = new UrlGenerator($router, Mockery::mock(RequestContext::class));

        static::assertEquals($generatedPath, $generator->relative($name, $parameters));
    }

    public function relativeDataProvider(): array
    {
        return [
            ['foo', [], [], [], '/foo/bar', '/foo/bar'],
            ['foo', ['bar' => '123'], ['bar'], [], '/foo/{bar}', '/foo/123'],
            ['foo', ['bar' => '123', 'qux' => '456'], [], [], '/foo', '/foo?bar=123&qux=456']
        ];
    }

    public function testRelativeShouldThrowException(): void
    {
        $this->expectException(RouteParameterMissingException::class);

        $route = new Route('GET', '/foo/{bar}/{qux}', '');
        $route->setName('foo');

        $generator = new UrlGenerator(
            static::router($route, ['foo', 'bar'], []),
            Mockery::mock(RequestContext::class)
        );

        $generator->relative('foo');
    }

    public function testAbsolute(): void
    {
        $route = new Route('GET', '/foo/{bar}', '');
        $route->setName('foo');
        $route->setVars(['bar' => '123']);

        $router = static::router($route, ['bar'], []);

        $context = Mockery::mock(RequestContext::class);
        $context
            ->shouldReceive('buildBaseUrl')
            ->once()
            ->andReturn('http://localhost:9999');

        $generator = new UrlGenerator($router, $context);

        static::assertEquals(
            'http://localhost:9999/foo/123',
            $generator->absolute('foo', ['bar' => '123'])
        );
    }

    private static function router(Route $route, array $required, array $optional)
    {
        $router = Mockery::mock(Router::class);
        $router
            ->shouldReceive('getNamedRoute')
            ->once()
            ->with($route->getName())
            ->andReturn($route);

        $router
            ->shouldReceive('getRouteMetadata')
            ->once()
            ->with($route)
            ->andReturn(new RouteMetadata($route, $required, $optional));

        return $router;
    }
}
