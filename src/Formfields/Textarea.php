<?php

namespace Bread\Formfields;

class Textarea extends Text
{
    protected $name = "Textarea";
    protected $codename = "textarea";

    public $options = [
        'length'         => null,
        'placeholder'    => '',
        'default_value'  => '',
        'title'          => '',
        'help_text'      => '',
        'rows'           => 5,
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
