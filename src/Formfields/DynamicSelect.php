<?php

namespace Bread\Formfields;

class DynamicSelect extends BaseFormfield
{
    protected $name = 'Dynamic Select';
    protected $codename = 'dynamicselect';
    public $options = [
        'title'     => '',
        'help_text' => '',
        'multiple'  => false,
        'method'    => '', //The method that is called
        'arguments' => [], //The arguments that get passed
        'fields'    => [], //The fields that trigger a reload
    ];
}
