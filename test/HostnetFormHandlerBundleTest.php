<?php
namespace Hostnet\Bundle\FormHandlerBundle;

use Hostnet\Bundle\FormHandlerBundle\DependencyInjection\Compiler\FormParamConverterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 * @coversDefaultClass Hostnet\Bundle\FormHandlerBundle\HostnetFormHandlerBundle
 */
class HostnetFormHandlerBundleTest extends \PHPUnit_Framework_TestCase
{
    private $expected_resources = [
        '/Resources/config/services.yml',
        '/DependencyInjection/Compiler/FormParamConverterCompilerPass.php'
    ];

    private $expected_service_definitions = [
        'form_handler.param_converter',
        'form_handler.provider.simple',
        'hostnet.form_handler.registry',
        'hostnet.form_handler.factory',
    ];

    /**
     * @covers ::build
     */
    public function testBuild()
    {
        $container = new ContainerBuilder();

        $bundle = new HostnetFormHandlerBundle();
        $bundle->build($container);

        $passes = $container->getCompilerPassConfig()->getBeforeOptimizationPasses();
        $this->assertTrue($passes[0] instanceof FormParamConverterCompilerPass);
    }

    /**
     * @covers ::build
     * @dataProvider loadedResourcesProvider
     */
    public function testLoadedResources(BundleInterface $bundle, array $expected_resources)
    {
        $container = new ContainerBuilder();
        $bundle->build($container);

        $resources = $container->getResources();

        foreach ($resources as $resource) {
            /* @var $resource \Symfony\Component\Config\Resource\ResourceInterface */
            $resource_path            = $resource->getResource();
            $expected_resources_count = count($expected_resources);

            for ($i = 0; $i < $expected_resources_count; $i++) {
                if (false !== strpos($resource_path, $expected_resources[$i])) {
                    unset($expected_resources[$i]);
                    break;
                }
            }

            // reset indice
            $expected_resources = array_values($expected_resources);

            // found a resource we didn't expect
            if (count($expected_resources) === $expected_resources_count) {
                $this->fail('Test did not expect resource to be loaded: '. $resource_path);
            }
        }

        $this->assertEmpty($expected_resources, 'Container resource(s) missing: ' . implode(',', $expected_resources));
    }

    /**
     * @return array
     */
    public function loadedResourcesProvider()
    {
        return [
            [new HostnetFormHandlerBundle(), $this->expected_resources]
        ];
    }

    /**
     * @covers ::build
     * @dataProvider loadedServicesProvider
     */
    public function testLoadedServices(BundleInterface $bundle, array $expected_service_definitions)
    {
        $container = new ContainerBuilder();
        $bundle->build($container);

        $definitions = $container->getDefinitions();

        foreach ($definitions as $id => $def) {
            /* @var $def \Symfony\Component\DependencyInjection\Definition */
            $class = $def->getClass();

            $expected_service_definitions_count = count($expected_service_definitions);

            for ($i = 0; $i < $expected_service_definitions_count; $i++) {
                if ($id === $expected_service_definitions[$i]) {
                    unset($expected_service_definitions[$i]);
                    break;
                }
            }

            // reset indice
            $expected_service_definitions = array_values($expected_service_definitions);

            // found a resource we didn't expect
            if (count($expected_service_definitions) === $expected_service_definitions_count) {
                $this->fail('Test did not expect service definition to be loaded: '. $id . ' with class ' . $class);
            }

            // test this later because fixing this failure can lead to the previous
            if (!class_exists($class)) {
                $this->fail(sprintf('Could not load class %s for definition %s', $class, $id));
            }
        }

        $this->assertEmpty(
            $expected_service_definitions,
            'Service definition(s) missing: ' . implode(',', $expected_service_definitions)
        );
    }

    /**
     * @return array
     */
    public function loadedServicesProvider()
    {
        return [
            [new HostnetFormHandlerBundle(), $this->expected_service_definitions]
        ];
    }
}
