<?php
namespace Hostnet\Bundle\FormHandlerBundle;

use Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler\FormParamConverterCompilerPass;
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
            if (!$pass instanceof FormParamConverterCompilerPass) {
                continue;
            }

            $found = true;
            break;
        }

        self::assertTrue($found, 'Expected to find a compiler pass instance of the FormParamConverterCompilerPass.');
    }
}
