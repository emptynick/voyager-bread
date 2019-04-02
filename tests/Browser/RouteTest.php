<?php

namespace Bread\Tests\Browser;

use Laravel\Dusk\Browser;
use Orchestra\Testbench\Dusk\TestCase;

class RouteTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['router']->get('hello', ['as' => 'hi', 'uses' => function () {
            return 'hello world';
        }]);
        $app['router']->get('config', ['as' => 'hi', 'uses' => function () {
            return config('new_config_item');
        }]);
    }

    /** @test */
    public function can_use_dusk()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('hello')
                ->assertSee('hello world');
        });
    }

    /** @test */
    public function can_use_multiple_browsers()
    {
        $this->browse(function (Browser $browser, Browser $browserTwo) {
            $browser->visit('hello')
                ->assertSee('hello world');
            $browserTwo->visit('hello')
                ->assertSee('hello world');
        });
    }

    /** @test */
    public function can_tweak_the_application_within_a_test()
    {
        $this->tweakApplication(function ($app) {
            $app['config']->set('new_config_item', 'Fantastic!');
        });
        $this->assertEquals('Fantastic!', config('new_config_item'));
        $this->browse(function (Browser $browser, Browser $browserTwo) {
            $browser->visit('config')
                ->assertSee('Fantastic!');
        });
        $this->removeApplicationTweaks();
    }
}