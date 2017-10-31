<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 * @covers \Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler\FormHandlerRegistryCompilerPass
 */
class FormHandlerRegistryCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!interface_exists('Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface')) {
            $this->markTestSkipped(
              'Sensio Extra bundle is not installed.'
            );
        }
    }

    /**
     * @dataProvider processDataProvider
     */
    public function testProcess($tagged_services)
    {
        $container = new ContainerBuilder();
        $container->setDefinition('form_handler.param_converter', new Definition());
        $container->setDefinition('hostnet.form_handler.registry', new Definition(null, [null, null]));

        foreach ($tagged_services as $id => $tag) {
            $container->register($id)->addTag($tag, ['tests']);
        }

        $pass = new FormHandlerRegistryCompilerPass();
        $pass->process($container);
    }

    public function processDataProvider()
    {
        return [
            [[]],
            [['test.service' => 'form.handler', 'test.phpunit' => 'form.handler']]
        ];
    }
}
