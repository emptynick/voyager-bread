<?php

namespace Bread\Classes;

use Bread\BreadFacade;

class Layout
{
    public $name;
    public $type;
    public $elements;

    public function __construct($content)
    {
        foreach ($content as $key => $value) {
            if ($key == 'elements' && $value) {
                $this->elements = collect();
                foreach ($value as $element) {
                    $class = BreadFacade::formfield($element->type);
                    $formfield = new $class($element->options);
                    $this->elements->put($formfield);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }
}
