<?php
declare(strict_types=1);

namespace GimmeUrl;

use GimmeUrl\Exception\RouteParameterMissingException;

interface UrlGeneratorInterface
{
    /**
     * Generates a relative path to a route.
     *
     * Given the following route:
     *
     *     $router->get('/foo/{param1}', function () {});
     *
     * A relative path can be generated as following:
     *
     *     $path = $generator->relative('foo', ['param1' => 'value1']);
     *     // '/foo/value1'
     *
     * Any parameters that does not directly belong
     * to the route will appear in the query string:
     *
     *     $path = $generator->relative('foo', ['param1' => 'value1', ['bar' => '123']);
     *     // '/foo/value1?bar=123'
     *
     * @param string $route Route name
     * @param array $parameters Route parameters
     *
     * @return string
     * @throws RouteParameterMissingException if a required parameter is missing
     */
    public function relative(string $route, array $parameters = []): string;

    /**
     * Generates a RequestContext based absolute path to a route.
     *
     * Given the following route:
     *
     *     $router->get('/foo/{param1}', function () {});
     *
     *
     * And the following context:
     *
     *     $context = RequestContext::fromRequest($serverRequest);
     *     // scheme: https
     *     // host: localhost
     *     // port: 9999
     *
     *
     * An absolute path can be generated as following:
     *
     *     $url = $generator->absolute('foo', ['param1' => 'value1']);
     *     // 'https://localhost:9999/foo/value1'
     *
     * Any parameters that does not directly belong
     * to the route will appear in the query string:
     *
     *     $path = $generator->absolute('foo', ['param1' => 'value1', ['bar' => '123']);
     *     // 'https://localhost:9999/foo/value1?bar=123'
     *
     * @param string $route Route name
     * @param array $parameters Route parameters
     *
     * @return string
     * @throws RouteParameterMissingException if a required parameter is missing
     */
    public function absolute(string $route, array $parameters = []): string;
}
