<?php

namespace Bread\Formfields;

class Number extends BaseFormfield
{
    public $type = 'Number';

    public function update($value)
    {
        return number_format(floatval($value), $this->options->decimals ?? 0);
    }

    public function store($value)
    {
        return $this->update($value);
    }
}
