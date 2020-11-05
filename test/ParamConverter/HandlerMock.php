<?php
/**
 * @copyright 2014-2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\ParamConverter;

use Hostnet\Component\Form\FormHandlerInterface;
use Symfony\Component\Form\FormInterface;

class HandlerMock implements FormHandlerInterface
{
    public function getType()
    {
        return 'henk';
    }

    public function getData()
    {
        return new \stdClass();
    }

    public function getOptions()
    {
        return [];
    }

    public function getForm()
    {
    }

    public function setForm(FormInterface $form)
    {
    }
}
