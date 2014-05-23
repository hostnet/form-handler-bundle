<?php
namespace Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Yannick de Lange <ydelange@hostnet.nl>
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 */
class FormParameterConverterCompilerPass implements CompilerPassInterface
{
    /**
     * @see \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface::process()
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('form_handler.param_converter')) {
            return;
        }

        $definition = $container->getDefinition(
            'form_handler.param_converter'
        );

        $tagged_services = $container->findTaggedServiceIds(
            'form.handler'
        );

        foreach ($tagged_services as $id => $attributes) {
            $definition->addMethodCall('addFormClass', [$id, $container->getDefinition($id)->getClass()]);
        }
    }
}
