<?php

namespace Bread\Formfields;

abstract class BaseFormfield
{
    public $codename;
    public $name;
    public $group = 'formfield';
    public $field;
    public $width = 12;
    public $options = [];
    public $validation = [];
    private $computed = [];

    public function __construct($element = null)
    {
        if ($element) {
            foreach ($element as $key => $value) {
                if ($key == 'options') {
                    $this->options = array_merge($this->options, (array)$value);
                } else {
                    $this->{$key} = $value;
                }
            }
        }
    }

    public function getComponent()
    {
        return 'bread::formfields.'.$this->codename;
    }

    public function prepare($bread, $model)
    {
        return $this;
    }

    public function store($input)
    {
        //Translatable fields get a json_encoded string as input
        return $input;
    }
}
