<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Controller test.
 */
class ControllerTest extends WebTestCase
{
    private KernelBrowser $test_client;

    protected function setUp(): void
    {
        $this->test_client = static::createClient(['config_file' => 'autoconfigure.yml']);
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        return new TestKernel($options);
    }

    public function testActionInterfaceDependencyInjection(): void
    {
        $crawler = $this->test_client->request('GET', '/');

        self::assertSame('test', $crawler->text());
    }
}
