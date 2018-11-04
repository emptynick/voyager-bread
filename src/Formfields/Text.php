<?php

namespace Bread\Formfields;

class Text extends BaseFormfield
{
    public $codename = 'text';
    public $name = 'Text';
    public $options = [
        'title'                 => '',
        'help_text'             => '',
        'display_length'        => 50,
        'slug_from'             => '',
    ];
}
