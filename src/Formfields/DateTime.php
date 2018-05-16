<?php

namespace Bread\Formfields;

class DateTime extends AbstractFormfield
{
    public $element_type = 'formfield';
    protected $codename = 'datetime';
    protected $name = 'DateTime';
    public $options = [
        'type'       => '',
    ];
}
