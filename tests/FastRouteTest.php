<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\FastRoute;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;
use function FastRoute\simpleDispatcher;

class FastRouteTest extends TestCase
{
    public function testFastRouteNotFound()
    {
        $dispatcher = simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->get('/users', 'listUsers');
        });

        $request = Factory::createServerRequest([], 'GET', '/posts');

        $response = Dispatcher::run([
            new FastRoute($dispatcher),
        ], $request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testFastRouteNotAllowed()
    {
        $dispatcher = simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->get('/users', 'listUsers');
            $r->post('/users', 'createUser');
        });

        $request = Factory::createServerRequest([], 'DELETE', '/users');

        $response = Dispatcher::run([
            new FastRoute($dispatcher),
        ], $request);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertEquals('GET, POST', $response->getHeaderLine('Allow'));
    }

    public function testFastRouteOK()
    {
        $dispatcher = simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->get('/users/{name}', 'listUsers');
        });

        $request = Factory::createServerRequest([], 'GET', '/users/alice');

        $response = Dispatcher::run([
            new FastRoute($dispatcher),
            function ($request) {
                echo $request->getAttribute('request-handler');
                echo $request->getAttribute('name');
            },
        ], $request);

        $this->assertEquals('listUsersalice', (string) $response->getBody());
    }

    public function testFastRouteCustomAttribute()
    {
        $dispatcher = simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->get('/users', 'listUsers');
        });

        $request = Factory::createServerRequest([], 'GET', '/users');

        $response = Dispatcher::run([
            (new FastRoute($dispatcher))->attribute('handler'),
            function ($request) {
                echo $request->getAttribute('handler');
            },
        ], $request);

        $this->assertEquals('listUsers', (string) $response->getBody());
    }
}
