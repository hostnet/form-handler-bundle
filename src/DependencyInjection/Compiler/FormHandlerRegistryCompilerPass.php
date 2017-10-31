<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Yannick de Lange <ydelange@hostnet.nl>
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 */
class FormHandlerRegistryCompilerPass implements CompilerPassInterface
{
    /**
     * @see \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface::process()
     */
    public function process(ContainerBuilder $container)
    {
        $registerWithParamConverter = false;

        if (interface_exists('Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface') &&
            $container->hasDefinition('form_handler.param_converter')) {
            $definition = $container->getDefinition('form_handler.param_converter');
            $registerWithParamConverter = true;
        }

        $tagged_services = array_keys($container->findTaggedServiceIds('form.handler'));
        $handlers        = [];

        foreach ($tagged_services as $id) {
            $class      = $container->getDefinition($id)->getClass();
            $handlers[] = [$id, $class];

            if ($registerWithParamConverter) {
                $definition->addMethodCall('addFormClass', [$id, $class]);
            }
        }

        // Add handlers to registry
        $container->getDefinition('hostnet.form_handler.registry')->replaceArgument(1, $handlers);
    }
}
