<?php
/**
 * @copyright 2017 Hostnet B.V.
 */

namespace Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleFormHandler;
use Hostnet\Bundle\FormHandlerBundle\ParamConverter\FormParamConverter;
use Hostnet\Bundle\FormHandlerBundle\Registry\LegacyHandlerRegistry;
use Hostnet\Component\Form\Simple\SimpleFormProvider;
use Hostnet\Component\FormHandler\HandlerFactory;
use Hostnet\Component\FormHandler\HandlerFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller testing fixture.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class TestController
{
    /** @var FormParamConverter */
    private $converter;
    /** @var SimpleFormProvider */
    private $provider;
    /** @var LegacyHandlerRegistry */
    private $registry;
    /** @var HandlerFactory */
    private $handler;

    public function __construct(
        FormParamConverter $converter,
        SimpleFormProvider $provider,
        LegacyHandlerRegistry $registry,
        HandlerFactory $handler
    ) {
        $this->converter = $converter;
        $this->provider  = $provider;
        $this->registry  = $registry;
        $this->handler   = $handler;
    }

    public function action(Request $request, HandlerFactoryInterface $factory)
    {
        $handler  = $factory->create(SimpleFormHandler::class);
        $response = $handler->handle($request);
        if ($response instanceof Response) {
            return $response;
        }

        return new Response('test');
    }
}
