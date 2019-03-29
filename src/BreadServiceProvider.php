<?php

namespace Bread;

use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use TCG\Voyager\Facades\Voyager;

class BreadServiceProvider extends ServiceProvider
{
    private $breadPath;

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bread');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'bread');

        Blade::directive('localization', function () {
            return 'this.$eventHub.locale = "'.BreadFacade::getLocale().'";
                this.$eventHub.initialLocale = "'.BreadFacade::getLocale().'";
                this.$eventHub.locales = '.json_encode(BreadFacade::getLocales()).';
                this.$eventHub.translatable = '.var_export(BreadFacade::translatable(), true).';
                this.$eventHub.translations = '.$this->getTranslations().';';
        });
    }

    public function register()
    {
        $this->loadBreadsFrom(storage_path('bread'));

        app(Dispatcher::class)->listen('voyager.admin.routing.after', function ($router) {
            $this->addRoutes($router);
        });

        $loader = AliasLoader::getInstance();
        $loader->alias('Bread', BreadFacade::class);

        $this->app->singleton('BreadDB', function () {
            return DB::connection();
        });

        Collection::macro('whereTranslation', function ($field, $query) {
            return $this->filter(function ($bread) use ($field, $query) {
                if (is_object($bread->{$field})) {
                    foreach ($bread->{$field} as $locale) {
                        if ($locale == $query) {
                            return true;
                        }
                    }
                } else {
                    if ($bread->{$field} == $query) {
                        return true;
                    }
                }

                return false;
            });
        });

        BreadFacade::addFormfield(\Bread\Formfields\Text::class);
        BreadFacade::addFormfield(\Bread\Formfields\Number::class);
        BreadFacade::addFormfield(\Bread\Formfields\Color::class);
        BreadFacade::addFormfield(\Bread\Formfields\MaskedInput::class);
    }

    public function loadBreadsFrom($path)
    {
        BreadFacade::breadPath($path);
    }

    public function getTranslations()
    {
        return collect(['bread', 'generic', 'manager'])->flatMap(function ($file) {
            return ['bread::'.($translation = $file) => trans('bread::'.$translation)];
        })->toJson();
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

        //BREADs
        BreadFacade::getBreads()->each(function ($bread) use ($router, $namespace) {
            foreach ((array) $bread->slug as $slug) {
                if ($slug) {
                    $controller = $bread->controller ?? $namespace.'BreadController';
                    $router->resource($slug, $controller);
                    $router->post($slug.'/data', $controller.'@getData')->name($slug.'.data');
                }
            }
        });
    }
}
