<?php

namespace Middlewares;

use Middlewares\Utils\CallableHandler;
use Middlewares\Utils\CallableResolver\CallableResolverInterface;
use Middlewares\Utils\CallableResolver\ReflectionResolver;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

class FastRouteAction implements MiddlewareInterface
{
    /**
     * @var array Extra arguments passed to the controller
     */
    private $arguments = [];

    /**
     * @var CallableResolverInterface Used to resolve the controllers
     */
    private $resolver;

    /**
     * Set the resolver instance.
     *
     * @param CallableResolverInterface $resolver
     */
    public function __construct(CallableResolverInterface $resolver = null)
    {
        if (empty($resolver)) {
            $resolver = new ReflectionResolver();
        }

        $this->resolver = $resolver;
    }

    /**
     * Extra arguments passed to the callable.
     *
     * @return self
     */
    public function arguments(...$args)
    {
        $this->arguments = $args;

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
        $arguments = array_merge([$request], $this->arguments);

        $controller = $this->getController($request);
        $callable = $this->resolver->resolve($controller, $arguments);

        return CallableHandler::execute($callable, $arguments);
    }

    /**
     * Return the controller reference.
     *
     * @param ServerRequestInterface $request
     *
     * @return callable|string|array
     */
    protected function getController(ServerRequestInterface $request)
    {
        return $request->getAttribute('controller');
    }
}
