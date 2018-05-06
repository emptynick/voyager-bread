<?php

namespace Bread\Formfields;

class Textarea extends AbstractFormfield
{
    protected $codename = 'textarea';
    protected $name = 'Textarea';
    public $options = [
        'label'       => '',
        'value'       => '',
        'placeholder' => '',
        'help_text'   => '',
    ];
}
