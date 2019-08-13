<?php
declare(strict_types=1);

namespace GimmeUrl\Tests;

use GimmeUrl\RequestContext;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

final class RequestContextTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @dataProvider buildBaseUrlDataProvider
     *
     * @param string $scheme
     * @param string $host
     * @param int|null $port
     * @param string $expectedUrl
     */
    public function testBuildBaseUrl(
        string $scheme,
        string $host,
        ?int $port,
        string $expectedUrl
    ): void {
        $uri = static::uri($scheme, $host, $port);
        $request = static::request($uri);
        $context = RequestContext::fromRequest($request);

        static::assertEquals($expectedUrl, $context->buildBaseUrl());
    }

    public function buildBaseUrlDataProvider(): array
    {
        return [
            ['http', 'localhost', 9999, 'http://localhost:9999'],
            ['http', 'localhost', null, 'http://localhost'],
            ['https', 'localhost', 444, 'https://localhost:444'],
            ['https', 'localhost', null, 'https://localhost'],
        ];
    }

    private static function uri(string $scheme, string $host, ?int $port)
    {
        $uri = Mockery::mock(UriInterface::class);
        $uri->shouldReceive('getScheme')
            ->once()
            ->andReturn($scheme);

        $uri->shouldReceive('getHost')
            ->once()
            ->andReturn($host);

        $uri->shouldReceive('getPort')
            ->once()
            ->andReturn($port);

        return $uri;
    }

    private static function request($uri)
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $request
            ->shouldReceive('getUri')
            ->once()
            ->andReturn($uri);

        $request
            ->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['HTTPS' => 'off']);

        return $request;
    }
}
