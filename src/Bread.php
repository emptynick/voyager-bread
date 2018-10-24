<?php

namespace Bread;

use Bread\Classes\Bread as BreadClass;

class Bread
{
    protected $formfields = [];
    protected $breads = [];

    public function addFormfield($formfield)
    {
        if (gettype($formfield) == 'string') {
            $formfield = app($formfield);
        }
        $this->formfields[$formfield->getCodename()] = $formfield;
        return $this;
    }

    public function formfield($type)
    {
        return isset($this->formfields[$type]) ? $this->formfields[$type] : null;
    }

    public function formfields()
    {
        return collect($this->formfields);
    }

    public function getBread($slug)
    {
        if (count($this->breads) == 0) {
            $this->breads = $this->getBreads();
        }

        return $this->breads->filter(function ($bread) use ($slug) {
            $slugs = get_translated_values($bread->slug, true);
            if (is_string($slugs)) {
                return $slugs == $slug;
            } else {
                return $slugs->contains($slug);
            }

            return false;
        })->first();
    }

    public function getBreadByTable($table)
    {
        if (count($this->breads) == 0) {
            $this->breads = $this->getBreads();
        }

        return $this->breads->where('table_name', $table)->first();
    }

    public function getBreads()
    {
        $breads = collect();
        $files = scandir(config('bread.bread_path'));
        foreach ($files as $bread) {
            if (ends_with($bread, '.json')) {
                $bread = new BreadClass(
                    json_decode(
                        file_get_contents(config('bread.bread_path').DIRECTORY_SEPARATOR.$bread)
                    ));
                if ($bread->validate()) {
                    $breads[] = $bread;
                }
            }
        }

        return $breads;
    }

    public function hasBread($slug)
    {
        return count($this->getBread($slug)) > 0;
    }

    public function hasBreadByTable($table)
    {
        return count($this->getBreadByTable($table)) > 0;
    }

    public function saveBread($table, $content)
    {
        if (!is_dir(config('bread.bread_path'))) {
            mkdir(config('bread.bread_path'));
        }
        if (ends_with(config('bread.bread_path'), '/')) {
            $full_path = config('bread.bread_path').$table.'.json';
        } else {
            $full_path = config('bread.bread_path').'/'.$table.'.json';
        }

        return file_put_contents($full_path, json_encode($content, JSON_PRETTY_PRINT));
    }

    public function deleteBread($table)
    {
        if (!is_dir(config('bread.bread_path'))) {
            mkdir(config('bread.bread_path'));
        }
        if (ends_with(config('bread.bread_path'), '/')) {
            $full_path = config('bread.bread_path').$table.'.json';
        } else {
            $full_path = config('bread.bread_path').'/'.$table.'.json';
        }
        if (!file_exists($full_path)) {
            return false;
        }

        return unlink($full_path);
    }
}
