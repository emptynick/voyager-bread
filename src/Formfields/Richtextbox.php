<?php

namespace Bread\Formfields;

class Richtextbox extends BaseFormfield
{
    protected $name = 'Richtextbox';
    protected $codename = 'richtextbox';

    public $options = [
        'length'         => null,
        'placeholder'    => 'f',
        'default_value'  => '',
        'title'          => '',
        'help_text'      => '',
        'slug_from'      => null,
    ];
}
