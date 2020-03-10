<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Piotr Rzeczkowski <piotr@rzeka.net>
 * @coversDefaultClass \Hostnet\Bundle\FormHandlerBundle\FormHandlerBundle
 */
class FormHandlerBundleTest extends TestCase
{
    public function testIsInstanceOfFormHandlerBundle()
    {
        $bundle = new FormHandlerBundle();

        $this->assertTrue($bundle instanceof HostnetFormHandlerBundle);
    }

    /**
     * @group legacy
     * @expectedDeprecation The %s is deprecated. Use %s instead.
     */
    public function testIsDeprecated()
    {
        $container = new ContainerBuilder();
        $bundle    = new FormHandlerBundle();

        $bundle->build($container);
    }
}
