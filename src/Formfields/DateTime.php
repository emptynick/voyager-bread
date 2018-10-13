<?php

namespace Bread\Formfields;

class DateTime extends BaseFormfield
{
    protected $name = 'DateTime';
    protected $codename = 'datetime';

    public $options = [
        'field2'         => '',
        'type'           => 'datetime',
        'range'          => false,
        'help_text'      => '',
        'title'          => '',
    ];

    public function store($input)
    {
        if ($this->options->range) {
            return [
                $this->options->field  => '',
                $this->options->field2 => '',
            ];
        } else {
            return $input;
        }
    }
}
