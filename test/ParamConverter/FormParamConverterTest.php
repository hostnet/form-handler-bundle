<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 * @covers \Hostnet\Bundle\FormHandlerBundle\ParamConverter\FormParamConverter
 */
class FormParamConverterTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $request;

    protected function setUp()
    {
        if (!interface_exists('Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface')) {
            $this->markTestSkipped(
              'Sensio Extra bundle is not installed.'
            );
            return;
        }

        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();
        $this->request   = new Request();
    }

    public function testApplyFromServiceIdHandlerNotFound()
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

    public function testApplyFromServiceId()
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

    public function testGetServiceIdForClassName()
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetServiceIdForClassNameNoMatch()
    {
        $converter     = new FormParamConverter($this->container);
        $configuration = $this->buildParamConverter(
            'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            [],
            'henk'
        );

        $converter->apply($this->request, $configuration);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetServiceIdForClassNameTooManyClassesForOneService()
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

        $converter->apply($this->request, $configuration);
    }

    private function buildParamConverter($class, array $options = [], $name = null)
    {
        return  new ParamConverter([
            'class'   => $class,
            'options' => $options,
            'name'    => $name
        ]);
    }
}
