<?php

namespace Bread;

use Bread\Classes\Bread as BreadClass;

class Bread
{
    protected $formfields;
    protected $breads;

    public function addFormfield($formfield)
    {
        if (gettype($formfield) == 'string') {
            $formfield = app($formfield);
        }
        $this->formfields[$formfield->name] = $formfield;

        return $this;
    }

    public function formfield($type)
    {
        return $this->formfields[$type] ?? null;
    }

    public function formfields()
    {
        return collect($this->formfields);
    }

    public function getBread($slug)
    {
        return $this->getBreads()->where('slug', $slug)->first();
    }

    public function getBreadByTable($table)
    {
        return $this->getBreads()->where('table', $table)->first();
    }

    public function getBreads()
    {
        if (!$this->breads) {
            $this->breads = \Cache::remember('breads', now()->addHours(24), function ()
            {
                $breads = collect();
                $files = scandir(config('bread.bread_path'));
                foreach ($files as $file) {
                    if (ends_with($file, '.json')) {
                        $bread = new BreadClass(
                            json_decode(
                                file_get_contents(config('bread.bread_path').DIRECTORY_SEPARATOR.$file)
                            )
                        );
                        $breads->push($bread);
                    }
                }

                return $breads;
            });
        }

        return $this->breads;
    }

    public function hasBread($slug)
    {
        return !is_null($this->getBread($slug));
    }

    public function hasBreadByTable($table)
    {
        return !is_null($this->getBreadByTable($table));
    }

    public function saveBread($bread)
    {
        $path = config('bread.bread_path').DIRECTORY_SEPARATOR.$bread->table.'.json';
        return file_put_contents($path, json_encode($bread, JSON_PRETTY_PRINT));
    }

    public function deleteBread($table)
    {
        $path = config('bread.bread_path').DIRECTORY_SEPARATOR.$table.'.json';
        if (!file_exists($path)) {
            return false;
        }

        return unlink($path);
    }
}
