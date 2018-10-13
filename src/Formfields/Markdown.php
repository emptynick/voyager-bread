<?php

namespace Bread\Formfields;

class Markdown extends BaseFormfield
{
    protected $name = "Markdown";
    protected $codename = "markdown";

    public $options = [
        'length'         => null,
        'title'          => '',
        'help_text'      => '',
        'spellcheck'     => false,
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
