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
        $this->loadMigrationsFrom(__DIR__.'/../resources/database/migrations', 'bread');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'bread');

        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/bread'),
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

        app(Dispatcher::class)->listen('voyager.menu.display', function ($menu) {
            $this->addMenuItem($menu);
        });

        $this->registerFormFields();

        $this->commands([
            Commands\DummyDataCommand::class,
        ]);
    }

    public function addMenuItem(Menu $menu)
    {
        if ($menu->name == 'admin') {
            $toolsItem = $menu->items->where('title', 'Tools')->first();
            $menuItem = $menu->items->where('route', 'voyager.bread.index')->first();
            if (is_null($menuItem)) {
                $indexMenuItem = MenuItem::create([
                    'menu_id'    => $menu->id,
                    'route'      => 'voyager.bread.index',
                    'url'        => '',
                    'title'      => 'BREAD',
                    'target'     => '_self',
                    'icon_class' => 'voyager-bread',
                    'color'      => null,
                    'parent_id'  => $toolsItem->id,
                    'order'      => 99,
                ]);

                $menu->items->add($indexMenuItem);

                $this->checkPermissions();
            }

            return $menu;
        }
    }

    public function addRoutes($router)
    {
        $namespacePrefix = '\\Bread\\Http\\Controllers\\';
        if (starts_with(Voyager::getVersion(), '1.0')) {
            $router->resource('bread', $namespacePrefix.'BreadManagerController', ['except' => ['show', 'create']]);
        } else {
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
            });
        }

        $router->group([
            'as'    => 'bread.',
            'prefix'=> 'bread',
        ], function () use ($namespacePrefix, $router) {
            if (starts_with(Voyager::getVersion(), '1.0')) {
                $router->get('/create/{table}', [
                    'uses'  => $namespacePrefix.'BreadManagerController@create',
                    'as'    => 'create',
                ]);
            }

            $router->post('/store/view', [
                'uses'  => $namespacePrefix.'BreadManagerController@storeView',
                'as'    => 'store.view',
            ]);
            //Post
            $router->get('/edit/view/{view}', [
                'uses'  => $namespacePrefix.'BreadManagerController@editView',
                'as'    => 'edit.view',
            ]);
            $router->post('/update/view', [
                'uses'  => $namespacePrefix.'BreadManagerController@updateView',
                'as'    => 'update.view',
            ]);
            //Post
            $router->post('/update/list/{list}', [
                'uses'  => $namespacePrefix.'BreadManagerController@updateList',
                'as'    => 'update.list',
            ]);
            $router->get('/delete/view/{view}', [
                'uses'  => $namespacePrefix.'BreadManagerController@deleteView',
                'as'    => 'delete.view',
            ]);

            $router->post('/renderformfield', [
                'uses'  => $namespacePrefix.'BreadManagerController@renderFormfield',
                'as'    => 'render.formfield',
            ]);
        });

        foreach (BreadFacade::model('Bread')->all() as $bread) {
            $breadController = $bread->controller
                         ? $bread->controller
                         : $namespacePrefix.'BreadController';

            $router->resource($bread->slug, $breadController);
            $router->post($bread->slug.'/data/{breadView?}/{breadRow?}', ['uses' => $breadController.'@data', 'as' => $bread->slug.'.data']);
            $router->post($bread->slug.'/delete/{id?}', ['uses' => $breadController.'@destroy', 'as' => $bread->slug.'.delete']);
            $router->get($bread->slug.'/create/{view?}', ['uses' => $breadController.'@create', 'as' => $bread->slug.'.create']);
            $router->post($bread->slug.'/store/{view?}', ['uses' => $breadController.'@store', 'as' => $bread->slug.'.store']);
        }
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
            \Bread\FormFields\NumberHandler::class,
            \Bread\FormFields\SelectDropdownHandler::class,
            \Bread\FormFields\TextAreaHandler::class,
            \Bread\FormFields\TextHandler::class,
            \Bread\FormFields\HeadingHandler::class,
            \Bread\FormFields\ParagraphHandler::class,
            \Bread\FormFields\HorizontalRuleHandler::class,
            \Bread\FormFields\TabControlHandler::class,
            \Bread\FormFields\CheckboxHandler::class,
            \Bread\FormFields\ColorHandler::class,
            \Bread\FormFields\DateTimeHandler::class,
        ];
        foreach ($formFields as $formField) {
            BreadFacade::addFormField($formField);
        }
    }
}
