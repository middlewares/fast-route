<?php

namespace Middlewares\Tests;

use Psr\Container\ContainerInterface;
use Middlewares\FastRoute;
use Middlewares\Utils\CallableResolver\CallableResolverInterface;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use Prophecy\Argument;
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

            $r->addRoute('PUT', '/user/{name}/{id:[0-9]+}', function ($request) {
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

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('POST, PUT', $response->getHeaderLine('Allow'));
    }

    public function testFastRouteResolver()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->addRoute('POST', '/user/{name}/{id:[0-9]+}', 'controller');
        });

        $request = Factory::createServerRequest([], 'POST', 'http://domain.com/user/oscarotero/35');

        /** @var CallableResolverInterface|ObjectProphecy $resolver */
        $resolver = $this->prophesize(CallableResolverInterface::class);
        $resolver->resolve('controller', Argument::cetera())->willReturn(function ($request) {
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

        $this->assertEquals('Hello oscarotero (35)', (string) $response->getBody());
    }

    public function testFastRouteContainerResolver()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->addRoute('POST', '/user/{name}/{id:[0-9]+}', 'controller');
        });

        $request = Factory::createServerRequest([], 'POST', 'http://domain.com/user/oscarotero/35');

        /** @var ContainerInterface|ObjectProphecy $container */
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('controller')->willReturn(function ($request) {
            return sprintf(
                'Hello %s (%s)',
                $request->getAttribute('name'),
                $request->getAttribute('id')
            );
        });

        $middleware = new FastRoute($dispatcher);
        $middleware->container($container->reveal());

        $response = Dispatcher::run([
            $middleware,
        ], $request);

        $this->assertEquals('Hello oscarotero (35)', (string) $response->getBody());
    }
}
