<?php

namespace Bread\Formfields;

class Select extends BaseFormfield
{
    protected $name = 'Select';
    protected $codename = 'select';
    public $options = [
        'title'     => '',
        'help_text' => '',
        'multiple'  => false,
        'options'   => [],
    ];

    public function store($input)
    {
        if ($this->options['multiple']) {
            return $input ? json_encode($input) : '[]';
        }
        return $input ?? '';
    }
}
