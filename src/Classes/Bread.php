<?php

namespace Bread\Classes;

class Bread
{
    public $table;
    public $display_name_singular;
    public $display_name_plural;
    public $slug;
    public $model_name;
    public $controller_name;
    public $policy_name;
    public $icon;
    public $layouts = [];

    public function __construct($content)
    {
        foreach ($content as $key => $value) {
            if ($key == 'layouts' && $value) {
                $this->layouts = collect();
                foreach ($value as $layout) {
                    $this->layouts->push(new Layout($layout));
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }
}
