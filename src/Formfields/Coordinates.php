<?php

namespace Bread\Formfields;

class Coordinates extends BaseFormfield
{
    protected $name = 'Coordinates';
    protected $codename = 'coordinates';

    public $options = [
        'default_value'  => [],
        'title'          => '',
        'help_text'      => '',
    ];

    public function store($input, $model = null)
    {
        //Join array
        return $input;
    }
}
