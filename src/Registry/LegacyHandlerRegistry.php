<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\Registry;

use Hostnet\Component\Form\FormHandlerInterface;
use Hostnet\Component\FormHandler\Exception\InvalidHandlerTypeException;
use Hostnet\Component\FormHandler\HandlerRegistryInterface;
use Hostnet\Component\FormHandler\HandlerTypeAdapter;
use Hostnet\Component\FormHandler\HandlerTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class LegacyHandlerRegistry implements HandlerRegistryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var HandlerTypeInterface[]
     */
    private iterable $handlers;

    public function __construct(ContainerInterface $container, iterable $handlers)
    {
        $this->handlers  = $handlers;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($class)
    {
        foreach ($this->handlers as $handler) {
            if ($handler::class !== $class) {
                continue;
            }

            if ($handler instanceof FormHandlerInterface) {
                return new HandlerTypeAdapter($handler);
            }

            return $handler;
        }

        throw new InvalidHandlerTypeException($class);
    }
}
