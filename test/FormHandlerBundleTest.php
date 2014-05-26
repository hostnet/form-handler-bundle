<?php
namespace Hostnet\Bundle\FormHandlerBundle;

use Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler\FormParamConverterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 * @coversDefaultClass Hostnet\Bundle\FormHandlerBundle\FormHandlerBundle
 */
class FormHandlerBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::build
     */
    public function testBuild()
    {
        $container = new ContainerBuilder();

        $bundle = new FormHandlerBundle();
        $bundle->build($container);

        $passes = $container->getCompilerPassConfig()->getBeforeOptimizationPasses();
        $this->assertTrue($passes[0] instanceof FormParamConverterCompilerPass);
    }
}
