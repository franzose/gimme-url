<?php
declare(strict_types=1);

namespace GimmeUrl\Tests;

use GimmeUrl\Router;
use GimmeUrl\Tests\Stub\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

final class RouterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testGetRouteMetadata(): void
    {
        $router = new Router();
        $router->get('/{foo}[/{qux}]', static function (): ResponseInterface {
            return new Response();
        })->setName('home');

        $router->dispatch(static::request('/foo/qux'));

        $metadata = $router->getRouteMetadata($router->getNamedRoute('home'));

        static::assertEquals('home', $metadata->getName());
        static::assertEquals('/{foo}[/{qux}]', $metadata->getPath());
        static::assertEquals(['foo'], $metadata->getRequiredParameters());
        static::assertEquals(['qux'], $metadata->getOptionalParameters());
    }

    private static function request(string $path)
    {
        $uri = Mockery::mock(UriInterface::class);
        $uri->shouldReceive('getPath')
            ->twice()
            ->andReturn($path);

        $request = Mockery::mock(ServerRequestInterface::class);
        $request
            ->shouldReceive('getUri')
            ->twice()
            ->andReturn($uri);

        $request
            ->shouldReceive('getMethod')
            ->once()
            ->andReturn('GET');

        return $request;
    }
}
