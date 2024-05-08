<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleFormHandler;
use Hostnet\Component\FormHandler\HandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller testing fixture.
 */
class TestController
{
    public function __construct(
        private readonly HandlerFactoryInterface $handler_factory,
    ) {
    }

    public function action(Request $request)
    {
        $handler  = $this->handler_factory->create(SimpleFormHandler::class);
        $response = $handler->handle($request);
        if ($response instanceof Response) {
            return $response;
        }

        return new Response('test');
    }
}
