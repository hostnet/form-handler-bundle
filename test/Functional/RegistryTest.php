<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\FullFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestKernel;
use Hostnet\Component\FormHandler\Exception\InvalidHandlerTypeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class RegistryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        static::bootKernel(['config_file' => 'autoconfigure.yml']);
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
            $container->get(FullFormHandler::class),
            $registry->getType(FullFormHandler::class)
        );
        self::assertSame(
            $container->get(SimpleFormHandler::class),
            $registry->getType(SimpleFormHandler::class)
        );
    }

    public function testMissing(): void
    {
        $container = self::$kernel->getContainer();
        $registry  = $container->get('hostnet.form_handler.registry');

        $this->expectException(InvalidHandlerTypeException::class);

        $registry->getType(\stdClass::class);
    }
}
