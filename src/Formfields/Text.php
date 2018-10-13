<?php

namespace Bread\Formfields;

class Text extends BaseFormfield
{
    protected $name = 'Text';
    protected $codename = 'text';

    public $options = [
        'length'         => null,
        'placeholder'    => '',
        'default_value'  => '',
        'title'          => '',
        'help_text'      => '',
        'slug_from'      => null,
    ];

    public function browse($input)
    {
        return substr($input, 0, $this->options['length'] ?: 50);
    }

    public function store($input)
    {
        return $input;
    }
}
