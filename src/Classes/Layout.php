<?php

namespace Bread\Classes;

use Bread\BreadFacade;

class Layout
{
    public $name;
    public $type;
    public $elements;
    public $browse_roles = [];
    public $edit_roles = [];
    public $add_roles = [];

    public function __construct($content)
    {
        foreach ($content as $key => $value) {
            if ($key == 'elements' && $value) {
                $this->elements = collect();
                foreach ($value as $element) {
                    $class = BreadFacade::formfield($element->type);
                    $formfield = new $class($element->options);
                    $this->elements->push($formfield);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }
}
