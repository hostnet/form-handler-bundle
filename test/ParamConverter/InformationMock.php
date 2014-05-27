<?php
namespace Hostnet\Bundle\FormHandlerBundle\ParamConverter;

use Hostnet\Component\Form\FormInformationInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 */
class InformationMock implements FormInformationInterface
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