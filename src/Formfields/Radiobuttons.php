<?php

namespace Bread\Formfields;

class Radiobuttons extends BaseFormfield
{
    protected $name = 'Radio-Buttons';
    protected $codename = 'radiobuttons';
    public $options = [
        'title'     => '',
        'help_text' => '',
        'options'   => [],
    ];
}
