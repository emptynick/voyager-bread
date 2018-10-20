<?php

namespace Bread\Formfields;

class Textarea extends Text
{
    protected $name = 'Textarea';
    protected $codename = 'textarea';

    public $options = [
        'length'         => null,
        'placeholder'    => '',
        'default_value'  => '',
        'title'          => '',
        'help_text'      => '',
        'rows'           => 5,
    ];
}
