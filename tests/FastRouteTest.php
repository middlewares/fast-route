<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use function FastRoute\simpleDispatcher;
use Middlewares\FastRoute;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class FastRouteTest extends TestCase
{
    public function testFastRouteNotFound(): void
    {
        $dispatcher = simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->get('/users', 'listUsers');
        });

        $request = Factory::createServerRequest('GET', '/posts');

        $response = Dispatcher::run([
            new FastRoute($dispatcher),
        ], $request);

        self::assertEquals(404, $response->getStatusCode());
    }

    public function testFastRouteNotAllowed(): void
    {
        $dispatcher = simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->get('/users', 'listUsers');
            $r->post('/users', 'createUser');
        });

        $request = Factory::createServerRequest('DELETE', '/users');

        $response = Dispatcher::run([
            new FastRoute($dispatcher),
        ], $request);

        self::assertEquals(405, $response->getStatusCode());
        self::assertEquals('GET, POST', $response->getHeaderLine('Allow'));
    }

    public function testFastRouteOK(): void
    {
        $dispatcher = simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->get('/users/{name}', 'listUsers');
        });

        $request = Factory::createServerRequest('GET', '/users/alice');

        $response = Dispatcher::run([
            new FastRoute($dispatcher),
            function ($request) {
                echo $request->getAttribute('request-handler');
                echo $request->getAttribute('name');
            },
        ], $request);

        self::assertEquals('listUsersalice', (string) $response->getBody());
    }

    public function testAccent(): void
    {
        $dispatcher = simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->get('/hello/accentué', 'OK');
        });

        $request = Factory::createServerRequest('GET', '/hello/accentu%C3%A9');

        $response = Dispatcher::run([
            new FastRoute($dispatcher),
            function ($request) {
                echo $request->getAttribute('request-handler');
            },
        ], $request);

        self::assertEquals('OK', (string) $response->getBody());
    }

    public function testFastRouteCustomAttribute(): void
    {
        $dispatcher = simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->get('/users', 'listUsers');
        });

        $request = Factory::createServerRequest('GET', '/users');

        $response = Dispatcher::run([
            (new FastRoute($dispatcher))->attribute('handler'),
            function ($request) {
                echo $request->getAttribute('handler');
            },
        ], $request);

        self::assertEquals('listUsers', (string) $response->getBody());
    }
}
