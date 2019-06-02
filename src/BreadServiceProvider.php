<?php

namespace Bread;

use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;

class BreadServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bread');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'bread');

        Blade::directive('localization', function () {
            return '';
        });
    }

    public function register()
    {
        app(Dispatcher::class)->listen('voyager.admin.routing.after', function ($router) {
            $this->addRoutes($router);
        });

        $loader = AliasLoader::getInstance();
        $loader->alias('Bread', BreadFacade::class);

        $this->app->singleton('BreadDB', function () {
            return DB::connection();
        });
    }

    protected function addRoutes($router)
    {
        $namespace = '\\Bread\\Http\\Controllers\\';

        $router->group(['as' => 'bread.', 'prefix' => 'bread'], function () use ($namespace, $router) {
            //Index
            $router->get('/', ['uses' => $namespace.'ManagerController@index', 'as' => 'index']);
            //Create
            $router->get('{table}/create', ['uses' => $namespace.'ManagerController@create', 'as' => 'create']);
            //Store
            $router->post('/', ['uses' => $namespace.'ManagerController@store', 'as' => 'store']);
            //Edit
            $router->get('{table}/edit', ['uses' => $namespace.'ManagerController@create', 'as' => 'edit']);
            //Delete
            $router->delete('{id}', ['uses' => $namespace.'ManagerController@destroy', 'as' => 'delete']);
            //Assets
            $router->get('/styles.css', ['uses' => $namespace.'AssetController@styles', 'as' => 'styles']);
            $router->get('/scripts.js', ['uses' => $namespace.'AssetController@scripts', 'as' => 'scripts']);
        });
    }
}
