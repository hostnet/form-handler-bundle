<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestController;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Controller test.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class ControllerTest extends KernelTestCase
{
    /**
     * BC for current tests, new tests should get their own config.
     */
    protected function setUp()
    {
        static::bootKernel(['config_file' => TestKernel::getLegacyConfigFilename()]);
    }

    protected static function createKernel(array $options = array())
    {
        return new TestKernel($options);
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
