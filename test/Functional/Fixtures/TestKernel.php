<?php

use Symfony\Component\Config\Loader\LoaderInterface;
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
        $loader->load(__DIR__.'/config/config_27.yml');
//        if (self::VERSION_ID < 20800) {
//        } else {
//            $loader->load(__DIR__.'/config/config.yml');
//        }
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
