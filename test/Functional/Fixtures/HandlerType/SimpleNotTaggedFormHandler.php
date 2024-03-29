<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestType;
use Hostnet\Component\FormHandler\HandlerConfigInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;

class SimpleNotTaggedFormHandler implements HandlerTypeInterface
{
    public function configure(HandlerConfigInterface $config): void
    {
        $config->setType(TestType::class);
    }
}
