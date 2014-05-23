<?php
namespace Hostnet\Bundle\FormHandlerBundle\Service;

use Hostnet\Component\Form\FormInformationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Yannick de Lange <ydelange@hostnet.nl>
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 */
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
     * @param Container $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->handlers  = [];
    }

    /**
     * @param string $service_id
     * @param string $class
     */
    public function addFormClass($service_id, $class)
    {
        $this->handlers[$service_id] = $class;
    }

    /**
     * @see \Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface::apply()
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options    = $configuration->getOptions();
        $service_id = $options['service_id'];
        $handler    = $this->container->get($service_id);
        $class      = $this->handlers[$service_id];

        if (!$handler instanceof FormInformationInterface || get_class($handler) !== $class) {
            return;
        }

        $request->attributes->set($configuration->getName(), $handler);
    }

    /**
     * @see \Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface::supports()
     */
    public function supports(ParamConverter $configuration)
    {
        if (!array_key_exists('service_id', $configuration->getOptions())) {
            throw new \InvalidArgumentException(
                sprintf("No service_id found for parameter converter %s.", $configuration->getName())
            );
        }

        return in_array($configuration->getClass(), $this->handlers);
    }
}