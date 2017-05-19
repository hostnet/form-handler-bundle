<?php
/**
 * @copyright 2014-2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle;

use Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler\FormParamConverterCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Yannick de Lange <ydelange@hostnet.nl>
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 */
class FormHandlerBundle extends Bundle
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
        $container->addCompilerPass(new FormParamConverterCompilerPass());
    }
}
