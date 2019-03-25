<?php

namespace Bread;

use Bread\Classes\Bread as BreadClass;
use Bread\Traits\Translatable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

class Bread
{
    use Translatable;

    protected $breads;
    protected $breadPath;
    protected $formfields;

    public function getTables()
    {
        return array_diff(
            app('BreadDB')->getDoctrineSchemaManager()->listTableNames(),
            config('voyager.database.tables.hidden', [])
        );
    }

    public function getTableColumns($table)
    {
        return app('BreadDB')->getSchemaBuilder()->getColumnListing($table);
    }

    public function breadPath($path = null)
    {
        if ($path) {
            $this->breadPath = Str::finish($path, '/');
        }

        return $this->breadPath;
    }

    public function getBreads()
    {
        if (!$this->breads) {
            // Cache BREADs
            if (!File::isDirectory($this->breadPath)) {
                File::makeDirectory($this->breadPath);
            }
            $this->breads = collect(File::files($this->breadPath))->transform(function ($bread) {
                return new BreadClass($bread->getPathName());
            })->filter(function ($bread) {
                return $bread->isValid();
            });
        }

        return $this->breads;
    }

    public function getBread($table)
    {
        if (!$this->breads) {
            $this->getBreads();
        }

        return $this->breads->where('table', $table)->first();
    }

    public function getBreadBySlug($slug)
    {
        if (!$this->breads) {
            $this->getBreads();
        }

        return $this->breads->whereTranslation('slug', $slug)->first();
    }

    public function getBreadAccessors($table)
    {
        if ($bread = $this->getBread($table)) {
            return [
                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11,
            ];
        }

        return [];
    }

    public function getBreadRelationships($table)
    {
        if ($bread = $this->getBread($table)) {
            return [
                [
                    'name' => 'irgendwas',
                'fields'   => [
                    'feld_1', 'feld_2',
                ],
                ],
            ];
        }

        return [];
    }

    public function debug($message)
    {
        if (function_exists('debug')) {
            debug($message);
        }
    }

    public function translatable()
    {
        return true;
    }

    public function getLocales()
    {
        return ['de', 'en'];
    }

    public function getLocale()
    {
        return app()->getLocale();
    }

    public function addFormfield($class)
    {
        if (!$this->formfields) {
            $this->formfields = collect();
        }

        $class = new $class();
        $this->formfields->push($class);
    }

    public function formfields()
    {
        return $this->formfields;
    }

    public function formfield($type)
    {
        return $this->formfields->filter(function ($formfield) use ($type) {
            return $formfield->getType() == $type;
        })->first();
    }

    public function getAllRoles()
    {
        return Voyager::model('Role')->all()->transform(function ($role) {
            return $role->only(['name', 'id']);
        });
    }
}
