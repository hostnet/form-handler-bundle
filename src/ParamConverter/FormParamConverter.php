<?php
namespace Hostnet\Bundle\FormHandlerBundle\ParamConverter;

use Hostnet\Component\Form\FormInformationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @var FormFactoryInterface
     */
    private $form_factory;

    /**
     * @var array
     */
    private $handlers;

    /**
     * @param ContainerInterface   $container
     * @param FormFactoryInterface $form_factory
     */
    public function __construct(ContainerInterface $container, FormFactoryInterface $form_factory)
    {
        $this->container    = $container;
        $this->form_factory = $form_factory;
        $this->handlers     = [];
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
        $service_id = isset($options['service_id'])
            ? $options['service_id']
            : $this->getServiceIdForClassName($configuration);
        $handler    = $this->container->get($service_id);
        $class      = $this->handlers[$service_id];

        if (!$handler instanceof FormInformationInterface || get_class($handler) !== $class) {
            return;
        }

        $form = $this->form_factory->create($handler->getType(), $handler->getData(), $handler->getOptions());

        if (!$form instanceof FormInterface) {
            throw new FormTypeNotFoundException($handler->getType());
        }

        // set the form which is associated with the handler
        $handler->setForm($form);

        $request->attributes->set($configuration->getName(), $handler);
    }

    /**
     * @see \Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface::supports()
     */
    public function supports(ParamConverter $configuration)
    {
        return in_array($configuration->getClass(), $this->handlers);
    }

    /**
     * @param ParamConverter $configuration
     * @return string
     */
    private function getServiceIdForClassName(ParamConverter $configuration)
    {
        $service_ids = [];
        $class       = $configuration->getClass();
        foreach($this->handlers as $service_id => $service_class) {
            if ($class === $service_class) {
                $service_ids[] = $service_id;
            }
        }
        if (count($service_ids) === 0) {
            throw new \InvalidArgumentException(
                    sprintf("No service_id found for parameter converter %s.", $configuration->getName())
            );
        }
        if (count($service_ids) > 1) {
            throw new \InvalidArgumentException(
                    sprintf("More than one service_id found for parameter converter %s.", $configuration->getName())
            );
        }

        return $service_ids[0];
    }
}