<?php

namespace Bread\Classes;

use Bread\BreadFacade;

class Layout
{
    public $name;
    public $type;
    public $elements = [];

    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            if ($key == 'elements') {
                $this->parseElements($value);
            } else {
                $this->{$key} = $value;
            }
        }
    }

    public function parseElements($elements)
    {
        foreach ($elements as $element) {
            $class = BreadFacade::formfield($element->type);
            if (isset($class)) {
                $new_element = new $class();
                $new_element->setData($element);

                if ($new_element->validate()) {
                    $this->elements[] = $new_element;
                }
            }
        }
    }

    public function validate()
    {
        return
            isset($this->name)
            && isset($this->type);
    }
}
