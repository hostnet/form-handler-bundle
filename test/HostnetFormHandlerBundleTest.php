<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle;

use Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler\FormHandlerRegistryCompilerPass;
use Hostnet\Component\Form\FormHandlerInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 * @coversDefaultClass Hostnet\Bundle\FormHandlerBundle\HostnetFormHandlerBundle
 */
class HostnetFormHandlerBundleTest extends \PHPUnit_Framework_TestCase
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
