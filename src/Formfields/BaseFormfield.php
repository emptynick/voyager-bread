<?php

namespace Bread\Formfields;

abstract class BaseFormfield
{
    public $codename;
    public $name;
    public $group = 'layout';
    private $field;
    public $options = [];
    private $computed = [];

    public function getComponent()
    {
        return 'bread::formfields.'.$this->codename;
    }
}
