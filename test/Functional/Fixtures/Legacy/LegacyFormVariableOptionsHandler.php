<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestData;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestType;
use Hostnet\Component\Form\AbstractFormHandler;
use Hostnet\Component\Form\FormFailureHandlerInterface;
use Hostnet\Component\Form\FormSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class LegacyFormVariableOptionsHandler extends AbstractFormHandler implements
    FormSuccessHandlerInterface,
    FormFailureHandlerInterface
{
    private $data;

    public function __construct()
    {
        $this->data = new TestData();
    }

    public function getOptions()
    {
        return ['attr' => ['class' => $this->data->test]];
    }

    public function getType()
    {
        return TestType::class;
    }

    public function getData()
    {
        return $this->data;
    }

    public function onSuccess(Request $request)
    {
        return new RedirectResponse('http://success.nl/' . $this->data->test);
    }

    public function onFailure(Request $request)
    {
        return new RedirectResponse('http://failure.nl/' . $this->data->test);
    }
}
