<?php

namespace Bread;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

class BreadServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bread');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'bread');
        $this->mergeConfigFrom(
            dirname(__DIR__).'/publishable/config/bread.php', 'bread'
        );

        \View::share('locale', app()->getLocale());
        \View::share('locales', config('voyager.multilingual.locales', []));
    }

    public function register()
    {
        app(Dispatcher::class)->listen('voyager.admin.routing.after', function ($router) {
            $this->addRoutes($router);
        });

        $this->loadHelpers();
        $this->registerFormfields();
    }

    protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    protected function addRoutes($router)
    {
        $namespace = '\\Bread\\Http\\Controllers\\';

        $router->group([
            'as'     => 'bread.',
            'prefix' => 'bread',
        ], function () use ($namespace, $router) {
            //Index
            $router->get('/', [
                'uses' => $namespace.'ManagerController@index',
                'as' => 'index'
            ]);
            //Create
            $router->get('{table}/create', [
                'uses' => $namespace.'ManagerController@create',
                'as'   => 'create',
            ]);
            //Store
            $router->post('/', [
                'uses' => $namespace.'ManagerController@store',
                'as'   => 'store',
            ]);
            //Edit
            $router->get('{table}/edit', [
                'uses' => $namespace.'ManagerController@edit',
                'as'   => 'edit',
            ]);
            //Delete
            $router->delete('{id}', [
                'uses' => $namespace.'ManagerController@destroy',
                'as'   => 'delete',
            ]);
            //Assets
            $router->get('/styles.css', [
                'uses' => $namespace.'AssetController@styles',
                'as'   => 'styles',
            ]);
            $router->get('/scripts.js', [
                'uses' => $namespace.'AssetController@scripts',
                'as'   => 'scripts',
            ]);
        });

        try {

        } catch (\Exception $e) { }
    }

    protected function registerFormfields()
    {
        $formfields = [
            \Bread\Formfields\Text::class
        ];
        foreach ($formfields as $formfield) {
            BreadFacade::addFormfield($formfield);
        }
    }
}
