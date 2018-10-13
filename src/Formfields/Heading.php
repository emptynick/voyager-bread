<?php

namespace Bread\Formfields;

class Heading extends BaseFormfield
{
    protected $name = 'Heading';
    protected $codename = 'heading';
    public $group = 'layout';
    public $options = [
        'text'         => '',
        'size'         => 'h2',
    ];
}
