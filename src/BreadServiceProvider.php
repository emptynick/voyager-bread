<?php

namespace Bread;

use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
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
            $router->get('getTranslation/{key?}', function($key = null) {
                return __($key);
            })->name('getTranslation');
            $router->get('/', [
                'uses' => $namespace.'ManagerController@index',
                'as' => 'index'
            ]);
            $router->get('{table}/create', [
                'uses' => $namespace.'ManagerController@create',
                'as' => 'create'
            ]);
            $router->post('/', [
                'uses' => $namespace.'ManagerController@store',
                'as' => 'store'
            ]);
            $router->get('{table}/edit', [
                'uses' => $namespace.'ManagerController@edit',
                'as' => 'edit'
            ]);
            $router->delete('{id}', [
                'uses' => $namespace.'ManagerController@destroy',
                'as' => 'delete'
            ]);
            $router->get('{table}/views/{name?}', [
                'uses'  => $namespace.'ManagerController@views',
                'as'    => 'views.edit',
            ]);
            $router->get('{table}/lists/{name?}', [
                'uses'  => $namespace.'ManagerController@lists',
                'as'    => 'lists.edit',
            ]);
            $router->post('{table}/storelayouts', [
                'uses'  => $namespace.'ManagerController@storeLayouts',
                'as'    => 'storelayouts',
            ]);

            //Assets
            $router->get('/styles.css', [
                'uses' => $namespace.'AssetController@styles',
                'as' => 'styles'
            ]);
            $router->get('/scripts.js', [
                'uses' => $namespace.'AssetController@scripts',
                'as' => 'scripts'
            ]);
        });

        try {
            foreach (BreadFacade::getBreads(true) as $bread) {
                $router->resource($bread->slug, $bread->controller ?: '\Bread\Http\Controllers\BreadController');
                $router->get($bread->slug.'/data/get', ($bread->controller ?: '\Bread\Http\Controllers\BreadController').'@data')->name($bread->slug.'.data');
            }
        } catch (\Exception $e) { }
    }

    protected function registerFormfields()
    {
        $formfields = [
            \Bread\Formfields\Checkboxes::class,
            \Bread\Formfields\Color::class,
            \Bread\Formfields\Coordinates::class,
            \Bread\Formfields\DateTime::class,
            \Bread\Formfields\Heading::class,
            \Bread\Formfields\Markdown::class,
            \Bread\Formfields\MaskedInput::class,
            \Bread\Formfields\Number::class,
            \Bread\Formfields\Paragraph::class,
            \Bread\Formfields\Password::class,
            \Bread\Formfields\RadioButtons::class,
            \Bread\Formfields\Richtextbox::class,
            \Bread\Formfields\Select::class,
            \Bread\Formfields\DynamicSelect::class,
            \Bread\Formfields\Tags::class,
            \Bread\Formfields\Text::class,
            \Bread\Formfields\Textarea::class,
            /*\Bread\Formfields\MaskedInput::class,
            \Bread\Formfields\RichTextEditor::class,*/

            \Bread\Formfields\Relationships\HasOne::class,
            \Bread\Formfields\Relationships\HasMany::class,
            \Bread\Formfields\Relationships\BelongsTo::class,
            \Bread\Formfields\Relationships\BelongsToMany::class,
        ];
        foreach ($formfields as $formfield) {
            BreadFacade::addFormfield($formfield);
        }
    }
}
