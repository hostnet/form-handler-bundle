<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler;

use Hostnet\Component\Form\FormHandlerInterface;
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
        $register_with_param_converter = false;

        if (interface_exists('Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface')
            && $container->hasDefinition('form_handler.param_converter')
        ) {
            $register_with_param_converter = true;
        }

        $tagged_services = array_keys($container->findTaggedServiceIds('form.handler'));
        $handlers        = [];
        $legacy_handlers = [];

        foreach ($tagged_services as $id) {
            $class      = $container->getDefinition($id)->setPublic(true)->getClass();
            $handlers[] = [$id, $class];

            if ($register_with_param_converter && is_a($class, FormHandlerInterface::class, true)) {
                $legacy_handlers[$id] = $class;
            }
        }

        // Add handlers to registry
        $container->getDefinition('hostnet.form_handler.registry')->replaceArgument(1, $handlers);

        if (count($legacy_handlers) > 0) {
            $container->getDefinition('form_handler.param_converter')->replaceArgument(1, $legacy_handlers);
        }
    }
}
