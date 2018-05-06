<?php

namespace Bread\Formfields;

class Text extends AbstractFormfield
{
    protected $codename = 'text';
    protected $name = 'Text';
    public $options = [
        'label'       => '',
        'value'       => '',
        'placeholder' => '',
        'help_text'   => '',
    ];
}
