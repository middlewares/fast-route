<?php

namespace Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Interop\Http\Middleware\DelegateInterface;
use FastRoute\Dispatcher;

class FastRoute implements ServerMiddlewareInterface
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
     * @var ContainerInterface Used to resolve the controllers
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
     * @param ContainerInterface $resolver
     *
     * @return self
     */
    public function resolver(ContainerInterface $resolver)
    {
        $this->resolver = $resolver;

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

        if ($this->resolver) {
            $callable = $this->resolver->get($route[1]);
        } else {
            $callable = Utils\CallableHandler::resolve($route[1], $arguments);
        }

        return Utils\CallableHandler::execute($callable, $arguments);
    }
}
