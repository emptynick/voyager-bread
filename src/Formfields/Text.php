<?php

namespace Bread\Formfields;

class Text extends AbstractFormfield
{
    public $element_type = 'formfield';
    protected $codename = 'formfield-text';
    protected $name = 'Text';
    public $options = [
        'label'         => '',
        'placeholder'   => '',
        'help_text'     => '',
        'default_text'  => '',
    ];

    public function getComponent($render = false)
    {
        if ($render) {
            return view('bread::vue.formfields.text');
        } else {
            return 'bread::vue.formfields.text';
        }
    }
}
