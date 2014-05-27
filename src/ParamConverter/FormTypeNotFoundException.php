<?php
namespace Hostnet\Bundle\FormHandlerBundle\ParamConverter;

use Hostnet\Component\Form\FormInformationInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 */
class FormTypeNotFoundException extends \RuntimeException
{
    /**
     * @param mixed $type
     */
    public function __construct($type)
    {
        switch(true) {
            case $type instanceof FormTypeInterface:
                $msg = sprintf('Could not create a form for type "%s"', get_class($type));
                break;
            case is_string($type):
                $msg = sprintf('could not find a form for alias "%s"', $type);
                break;
            default:
                throw new \InvalidArgumentException(sprintf(
                    'Argument expected type of string or an implemented FormTypeInterface. Received: %s',
                    is_object($type) ? get_class($type) : gettype($type)
                ));
        }

        parent::__construct($msg);
    }
}
