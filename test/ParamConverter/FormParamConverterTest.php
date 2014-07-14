<?php
namespace Hostnet\Bundle\FormHandlerBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 * @coversDefaultClass Hostnet\Bundle\FormHandlerBundle\ParamConverter\FormParamConverter
 */
class FormParamConverterTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $request;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->request   = new Request();
    }

    /**
     * @covers ::__construct
     * @covers ::apply
     * @covers ::supports
     * @covers ::addFormClass
     */
    public function testApplyFromServiceIdHandlerNotFound()
    {
        $configuration = new ParamConverter(['class' => 'Test\Henk', 'options' => ['service_id' => 'test.henk']]);
        $converter     = new FormParamConverter($this->container);
        $this->assertFalse($converter->supports($configuration));
        $configuration = new ParamConverter([
            'class'   => 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            'options' => ['service_id' => 'test.henk']
        ]);

        $converter->addFormClass('test.henk', 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock');
        $this->assertTrue($converter->supports($configuration));

        $converter->apply($this->request, $configuration);
    }

    /**
     * @covers ::__construct
     * @covers ::apply
     * @covers ::supports
     * @covers ::addFormClass
     */
    public function testApplyFromServiceId()
    {
        $converter     = new FormParamConverter($this->container);
        $configuration = new ParamConverter([
            'class'   => 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            'options' => ['service_id' => 'test.henk'],
            'name'    => 'henk'
        ]);

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

    /**
     * @covers ::getServiceIdForClassName
     */
    public function testGetServiceIdForClassName()
    {
        $converter     = new FormParamConverter($this->container);
        $configuration = new ParamConverter([
            'class' => 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            'name'  => 'henk'
        ]);

        $converter->addFormClass('test.henk', 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock');
        $converter->apply($this->request, $configuration);
    }

    /**
     * @covers ::getServiceIdForClassName
     * @expectedException \InvalidArgumentException
     */
    public function testGetServiceIdForClassNameNoMatch()
    {
        $converter     = new FormParamConverter($this->container);
        $configuration = new ParamConverter([
            'class' => 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            'name'  => 'henk'
        ]);

        $converter->apply($this->request, $configuration);
    }

    /**
     * @covers ::getServiceIdForClassName
     * @expectedException \InvalidArgumentException
     */
    public function testGetServiceIdForClassNameTooManyClassesForOneService()
    {
        $converter     = new FormParamConverter($this->container);
        $configuration = new ParamConverter([
            'class' => 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock',
            'name'  => 'henk'
        ]);

        // too many for 1 class to automatically determine the service id by class
        $converter->addFormClass('test.henk', 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock');
        $converter->addFormClass('test.hans', 'Hostnet\Bundle\FormHandlerBundle\ParamConverter\HandlerMock');

        $converter->apply($this->request, $configuration);
    }
}
