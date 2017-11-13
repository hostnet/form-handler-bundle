<?php
namespace Hostnet\Bundle\FormHandlerBundle;

use Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler\FormHandlerRegistryCompilerPass;
use Hostnet\Component\Form\FormHandlerInterface;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Yannick de Lange <ydelange@hostnet.nl>
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 * @author Hidde Boomsma <hboomsma@hostnet.nl>
 */
class HostnetFormHandlerBundle extends Bundle
{
    /**
     * @see \Symfony\Component\HttpKernel\Bundle\Bundle::build()
     */
    public function build(ContainerBuilder $container)
    {
        // load default services.yml
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/Resources/config'));
        $loader->load('services.yml');

        parent::build($container);
        $container->addCompilerPass(new FormHandlerRegistryCompilerPass());

        // If auto configuring is available, register tags for the Handler Types.
        if (method_exists($container, 'registerForAutoconfiguration')) {
            $container->registerForAutoconfiguration(FormHandlerInterface::class)->addTag('form.handler');
            $container->registerForAutoconfiguration(HandlerTypeInterface::class)->addTag('form.handler');
        }
    }
}
