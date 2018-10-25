<?php

namespace Bread\Formfields;

class Repeater extends BaseFormfield
{
    protected $name = 'Repeater';
    protected $codename = 'repeater';
    public $group = 'layout';
    public $options = [
        'elements'         => [],
        'label'            => '',
    ];

    public function store($input)
    {
        return json_encode(array_values($input ?? []));
    }
}
