<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.6.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\TestCase\Core;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
use Cake\TestSuite\TestCase;
use Company\TestPluginThree\Plugin as TestPluginThree;
use TestPlugin\Plugin as TestPlugin;

/**
 * BasePluginTest class
 */
class BasePluginTest extends TestCase
{

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload();
    }

    /**
     * testConfigForRoutesAndBootstrap
     *
     * @return void
     */
    public function testConfigForRoutesAndBootstrap()
    {
        $plugin = new BasePlugin([
            'bootstrap' => false,
            'routes' => false
        ]);

        $this->assertFalse($plugin->isEnabled('routes'));
        $this->assertFalse($plugin->isEnabled('bootstrap'));
        $this->assertTrue($plugin->isEnabled('console'));
        $this->assertTrue($plugin->isEnabled('middleware'));
    }

    public function testGetName()
    {
        $plugin = new TestPlugin();
        $this->assertSame('TestPlugin', $plugin->getName());

        $plugin = new TestPluginThree();
        $this->assertSame('Company/TestPluginThree', $plugin->getName());
    }

    public function testGetNameOption()
    {
        $plugin = new TestPlugin(['name' => 'Elephants']);
        $this->assertSame('Elephants', $plugin->getName());
    }

    public function testMiddleware()
    {
        $plugin = new BasePlugin();
        $middleware = new MiddlewareQueue();
        $this->assertSame($middleware, $plugin->middleware($middleware));
    }

    public function testConsole()
    {
        $plugin = new BasePlugin();
        $commands = new CommandCollection();
        $this->assertSame($commands, $plugin->console($commands));
    }

    public function testBootstrap()
    {
        $app = $this->createMock(PluginApplicationInterface::class);
        $plugin = new TestPlugin();

        $this->assertFalse(Configure::check('PluginTest.test_plugin.bootstrap'));
        $this->assertNull($plugin->bootstrap($app));
        $this->assertTrue(Configure::check('PluginTest.test_plugin.bootstrap'));
    }

    public function testConstructorArguments()
    {
        $plugin = new BasePlugin([
            'routes' => false,
            'bootstrap' => false,
            'console' => false,
            'middleware' => false
        ]);
        $this->assertFalse($plugin->isEnabled('routes'));
        $this->assertFalse($plugin->isEnabled('bootstrap'));
        $this->assertFalse($plugin->isEnabled('console'));
        $this->assertFalse($plugin->isEnabled('middleware'));
    }

    public function testGetPathBaseClass()
    {
        $plugin = new BasePlugin();

        $expected = CAKE . 'Core' . DS;
        $this->assertSame($expected, $plugin->getPath());
        $this->assertSame($expected . 'config' . DS, $plugin->getConfigPath());
        $this->assertSame($expected . 'src' . DS, $plugin->getClassPath());
    }

    public function testGetPathOptionValue()
    {
        $plugin = new BasePlugin(['path' => '/some/path']);
        $expected = '/some/path';
        $this->assertSame($expected, $plugin->getPath());
        $this->assertSame($expected . 'config' . DS, $plugin->getConfigPath());
        $this->assertSame($expected . 'src' . DS, $plugin->getClassPath());
    }

    public function testGetPathSubclass()
    {
        $plugin = new TestPlugin();
        $expected = TEST_APP . 'Plugin/TestPlugin' . DS;
        $this->assertSame($expected, $plugin->getPath());
        $this->assertSame($expected . 'config' . DS, $plugin->getConfigPath());
        $this->assertSame($expected . 'src' . DS, $plugin->getClassPath());
    }
}