<?php

namespace Bread\Formfields;

class ColorPicker extends AbstractFormfield
{
    public $element_type = 'formfield';
    protected $codename = 'colorpicker';
    protected $name = 'Color Picker';
    public $options = [
        'palette'       => 'basic',
        'custom_colors' => [],
    ];
}
