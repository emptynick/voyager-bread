<?php

namespace Bread\Formfields;

class Number extends AbstractFormfield
{
    protected $codename = 'number';
    protected $name = 'Number';
    public $options = [
        'label'       => '',
        'value'       => '',
        'placeholder' => '',
        'help_text'   => '',
    ];
}
