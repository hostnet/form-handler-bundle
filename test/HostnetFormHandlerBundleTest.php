<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle;

use Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler\FormHandlerRegistryCompilerPass;
use Hostnet\Component\Form\FormHandlerInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @coversDefaultClass \Hostnet\Bundle\FormHandlerBundle\HostnetFormHandlerBundle
 * @covers \Hostnet\Bundle\FormHandlerBundle\HostnetFormHandlerBundle
 */
class HostnetFormHandlerBundleTest extends TestCase
{
    public function testBuild()
    {
        $container = new ContainerBuilder();

        $bundle = new HostnetFormHandlerBundle();
        $bundle->build($container);

        $found = false;

        foreach ($container->getCompilerPassConfig()->getBeforeOptimizationPasses() as $pass) {
            if (!$pass instanceof FormHandlerRegistryCompilerPass) {
                continue;
            }

            $found = true;
            break;
        }

        self::assertTrue($found, 'Expected to find a compiler pass instance of the FormParamConverterCompilerPass.');

        if (method_exists($container, 'getAutoconfiguredInstanceof')) {
            $child_definitions = $container->getAutoconfiguredInstanceof();
            self::assertArrayHasKey(FormHandlerInterface::class, $child_definitions);
            self::assertArrayHasKey(HandlerTypeInterface::class, $child_definitions);
            self::assertArrayHasKey('form.handler', $child_definitions[HandlerTypeInterface::class]->getTags());
            self::assertArrayHasKey('form.handler', $child_definitions[FormHandlerInterface::class]->getTags());
        }
    }
}
