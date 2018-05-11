<?php

namespace Bread\Classes;

class Bread
{
    public $name;
    public $slug;
    public $display_name_singular;
    public $display_name_plural;
    public $model;
    public $controller;
    public $policy;
    public $icon;
    public $layouts;
    public $browse_list;
    public $read_view;
    public $edit_view;
    public $add_view;

    public function __construct($data)
    {
        $this->layouts = collect([]);
        foreach ($data as $key => $value) {
            if ($key == 'layouts') {
                $this->parseLayouts($value);
            } /*elseif ($key == 'model' && $value != '') {
                $this->model = app($value);
            } */else {
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
        foreach ($layouts as $layout) {
            $layout = new Layout($layout);

            if ($layout->validate()) {
                $this->layouts[] = $layout;
            }
        }

        $this->layouts = collect($this->layouts);
    }

    public function getModel()
    {
        return app($this->model);
    }

    public function validate()
    {
        return
            isset($this->slug)
            && isset($this->display_name_singular)
            && isset($this->display_name_plural)
            && isset($this->model);
    }
}
