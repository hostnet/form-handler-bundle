<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Hostnet\Bundle\FormHandlerBundle\HostnetFormHandlerBundle(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if (Kernel::VERSION_ID >= 30300) {
            $loader->load(__DIR__.'/config/config_33.yml');
        } elseif (Kernel::VERSION_ID >= 30000) {
            $loader->load(__DIR__.'/config/config_32.yml');
        } else {
            $loader->load(__DIR__.'/config/config_27.yml');
        }
    }

    protected function prepareContainer(ContainerBuilder $container)
    {
        parent::prepareContainer($container);

        $container->findDefinition('hostnet.form_handler.registry')->setPublic(true);
        $container->findDefinition('hostnet.form_handler.factory')->setPublic(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return __DIR__.'/../../../var/cache/' . $this->getEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return __DIR__.'/../../../var/logs';
    }
}
