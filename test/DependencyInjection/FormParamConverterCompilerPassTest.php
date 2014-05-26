<?php

namespace Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 * @coversDefaultClass Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler\FormParamConverterCompilerPass
 */
class FormParamConverterCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::process
     */
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
     * @covers ::process
     * @dataProvider processDataProvider
     */
    public function testProcess($tagged_services)
    {
        $container  = new ContainerBuilder();
        $definition = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();

        $container->setDefinition('form_handler.param_converter', $definition);

        foreach ($tagged_services as $id => $tag) {
            $container->register($id)->addTag($tag, ['tests']);
        }


        $pass = new FormParamConverterCompilerPass();
        $pass->process($container);
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [[]],
            [['test.service' => 'form.handler', 'test.phpunit' => 'form.handler']]
        ];
    }
}
