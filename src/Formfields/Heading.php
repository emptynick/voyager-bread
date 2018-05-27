<?php

namespace Bread\Formfields;

class Heading extends AbstractFormfield
{
    public $element_type = 'layout_element';
    protected $codename = 'heading';
    protected $name = 'Heading';
    public $options = [
        'text'       => '',
        'size'       => 'h2',
        'align'      => 'left',
        'color'      => '',
    ];
}
