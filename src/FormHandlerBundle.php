<?php
/**
 * @copyright 2014-2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @deprecated The FormHandlerBundle is deprecated.
 *             Use Hostnet\Bundle\FormHandlerBundle\HostnetFormHandlerBundle instead.
 */
class FormHandlerBundle extends HostnetFormHandlerBundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        @trigger_error(
            sprintf(
                'The %s class is deprecated. Use the %s class instead.',
                self::class,
                HostnetFormHandlerBundle::class
            ),
            E_USER_DEPRECATED
        );
        parent::build($container);
    }
}
