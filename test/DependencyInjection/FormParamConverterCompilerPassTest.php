<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 * @covers \Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler\FormParamConverterCompilerPass
 */
class FormParamConverterCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessNoDef()
    {
        $container = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(false));

        $container
            ->expects($this->never())
            ->method('getDefinition');

        $pass = new FormParamConverterCompilerPass();
        $pass->process($container);
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

        $pass = new FormParamConverterCompilerPass();
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
