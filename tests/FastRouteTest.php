<?php

namespace Middlewares\Tests;

use Middlewares\FastRoute;
use Zend\Diactoros\ServerRequest;
use mindplay\middleman\Dispatcher;

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

        $response = (new Dispatcher([
            new FastRoute($dispatcher),
        ]))->dispatch(new ServerRequest([], [], 'http://domain.com/user/oscarotero/35'));

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

        $response = (new Dispatcher([
            new FastRoute($dispatcher),
        ]))->dispatch(new ServerRequest([], [], 'http://domain.com/username/oscarotero/35'));

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

        $response = (new Dispatcher([
            new FastRoute($dispatcher),
        ]))->dispatch(new ServerRequest([], [], 'http://domain.com/user/oscarotero/35'));

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals(405, $response->getStatusCode());
    }
}
