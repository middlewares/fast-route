<?php
declare(strict_types = 1);

namespace Middlewares;

use FastRoute\Dispatcher;
use Middlewares\Utils\Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FastRoute implements MiddlewareInterface
{
    /**
     * @var Dispatcher FastRoute dispatcher
     */
    private $router;

    /**
     * @var string Attribute name for handler reference
     */
    private $attribute = 'request-handler';

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * Set the Dispatcher instance and optionally the response factory to return the error responses.
     */
    public function __construct(Dispatcher $router, ResponseFactoryInterface $responseFactory = null)
    {
        $this->router = $router;
        $this->responseFactory = $responseFactory ?: Factory::getResponseFactory();
    }

    /**
     * Set the attribute name to store handler reference.
     */
    public function attribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->dispatch($request->getMethod(), rawurldecode($request->getUri()->getPath()));

        if ($route[0] === Dispatcher::NOT_FOUND) {
            return $this->responseFactory->createResponse(404);
        }

        if ($route[0] === Dispatcher::METHOD_NOT_ALLOWED) {
            return $this->responseFactory->createResponse(405)->withHeader('Allow', implode(', ', $route[1]));
        }

        foreach ($route[2] as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        $request = $this->setHandler($request, $route[1]);

        return $handler->handle($request);
    }

    /**
     * Set the handler reference on the request.
     *
     * @param mixed $handler
     */
    protected function setHandler(ServerRequestInterface $request, $handler): ServerRequestInterface
    {
        return $request->withAttribute($this->attribute, $handler);
    }
}
