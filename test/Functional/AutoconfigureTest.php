<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\FullFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestData;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;

class AutoconfigureTest extends KernelTestCase
{
    protected function setUp()
    {
        if (Kernel::VERSION_ID < 30300) {
            self::markTestSkipped(sprintf('Symfony version %s not supported by test', Kernel::VERSION));
        }
        static::bootKernel(['config_file' => 'autoconfigure.yml']);
    }

    protected static function createKernel(array $options = array())
    {
        return new TestKernel($options);
    }

    public function testHandlerType()
    {
        $container       = self::$kernel->getContainer();
        $handler_factory = $container->get('hostnet.form_handler.factory');
        $request         = Request::create('/', 'POST', ['test' => ['test' => 'foobar']]);

        $handler = $handler_factory->create(FullFormHandler::class);
        $data    = new TestData();

        $response = $handler->handle($request, $data);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals('http://success.nl/', $response->getTargetUrl());
    }

    /**
     * @group legacy
     * @expectedDeprecation Using %s is deprecated, use Hostnet\Component\FormHandler\HandlerTypeInterface instead.
     */
    public function testFormHandler()
    {
        $container       = self::$kernel->getContainer();
        $handler_factory = $container->get('hostnet.form_handler.factory');
        $request         = Request::create('/', 'POST', ['test' => ['test' => 'foobar']]);

        $handler = $handler_factory->create(LegacyFormHandler::class);
        $data    = new TestData();

        $response = $handler->handle($request, $data);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals('http://success.nl/foobar', $response->getTargetUrl());
    }
}
