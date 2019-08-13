<?php
declare(strict_types=1);

namespace GimmeUrl\Tests;

use GimmeUrl\RouteMetadata;
use League\Route\Route;
use PHPUnit\Framework\TestCase;

final class RouteMetadataTest extends TestCase
{
    public function testIsParameterRequired(): void
    {
        $route = new Route('GET', '/{foo}', null);
        $metadata = new RouteMetadata($route, ['foo'], []);

        static::assertTrue($metadata->isParameterRequired('foo'));
        static::assertFalse($metadata->isParameterRequired('bar'));
    }

    public function testIsParameterOptional(): void
    {
        $route = new Route('GET', '/{foo}[/{bar}]', null);
        $metadata = new RouteMetadata($route, [], ['bar']);

        static::assertTrue($metadata->isParameterOptional('bar'));
        static::assertFalse($metadata->isParameterOptional('foo'));
    }

    public function testGetQueryStringParameters(): void
    {
        $route = new Route('GET', '/{foo}', null);
        $metadata = new RouteMetadata($route, ['foo'], []);

        $given = [
            'foo' => '123',
            'bar' => '456',
            'qux' => '789'
        ];

        $expected = [
            'bar' => '456',
            'qux' => '789'
        ];

        static::assertEquals($expected, $metadata->getQueryStringParameters($given));
    }
}
