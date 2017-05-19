<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\Registry;

use Hostnet\Component\Form\FormHandlerInterface;
use Hostnet\Component\FormHandler\Exception\InvalidHandlerTypeException;
use Hostnet\Component\FormHandler\HandlerRegistryInterface;
use Hostnet\Component\FormHandler\HandlerTypeAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class LegacyHandlerRegistry implements HandlerRegistryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array[]
     */
    private $handlers;

    public function __construct(ContainerInterface $container, array $handlers)
    {
        $this->handlers  = $handlers;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($class)
    {
        foreach ($this->handlers as list($service_id, $handler_class)) {
            if ($handler_class !== $class) {
                continue;
            }

            $handler = $this->container->get($service_id);

            if ($handler instanceof FormHandlerInterface) {
                return new HandlerTypeAdapter($handler);
            }

            return $handler;
        }

        throw new InvalidHandlerTypeException($class);
    }
}
