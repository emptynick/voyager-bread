<?php

namespace Bread\Classes;

class Bread
{
    public $name,
           $slug,
           $display_name_singular,
           $display_name_plural,
           $model,
           $controller,
           $policy,
           $icon,
           $layouts = [],
           $browse_list,
           $read_view,
           $edit_view,
           $add_view;

    function __construct($data)
    {
        foreach ($data as $key => $value) {
            if ($key == 'layouts') {
                $this->parseLayouts($value);
            } elseif ($key == 'model' && $value != '') {
                $this->model = app($value);
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function getLists()
    {
        return $this->layouts->where('type', 'list');
    }

    public function getViews()
    {
        return $this->layouts->where('type', 'view');
    }

    public function getLayout($name)
    {
        return $this->layouts->where('name', $name)->first();
    }

    public function parseLayouts($layouts)
    {
        foreach ($layouts as $name => $layout) {
            $layout = new Layout($layout);
            $layout->name = $name;

            if ($layout->validate()) {
                $this->layouts[$layout->name] = $layout;
            }
        }

        $this->layouts = collect($this->layouts);
    }

    public function validate()
    {
        return (
            isset($this->slug)
            && isset($this->display_name_singular)
            && isset($this->display_name_plural)
            && isset($this->model)
        );
    }
}
