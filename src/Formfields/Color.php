<?php

namespace Bread\Formfields;

class Color extends BaseFormfield
{
    protected $name = 'Color';
    protected $codename = 'color';

    public $options = [
        'palette'        => 'basic',
        'custom_colors'  => [],
        'inline'         => true,
        'help_text'      => '',
        'title'          => '',
    ];
}
