# Gimme URL
Gimme URL is a missing URL generator for the League Route library. It's capable to generate relative and absolute paths to named routes.

## Installation
Use Composer to install Gimme URL:

```bash
composer install franzose/gimme-url
```

## Setup and usage

URL generator requires you to provide Router and RequestContext instances. The latter gathers information from a Psr\Http\Message\ServerRequestInterface instance and is used to build absolute paths to named routes.

```php
<?php

use GimmeUrl\RequestContext;
use GimmeUrl\Router;
use GimmeUrl\UrlGenerator;

$router = new Router();
$router->get('/foo/{bar}', function () {
    //
})->setName('foo_route');

// Let's say the request is secure and is made at example.com on 8080 port 
$context = RequestContext::fromRequest($serverRequest);
$generator = new UrlGenerator($router, $context);

// Then you'll get this
$generator->relative('foo_route', ['bar' => '123']); // '/foo/123'
$generator->relative('foo_route', ['bar' => '123', 'qux' => 'doo']); // '/foo/123?qux=doo'
$generator->absolute('foo_route', ['bar' => '456']); // 'https://example.com:8080/foo/456'
$generator->absolute('foo_route', ['bar' => '456', 'qux' => 'doo']); // 'https://example.com:8080/foo/456?qux=doo'
```
