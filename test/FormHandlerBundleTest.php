<?php
namespace Hostnet\Bundle\FormHandlerBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Piotr Rzeczkowski <piotr@rzeka.net>
 * @coversDefaultClass Hostnet\Bundle\FormHandlerBundle\FormHandlerBundle
 */
class FormHandlerBundleTest extends \PHPUnit_Framework_TestCase
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
        $bundle = new FormHandlerBundle();

        $bundle->build($container);

    }
}
