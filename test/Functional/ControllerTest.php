<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\Functional;

use Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Controller test.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class ControllerTest extends WebTestCase
{
    private $test_client;

    /**
     * BC for current tests, new tests should get their own config.
     */
    protected function setUp()
    {
        $this->test_client = static::createClient(['config_file' => TestKernel::getLegacyConfigFilename()]);
    }

    protected static function createKernel(array $options = array())
    {
        return new TestKernel($options);
    }

    public function testActionInterfaceDependencyInjection()
    {
        if (Kernel::VERSION_ID < 30300) {
            self::markTestSkipped(sprintf('Symfony version %s not supported by test', Kernel::VERSION));
        }

        if (!interface_exists('Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface')) {
            $this->markTestSkipped('Sensio Extra bundle is not installed.');
        }

        $crawler = $this->test_client->request('GET', '/');

        self::assertSame('test', $crawler->text());
    }
}
