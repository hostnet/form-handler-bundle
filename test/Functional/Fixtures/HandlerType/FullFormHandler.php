<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestData;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestType;
use Hostnet\Component\FormHandler\ActionSubscriberInterface;
use Hostnet\Component\FormHandler\HandlerActions;
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class FullFormHandler implements HandlerTypeInterface, ActionSubscriberInterface
{
    public function configure(HandlerConfigInterface $config)
    {
        $config->setType(TestType::class);
        $config->registerActionSubscriber($this);
    }

    public function getSubscribedActions()
    {
        return [
            HandlerActions::SUCCESS => 'onSuccess',
            HandlerActions::FAILURE => 'onFailure',
        ];
    }

    public function onSuccess(TestData $data, FormInterface $form, Request $request)
    {
        return new RedirectResponse('http://success.nl/');
    }

    public function onFailure(TestData $data, FormInterface $form, Request $request)
    {
        return new RedirectResponse('http://failure.nl/');
    }
}
