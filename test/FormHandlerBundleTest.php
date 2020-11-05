<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @coversDefaultClass \Hostnet\Bundle\FormHandlerBundle\FormHandlerBundle
 * @covers \Hostnet\Bundle\FormHandlerBundle\FormHandlerBundle
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
