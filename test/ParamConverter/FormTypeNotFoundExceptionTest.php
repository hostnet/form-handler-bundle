<?php
namespace Hostnet\Bundle\FormHandlerBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Iltar van der Berg <ivanderberg@hostnet.nl>
 * @coversDefaultClass Hostnet\Bundle\FormHandlerBundle\ParamConverter\FormTypeNotFoundException
 */
class FormTypeNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructFormTypeInterface()
    {
        $class_name = 'MyTestType';
        $type = $this
            ->getMockBuilder('Symfony\Component\Form\FormTypeInterface')
            ->setMockClassName($class_name)
            ->getMock();

        $e = new FormTypeNotFoundException($type);
        $this->assertContains($class_name, $e->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructString()
    {
        $type = 'test.henk';
        $e    = new FormTypeNotFoundException($type);
        $this->assertContains($type, $e->getMessage());
    }

    /**
     * @covers ::__construct
     * @dataProvider constructExceptionProvider
     * @expectedException \InvalidArgumentException
     */
    public function testConstructException($value)
    {
        new FormTypeNotFoundException($value);
    }

    /**
     * @return array
     */
    public function constructExceptionProvider()
    {
        return [
            [1],
            [false],
            [null],
            [true],
            [50.1],
            [new \stdClass()]
        ];
    }
}
