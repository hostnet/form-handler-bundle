<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\FullFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleNotTaggedFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyFormVariableOptionsHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyNamedFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestKernel;
use Hostnet\Component\FormHandler\Exception\InvalidHandlerTypeException;
use Hostnet\Component\FormHandler\HandlerTypeAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class RegistryTest extends KernelTestCase
{
    /**
     * BC for current tests, new tests should get their own config.
     */
    protected function setUp(): void
    {
        static::bootKernel(['config_file' => TestKernel::getLegacyConfigFilename()]);
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        return new TestKernel($options);
    }

    public function test(): void
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

    public function testMissing(): void
    {
        $container = self::$kernel->getContainer();
        $registry  = $container->get('hostnet.form_handler.registry');

        $this->expectException(InvalidHandlerTypeException::class);

        $registry->getType(\stdClass::class);
    }

    public function testNotTagged(): void
    {
        $container = self::$kernel->getContainer();
        $registry  = $container->get('hostnet.form_handler.registry');

        $this->expectException(InvalidHandlerTypeException::class);

        $registry->getType(SimpleNotTaggedFormHandler::class);
    }
}
