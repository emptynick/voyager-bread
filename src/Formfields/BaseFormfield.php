<?php

namespace Bread\Formfields;

abstract class BaseFormfield
{
    public $codename;
    public $name;
    public $group = 'formfield';
    private $field;
    public $options = [];
    private $computed = [];

    public function __construct($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function getComponent()
    {
        return 'bread::formfields.'.$this->codename;
    }
}
