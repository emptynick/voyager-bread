<?php

namespace Bread;

use Bread\FormFields\AbstractFormfield;
use Bread\Classes\Bread as BreadClass;

class Bread
{
    protected $formfields = [];
    protected $breads = [];

    public function addFormfield($handler)
    {
        if (!$handler instanceof AbstractFormfield) {
            $handler = app($handler);
        }
        $this->formfields[$handler->getCodename()] = $handler;

        return $this;
    }

    public function formfield($type)
    {
        return (isset($this->formfields[$type]) ? $this->formfields[$type] : null);
    }

    public function formfields()
    {
        return collect($this->formfields);
    }

    public function getBread($table)
    {
        if (ends_with(config('bread.bread_path'), '/')) {
            $full_path = config('bread.bread_path').$table.'.json';
        } else {
            $full_path = config('bread.bread_path').'/'.$table.'.json';
        }

        if (file_exists($full_path)) {
            $bread = new BreadClass(
                json_decode(
                    file_get_contents($full_path)
                ));
            $bread->name = $table;
            return ($bread->validate() ? $bread : null);
        } else {
            return null;
        }
    }

    public function hasBread($table)
    {
        if (ends_with(config('bread.bread_path'), '/')) {
            $full_path = config('bread.bread_path').$table.'.json';
        } else {
            $full_path = config('bread.bread_path').'/'.$table.'.json';
        }

        return file_exists($full_path);
    }
}
