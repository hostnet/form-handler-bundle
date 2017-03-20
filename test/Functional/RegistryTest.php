<?php

namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\FullFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleNotTaggedFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyFormVariableOptionsHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyNamedFormHandler;
use Hostnet\Component\FormHandler\HandlerTypeAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RegistryTest extends KernelTestCase
{
    protected function setUp()
    {
        static::bootKernel();
    }

    public function test()
    {
        $container = self::$kernel->getContainer();
        $registry = $container->get('hostnet.form_handler.registry');

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
        $registry = $container->get('hostnet.form_handler.registry');

        $registry->getType(\stdClass::class);
    }

    /**
     * @expectedException \Hostnet\Component\FormHandler\Exception\InvalidHandlerTypeException
     */
    public function testNotTagged()
    {
        $container = self::$kernel->getContainer();
        $registry = $container->get('hostnet.form_handler.registry');

        $registry->getType(SimpleNotTaggedFormHandler::class);
    }
}
