<?php

namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\FullFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\FullFormHandler27;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\SimpleNotTaggedFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyFormVariableOptionsHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\Legacy\LegacyNamedFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestData;
use Hostnet\Component\FormHandler\HandlerTypeAdapter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;

class HandlerTest extends KernelTestCase
{
    protected function setUp()
    {
        static::bootKernel();
    }

    public function testValid()
    {
        if (Kernel::VERSION_ID < 20800) {
            self::markTestSkipped(sprintf('Symfony version %s not supported by test', Kernel::VERSION));
        }

        $container       = self::$kernel->getContainer();
        $handler_factory = $container->get('hostnet.form_handler.factory');
        $request         = Request::create('/', 'POST', ['test' => ['test' => 'foobar']]);

        $handler = $handler_factory->create(FullFormHandler::class);
        $data = new TestData();

        $response = $handler->handle($request, $data);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals('http://success.nl/', $response->getTargetUrl());
    }

    public function testInvalid()
    {
        if (Kernel::VERSION_ID < 20800) {
            self::markTestSkipped(sprintf('Symfony version %s not supported by test', Kernel::VERSION));
        }

        $container       = self::$kernel->getContainer();
        $handler_factory = $container->get('hostnet.form_handler.factory');
        $request         = Request::create('/', 'POST', ['test' => ['test' => null]]);

        $handler = $handler_factory->create(FullFormHandler::class);
        $data = new TestData();

        $response = $handler->handle($request, $data);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals('http://failure.nl/', $response->getTargetUrl());
    }

    /**
     * @group legacy
     */
    public function testValid27()
    {
        if (Kernel::VERSION_ID >= 30000) {
            self::markTestSkipped(sprintf('Symfony version %s not supported by test', Kernel::VERSION));
        }

        $container       = self::$kernel->getContainer();
        $handler_factory = $container->get('hostnet.form_handler.factory');
        $request         = Request::create('/', 'POST', ['test' => ['test' => 'foobar']]);

        $handler = $handler_factory->create(FullFormHandler27::class);
        $data = new TestData();

        $response = $handler->handle($request, $data);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals('http://success.nl/', $response->getTargetUrl());
    }

    /**
     * @group legacy
     */
    public function testInvalid27()
    {
        if (Kernel::VERSION_ID >= 30000) {
            self::markTestSkipped(sprintf('Symfony version %s not supported by test', Kernel::VERSION));
        }

        $container       = self::$kernel->getContainer();
        $handler_factory = $container->get('hostnet.form_handler.factory');
        $request         = Request::create('/', 'POST', ['test' => ['test' => null]]);

        $handler = $handler_factory->create(FullFormHandler27::class);
        $data = new TestData();

        $response = $handler->handle($request, $data);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals('http://failure.nl/', $response->getTargetUrl());
    }
}
