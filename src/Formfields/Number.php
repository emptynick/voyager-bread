<?php

namespace Bread\Formfields;

class Number extends AbstractFormfield
{
    public $element_type = 'formfield';
    protected $codename = 'formfield-number';
    protected $name = 'Number';
    public $options = [
        'label'         => '',
        'placeholder'   => '',
        'help_text'     => '',
        'default_value' => '',
        'min'           => '',
        'max'           => '',
        'step'          => '',
    ];

    public function getComponent($render = false)
    {
        if ($render) {
            return view('bread::vue.formfields.number');
        } else {
            return 'bread::vue.formfields.number';
        }
    }
}
