<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class TestKernel extends Kernel
{
    private $config_file;

    public function __construct(array $options)
    {
        $this->config_file = isset($options['config_file']) ? $options['config_file'] : 'config.yml';

        parent::__construct(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): iterable
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Hostnet\Bundle\FormHandlerBundle\HostnetFormHandlerBundle(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__."/config/{$this->config_file}");
    }

    protected function prepareContainer(ContainerBuilder $container): void
    {
        parent::prepareContainer($container);

        $container->findDefinition('hostnet.form_handler.registry')->setPublic(true);
        $container->findDefinition('hostnet.form_handler.factory')->setPublic(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir(): string
    {
        return __DIR__.'/../../../var/cache/'.md5($this->getEnvironment().$this->config_file);
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        return __DIR__.'/../../../var/logs';
    }
}
