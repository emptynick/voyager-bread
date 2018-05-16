<?php

namespace Bread\Formfields;

class Textarea extends AbstractFormfield
{
    public $element_type = 'formfield';
    protected $codename = 'formfield-textarea';
    protected $name = 'Textarea';
    public $options = [
        'label'         => '',
        'help_text'     => '',
        'default_text'  => '',
        'rows'          => 5,
    ];

    public function getComponent($render = false)
    {
        if ($render) {
            return view('bread::vue.formfields.textarea');
        } else {
            return 'bread::vue.formfields.textarea';
        }
    }
}
