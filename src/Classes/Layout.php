<?php

namespace Bread\Classes;

use Bread\BreadFacade;

class Layout
{
    public $name;
    public $type;
    public $elements = [];
    public $browse_roles = [];
    public $read_roles = [];
    public $edit_roles = [];
    public $add_roles = [];

    public function __construct($data, $bread)
    {
        foreach ($data as $key => $value) {
            if ($key == 'elements') {
                $this->parseElements($value, $bread);
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function parseElements($elements, $bread)
    {
        foreach ($elements as $element) {
            $formfield = BreadFacade::formfield($element->type);
            if (isset($formfield)) {
                $new_element = new $formfield();
                $new_element->setData($element);
                $this->elements[] = $new_element;
            }
        }
        $this->elements = collect($this->elements);
    }

    public function validate()
    {
        return isset($this->name) && isset($this->type);
    }

    //Returns a collection of all used Vue-components in this layouts
    public function getComponents($action)
    {
        return $this->elements->unique(function ($item) use ($action) {
            return $item->getComponent($action);
        });
    }

    public function translate()
    {
        $this->elements = $this->elements->map(function($element) {
            $element->options = $element->options->map(function($option) {
                if ($option && is_string($option) && starts_with($option, '__')) {
                    $option = __(str_after($option, '__'));
                }
                return $option;
            });
            return $element;
        });
        return $this;
    }
}
