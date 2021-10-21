# middlewares/fast-route

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to use [FastRoute](https://github.com/nikic/FastRoute) for handler discovery.

## Requirements

* PHP >= 7.2
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/fast-route](https://packagist.org/packages/middlewares/fast-route).

```sh
composer require middlewares/fast-route
```

You may also want to install [middlewares/request-handler](https://packagist.org/packages/middlewares/request-handler).

## Example

This example uses [middlewares/request-handler](https://github.com/middlewares/request-handler) to execute the route handler:

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

## Usage

Create the middleware with a `FastRoute\Dispatcher` instance:

```php
$route = new Middlewares\FastRoute($dispatcher);
```

Optionally, you can provide a `Psr\Http\Message\ResponseFactoryInterface` as the second argument, that will be used to create the error responses (`404` or `405`). If it's not defined, [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) will be used to detect it automatically.

```php
$responseFactory = new MyOwnResponseFactory();

$route = new Middlewares\FastRoute($dispatcher, $responseFactory);
```

### attribute

Changes the attribute name used to store the handler in the server request. The default name is `request-handler`.

```php
$dispatcher = new Dispatcher([
    //Save the route handler in an attribute called "route"
    (new Middlewares\FastRoute($dispatcher))->attribute('route'),

    //Execute the route handler
    (new Middlewares\RequestHandler())->handlerAttribute('route')
]);
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/fast-route.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/fast-route/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/fast-route.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/fast-route
[link-downloads]: https://packagist.org/packages/middlewares/fast-route
