<?php

namespace Bread\Formfields;

class Number extends BaseFormfield
{
    protected $name = 'Number';
    protected $codename = 'number';

    public $options = [
        'prefix'         => '',
        'suffix'         => '',
        'decimals'       => 0,
        'dec_point'      => '.',
        'thousands_sep'  => ',',
        'min'            => null,
        'max'            => null,
        'step'           => null,
        'placeholder'    => '',
        'default_value'  => '',
        'title'          => '',
        'help_text'      => '',
    ];

    public function store($input, $model = null)
    {
        if ($this->options['min'] && $this->options['min'] != '') {
            if ($input < $this->options['min']) {
                $input = $this->options['min'];
            }
        }

        if ($this->options['max'] && $this->options['max'] != '') {
            if ($input > $this->options['max']) {
                $input = $this->options['max'];
            }
        }
        $input = number_format((float) $input, $this->options['decimals']);

        return $input;
    }
}
