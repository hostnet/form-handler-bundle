<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\FullFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleNotTaggedFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyFormVariableOptionsHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyNamedFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestKernel;
use Hostnet\Component\FormHandler\HandlerTypeAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Kernel;

class RegistryTest extends KernelTestCase
{
    /**
     * BC for current tests, new tests should get their own config.
     */
    protected function setUp()
    {
        $file = 'config_27.yml';

        if (Kernel::VERSION_ID >= 30300) {
            $file = 'config_33.yml';
        } elseif (Kernel::VERSION_ID >= 30000) {
            $file = 'config_32.yml';
        }

        static::bootKernel(['config_file' => $file]);
    }

    protected static function createKernel(array $options = array())
    {
        return new TestKernel($options);
    }

    public function test()
    {
        $container = self::$kernel->getContainer();
        $registry  = $container->get('hostnet.form_handler.registry');

        self::assertSame(
            $container->get('app.handler.type.full_form'),
            $registry->getType(FullFormHandler::class)
        );
        self::assertSame(
            $container->get('app.handler.type.simple_form'),
            $registry->getType(SimpleFormHandler::class)
        );

        // are the legacy variants wrapped?
        self::assertEquals(
            new HandlerTypeAdapter($container->get('app.handler.legacy.normal')),
            $registry->getType(LegacyFormHandler::class)
        );
        self::assertEquals(
            new HandlerTypeAdapter($container->get('app.handler.legacy.variable')),
            $registry->getType(LegacyFormVariableOptionsHandler::class)
        );
        self::assertEquals(
            new HandlerTypeAdapter($container->get('app.handler.legacy.named')),
            $registry->getType(LegacyNamedFormHandler::class)
        );
    }

    /**
     * @expectedException \Hostnet\Component\FormHandler\Exception\InvalidHandlerTypeException
     */
    public function testMissing()
    {
        $container = self::$kernel->getContainer();
        $registry  = $container->get('hostnet.form_handler.registry');

        $registry->getType(\stdClass::class);
    }

    /**
     * @expectedException \Hostnet\Component\FormHandler\Exception\InvalidHandlerTypeException
     */
    public function testNotTagged()
    {
        $container = self::$kernel->getContainer();
        $registry  = $container->get('hostnet.form_handler.registry');

        $registry->getType(SimpleNotTaggedFormHandler::class);
    }
}
