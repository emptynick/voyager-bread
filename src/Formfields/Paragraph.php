<?php

namespace Bread\Formfields;

class Paragraph extends AbstractFormfield
{
    public $element_type = 'layout_element';
    protected $codename = 'paragraph';
    protected $name = 'Paragraph';
    public $options = [
        'text'       => '',
        'size'       => '12',
        'align'      => 'left',
        'color'      => '',
    ];
}
