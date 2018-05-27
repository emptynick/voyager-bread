<?php

namespace Bread\Formfields;

class Password extends AbstractFormfield
{
    public $element_type = 'formfield';
    protected $codename = 'formfield-password';
    protected $name = 'Password';
    public $options = [
        'label'         => '',
        'placeholder'   => '',
        'help_text'     => '',
        'preserve'      => true,
    ];

    public function getComponent($render = false)
    {
        if ($render) {
            return view('bread::vue.formfields.password');
        } else {
            return 'bread::vue.formfields.password';
        }
    }
}
