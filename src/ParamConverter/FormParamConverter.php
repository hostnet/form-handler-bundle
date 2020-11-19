<?php
/**
 * @copyright 2014-2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\ParamConverter;

use Hostnet\Component\Form\FormHandlerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class FormParamConverter implements ParamConverterInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $handlers;

    /**
     * @param ContainerInterface $container
     * @param array              $handlers [$service_id => $class]
     */
    public function __construct(ContainerInterface $container, array $handlers = [])
    {
        $this->container = $container;
        $this->handlers  = $handlers;
    }

    /**
     * @param string $service_id
     * @param string $class
     */
    public function addFormClass($service_id, $class)
    {
        @trigger_error(sprintf(
            'Calling %s is deprecated as of 1.6 and will be removed in 2.0. Use the constructor argument instead.',
            __METHOD__
        ), E_USER_DEPRECATED);
        $this->handlers[$service_id] = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options    = $configuration->getOptions();
        $service_id = $options['service_id'] ?? $this->getServiceIdForClassName($configuration);
        $handler    = $this->container->get($service_id);
        $class      = $this->handlers[$service_id];

        if (!$handler instanceof FormHandlerInterface || get_class($handler) !== $class) {
            return;
        }

        $request->attributes->set($configuration->getName(), $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return in_array($configuration->getClass(), $this->handlers, true);
    }

    /**
     * @param ParamConverter $configuration
     * @return string
     */
    private function getServiceIdForClassName(ParamConverter $configuration)
    {
        $service_ids = [];
        $class       = $configuration->getClass();
        foreach ($this->handlers as $service_id => $service_class) {
            if ($class === $service_class) {
                $service_ids[] = $service_id;
            }
        }
        if (count($service_ids) === 0) {
            throw new \InvalidArgumentException(
                sprintf('No service_id found for parameter converter %s.', $configuration->getName())
            );
        }
        if (count($service_ids) > 1) {
            throw new \InvalidArgumentException(
                sprintf('More than one service_id found for parameter converter %s.', $configuration->getName())
            );
        }

        return $service_ids[0];
    }
}
