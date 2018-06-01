<?php

namespace Bread;

use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;
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
            $router->delete('{id}', ['uses' => $namespacePrefix.'BreadManagerController@destroy',  'as' => 'delete']);

            $router->get('{table}/views', [
                'uses'  => $namespacePrefix.'BreadManagerController@views',
                'as'    => 'views.edit',
            ]);
            $router->post('{table}/views', [
                'uses'  => $namespacePrefix.'BreadManagerController@storeViews',
                'as'    => 'views.store',
            ]);

            $router->get('{table}/lists', [
                'uses'  => $namespacePrefix.'BreadManagerController@lists',
                'as'    => 'lists.edit',
            ]);
            $router->post('{table}/lists', [
                'uses'  => $namespacePrefix.'BreadManagerController@storeLists',
                'as'    => 'lists.store',
            ]);

            $router->post('translation', function (Request $request) {
                if (!isset($request->key)) {
                    return;
                }

                $translation = __($request->key);
                if (is_array($translation)) {
                    return $request->key;
                }

                return $translation;
            })->name('translation');
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
            \Bread\Formfields\Heading::class,
            \Bread\Formfields\Paragraph::class,
            \Bread\Formfields\MaskedInput::class,
            \Bread\Formfields\ColorPicker::class,
            \Bread\Formfields\Password::class,
            \Bread\Formfields\RichTextEditor::class,
        ];

        foreach ($formFields as $formField) {
            BreadFacade::addFormfield($formField);
        }
    }
}
