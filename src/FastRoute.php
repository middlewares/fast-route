<?php

namespace Middlewares;

use Middlewares\Utils\CallableResolver\CallableResolverInterface;
use Middlewares\Utils\CallableResolver\ContainerResolver;
use Middlewares\Utils\CallableResolver\ReflectionResolver;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Container\ContainerInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use FastRoute\Dispatcher;

class FastRoute implements MiddlewareInterface
{
    /**
     * @var Dispatcher FastRoute dispatcher
     */
    private $router;

    /**
     * @var array Extra arguments passed to the controller
     */
    private $arguments = [];

    /**
     * @var CallableResolverInterface Used to resolve the controllers
     */
    private $resolver;

    /**
     * Set the Dispatcher instance.
     *
     * @param Dispatcher $router
     */
    public function __construct(Dispatcher $router)
    {
        $this->router = $router;
    }

    /**
     * Set the resolver used to create the controllers.
     *
     * @param ContainerInterface $container
     *
     * @return self
     */
    public function resolver(ContainerInterface $container)
    {
        $this->resolver = new ContainerResolver($container);

        return $this;
    }

    /**
     * Extra arguments passed to the callable.
     *
     * @return self
     */
    public function arguments()
    {
        $this->arguments = func_get_args();

        return $this;
    }

    /**
     * Process a server request and return a response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $route = $this->router->dispatch($request->getMethod(), $request->getUri()->getPath());

        if ($route[0] === Dispatcher::NOT_FOUND) {
            return Utils\Factory::createResponse(404);
        }

        if ($route[0] === Dispatcher::METHOD_NOT_ALLOWED) {
            return Utils\Factory::createResponse(405);
        }

        foreach ($route[2] as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $arguments = array_merge([$request], $this->arguments);

        $callable = $this->getResolver()->resolve($route[1], $arguments);

        return Utils\CallableHandler::execute($callable, $arguments);
    }

    /**
     * Return the resolver used for the controllers
     *
     * @return CallableResolverInterface
     */
    private function getResolver()
    {
        if (!isset($this->resolver)) {
            $this->resolver = new ReflectionResolver();
        }

        return $this->resolver;
    }
}
