<?php

namespace Bread\Formfields;

class MaskedInput extends BaseFormfield
{
    protected $name = 'Masked Input';
    protected $codename = 'maskedinput';

    public $options = [
        'length'         => null,
        'placeholder'    => '',
        'default_value'  => '',
        'title'          => '',
        'help_text'      => '',
        'mask'           => '+',
        'mask_char'      => '-',
    ];

    public function store($input)
    {
        return $input;
    }
}
