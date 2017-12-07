<?php
/**
 * @copyright 2017 Hostnet B.V.
 */

namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Controller test.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class ControllerTest extends KernelTestCase
{
    protected function setUp()
    {
        static::bootKernel();
    }

    public function test()
    {
        if (Kernel::VERSION_ID < 30300) {
            self::markTestSkipped(sprintf('Symfony version %s not supported by test', Kernel::VERSION));
        }
        if (!interface_exists('Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface')) {
            $this->markTestSkipped(
                'Sensio Extra bundle is not installed.'
            );
        }

        $container = self::$kernel->getContainer();
        $container->get(TestController::class);

        // We only care that the controller instantiates correctly
        $this->addToAssertionCount(1);
    }
}
