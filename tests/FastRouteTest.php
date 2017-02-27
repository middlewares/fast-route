<?php

namespace Middlewares\Tests;

use Psr\Container\ContainerInterface;
use Middlewares\FastRoute;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ServerRequestInterface;

class FastRouteTest extends \PHPUnit_Framework_TestCase
{
    public function testFastRouteOK()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/user/{name}/{id:[0-9]+}', function ($request) {
                echo sprintf(
                    'Hello %s (%s)',
                    $request->getAttribute('name'),
                    $request->getAttribute('id')
                );
            });
        });

        $request = Factory::createServerRequest([], 'GET', 'http://domain.com/user/oscarotero/35');

        $response = Dispatcher::run([
            new FastRoute($dispatcher),
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('Hello oscarotero (35)', (string) $response->getBody());
    }

    public function testFastRouteNotFound()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/user/{name}/{id:[0-9]+}', function ($request) {
                echo sprintf(
                    'Hello %s (%s)',
                    $request->getAttribute('name'),
                    $request->getAttribute('id')
                );
            });
        });

        $request = Factory::createServerRequest([], 'GET', 'http://domain.com/username/oscarotero/35');

        $response = Dispatcher::run([
            new FastRoute($dispatcher),
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testFastRouteNotAllowed()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->addRoute('POST', '/user/{name}/{id:[0-9]+}', function ($request) {
                return sprintf(
                    'Hello %s (%s)',
                    $request->getAttribute('name'),
                    $request->getAttribute('id')
                );
            });
        });

        $request = Factory::createServerRequest([], 'GET', 'http://domain.com/user/oscarotero/35');

        $response = Dispatcher::run([
            new FastRoute($dispatcher),
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testFastRouteContainerResolve()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->addRoute('POST', '/user/{name}/{id:[0-9]+}', 'controller');
        });

        $request = Factory::createServerRequest([], 'POST', 'http://domain.com/user/oscarotero/35');

        /** @var ContainerInterface|ObjectProphecy $resolver */
        $resolver = $this->prophesize(ContainerInterface::class);
        $resolver->get('controller')->willReturn(function ($request) {
            return sprintf(
                'Hello %s (%s)',
                $request->getAttribute('name'),
                $request->getAttribute('id')
            );
        });

        $middleware = new FastRoute($dispatcher);
        $middleware->resolver($resolver->reveal());

        $response = Dispatcher::run([
            $middleware,
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals('Hello oscarotero (35)', (string) $response->getBody());
    }
}
