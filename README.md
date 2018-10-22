# middlewares/fast-route

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to use [FastRoute](https://github.com/nikic/FastRoute) for handler discovery.

## Requirements

* PHP >= 7.0
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/fast-route](https://packagist.org/packages/middlewares/fast-route).

```sh
composer require middlewares/fast-route
```

You may also want to install [middlewares/request-handler](https://packagist.org/packages/middlewares/request-handler).

## Example

This example uses [middleware/request-handler](https://github.com/middlewares/request-handler) to execute the route handler:

```php
//Create the router dispatcher
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/hello/{name}', function ($request) {
        //The route parameters are stored as attributes
        $name = $request->getAttribute('name');

        //You can echo the output (it will be captured and written into the body)
        echo sprintf('Hello %s', $name);

        //Or return a string
        return sprintf('Hello %s', $name);

        //Or return a response
        return new Response();
    });
});

$dispatcher = new Dispatcher([
    new Middlewares\FastRoute($dispatcher),
    new Middlewares\RequestHandler()
]);

$response = $dispatcher->dispatch(new ServerRequest('/hello/world'));
```

**FastRoute** allows anything to be defined as the router handler (a closure, callback, action object, controller class, etc). The middleware will store this handler in a request attribute.

## API

### `__construct`

Type | Required | Description
--------------|----------|------------
`FastRoute\Dispatcher` | Yes | The dispatcher instance to use.
`Psr\Http\Message\ResponseFactoryInterface` | No | A PSR-17 factory to create the error responses (`404` or `405`). If it's not defined, use [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) to detect it automatically.


### `attribute`

Changes the attribute name used to store the handler in the server request. The default name is `request-handler`.

Type | Required | Description
--------------|----------|------------
`string` | Yes | The new attribute name

```php
$dispatcher = new Dispatcher([
    //Save the route handler in an attribute called "route"
    (new Middlewares\FastRoute($dispatcher))->attribute('route'),

    //Execute the route handler
    (new Middlewares\RequestHandler())->attribute('route')
]);

$response = $dispatcher->dispatch(new ServerRequest('/hello/world'));
```
---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/fast-route.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/fast-route/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/fast-route.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/fast-route.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/bb44398f-43ee-4a09-a60e-d5c9735fa0be.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/fast-route
[link-travis]: https://travis-ci.org/middlewares/fast-route
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/fast-route
[link-downloads]: https://packagist.org/packages/middlewares/fast-route
[link-sensiolabs]: https://insight.sensiolabs.com/projects/bb44398f-43ee-4a09-a60e-d5c9735fa0be
