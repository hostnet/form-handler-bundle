<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestType;
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;

class SimpleFormHandler implements HandlerTypeInterface
{
    public function configure(HandlerConfigInterface $config)
    {
        $config->setType(TestType::class);
    }
}
