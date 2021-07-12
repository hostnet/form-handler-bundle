<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\ParamConverter;

use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Hostnet\Bundle\FormHandlerBundle\ParamConverter\FormParamConverter
 */
class FormParamConverterTest extends TestCase
{
    private $container;
    private $request;

    protected function setUp(): void
    {
        if (!interface_exists('Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface')) {
            $this->markTestSkipped('Sensio Extra bundle is not installed.');
            return;
        }

        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();
        $this->request   = new Request();
    }

    /**
     * @group legacy
     * @expectedDeprecation Calling %s is deprecated as of 1.6 and will be removed in 2.0. Use %s instead.
     */
    public function testApplyFromServiceIdHandlerNotFound(): void
    {
        $configuration = new ParamConverter(['class' => 'Test\Henk', 'options' => ['service_id' => 'test.henk']]);
        $converter     = new FormParamConverter($this->container);
        $this->assertFalse($converter->supports($configuration));

        $configuration = $this->buildParamConverter(
            'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            ['service_id' => 'test.henk']
        );

        $converter->addFormClass('test.henk', 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock');
        $this->assertTrue($converter->supports($configuration));

        $converter->apply($this->request, $configuration);
    }

    /**
     * @group legacy
     * @expectedDeprecation Calling %s is deprecated as of 1.6 and will be removed in 2.0. Use %s instead.
     */
    public function testApplyFromServiceIdDeprecated(): void
    {
        $converter     = new FormParamConverter($this->container);
        $configuration = $this->buildParamConverter(
            'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            ['service_id' => 'test.henk'],
            'henk'
        );

        $converter->addFormClass('test.henk', 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock');
        $this->assertTrue($converter->supports($configuration));

        $handler = new HandlerMock();

        $this->container
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($handler));

        $converter->apply($this->request, $configuration);

        $this->assertEquals($handler, $this->request->attributes->get('henk'));
    }

    public function testApplyFromServiceId(): void
    {
        $converter = new FormParamConverter($this->container, [
            'test.henk' => 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
        ]);

        $configuration = $this->buildParamConverter(
            'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            ['service_id' => 'test.henk'],
            'henk'
        );

        $this->assertTrue($converter->supports($configuration));

        $handler = new HandlerMock();

        $this->container
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($handler));

        $converter->apply($this->request, $configuration);

        $this->assertEquals($handler, $this->request->attributes->get('henk'));
    }

    /**
     * @group legacy
     * @expectedDeprecation Calling %s is deprecated as of 1.6 and will be removed in 2.0. Use %s instead.
     */
    public function testGetServiceIdForClassName(): void
    {
        $converter     = new FormParamConverter($this->container);
        $configuration = $this->buildParamConverter(
            'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            [],
            'henk'
        );

        $converter->addFormClass('test.henk', 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock');
        $converter->apply($this->request, $configuration);
    }

    public function testGetServiceIdForClassNameNoMatch(): void
    {
        $converter     = new FormParamConverter($this->container);
        $configuration = $this->buildParamConverter(
            'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            [],
            'henk'
        );

        $this->expectException(\InvalidArgumentException::class);

        $converter->apply($this->request, $configuration);
    }

    /**
     * @group legacy
     * @expectedDeprecation Calling %s is deprecated as of 1.6 and will be removed in 2.0. Use %s instead.
     */
    public function testGetServiceIdForClassNameTooManyClassesForOneService(): void
    {
        $converter     = new FormParamConverter($this->container);
        $configuration = $this->buildParamConverter(
            'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            [],
            'henk'
        );

        // too many for 1 class to automatically determine the service id by class
        foreach (['test.henk', 'test.hans'] as $id) {
            $converter->addFormClass($id, 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock');
        }

        $this->expectException(\InvalidArgumentException::class);

        $converter->apply($this->request, $configuration);
    }

    private function buildParamConverter($class, array $options = [], $name = null): ParamConverter
    {
        return new ParamConverter([
            'class'   => $class,
            'options' => $options,
            'name'    => $name,
        ]);
    }
}
