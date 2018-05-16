<?php

namespace Bread\Formfields;

class MaskedInput extends AbstractFormfield
{
    public $element_type = 'formfield';
    protected $codename = 'maskedinput';
    protected $name = 'Masked Input';
    public $options = [
        'label'             => '',
        'placeholder'       => '',
        'help_text'         => '',
        'default_value'     => '',
        'mask'              => [],
        'placeholder_char'  => '',
    ];
}
