<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\HandlerType\FullFormHandler;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestData;
use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class HandlerTest extends KernelTestCase
{
    /**
     * BC for current tests, new tests should get their own config.
     */
    protected function setUp(): void
    {
        static::bootKernel(['config_file' => 'autoconfigure.yml']);
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        return new TestKernel($options);
    }

    public function testValid(): void
    {
        if (Kernel::VERSION_ID < 20800) {
            self::markTestSkipped(sprintf('Symfony version %s not supported by test', Kernel::VERSION));
        }

        $container       = self::$kernel->getContainer();
        $handler_factory = $container->get('hostnet.form_handler.factory');
        $request         = Request::create('/', 'POST', ['test' => ['test' => 'foobar']]);

        $handler = $handler_factory->create(FullFormHandler::class);
        $data    = new TestData();

        $response = $handler->handle($request, $data);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals('http://success.nl/', $response->getTargetUrl());
    }

    public function testInvalid(): void
    {
        $container       = self::$kernel->getContainer();
        $handler_factory = $container->get('hostnet.form_handler.factory');
        $request         = Request::create('/', 'POST', ['test' => ['test' => null]]);

        $handler = $handler_factory->create(FullFormHandler::class);
        $data    = new TestData();

        $response = $handler->handle($request, $data);

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals('http://failure.nl/', $response->getTargetUrl());
    }
}
