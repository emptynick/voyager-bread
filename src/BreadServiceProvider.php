<?php

namespace Bread;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class BreadServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bread');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'bread');

        $this->publishes([
            __DIR__.'/../publishable/assets' => public_path('vendor/bread'),
        ], 'bread_assets');

        $this->loadHelpers();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app(Dispatcher::class)->listen('voyager.admin.routing.after', function ($router) {
            $this->addRoutes($router);
        });

        $this->registerFormFields();

        $this->mergeConfigFrom(
            dirname(__DIR__).'/resources/config/bread.php', 'bread'
        );
    }

    public function addRoutes($router)
    {
        $namespacePrefix = '\\Bread\\Http\\Controllers\\';

        $router->group([
            'as'     => 'bread.',
            'prefix' => 'bread',
        ], function () use ($namespacePrefix, $router) {
            $router->get('/', ['uses' => $namespacePrefix.'BreadManagerController@index',              'as' => 'index']);
            $router->get('{table}/create', ['uses' => $namespacePrefix.'BreadManagerController@create',     'as' => 'create']);
            $router->post('/', ['uses' => $namespacePrefix.'BreadManagerController@store',   'as' => 'store']);
            $router->get('{table}/edit', ['uses' => $namespacePrefix.'BreadManagerController@edit', 'as' => 'edit']);
            $router->put('{id}', ['uses' => $namespacePrefix.'BreadManagerController@update',  'as' => 'update']);
            $router->delete('{id}', ['uses' => $namespacePrefix.'BreadManagerController@destroy',  'as' => 'destroy']);

            $router->get('{table}/edit/layout/{name}', [
                'uses'  => $namespacePrefix.'BreadManagerController@editLayout',
                'as'    => 'edit.layout',
            ]);

            $router->post('{table}/store/layout/{name}', [
                'uses'  => $namespacePrefix.'BreadManagerController@storeLayout',
                'as'    => 'store.layout',
            ]);

            $router->get('/delete/layout/{name}', [
                'uses'  => $namespacePrefix.'BreadManagerController@deleteLayout',
                'as'    => 'delete.layout',
            ]);
        });
    }

    protected function checkPermissions()
    {
        $permission = Permission::firstOrNew([
            'key'        => 'bread',
            'table_name' => 'bread',
        ]);

        if (!$permission->exists) {
            $permission->save();
            $role = Role::where('name', 'admin')->first();
            if (!is_null($role)) {
                $role->permissions()->attach($permission);
            }
        }
    }

    protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    protected function registerFormFields()
    {
        $formFields = [
            \Bread\Formfields\Text::class,
            \Bread\Formfields\Textarea::class,
            \Bread\Formfields\Number::class,
            \Bread\Formfields\Tab::class,
        ];

        foreach ($formFields as $formField) {
            BreadFacade::addFormfield($formField);
        }
    }
}
