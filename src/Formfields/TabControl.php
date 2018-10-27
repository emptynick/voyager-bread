<?php

namespace Bread\Formfields;

class TabControl extends BaseFormfield
{
    protected $name = 'TabControl';
    protected $codename = 'tabcontrol';
    public $group = 'layout';
    public $options = [
        'elements'         => [],
        'tabs'             => [],
        'default_tab'      => 0,
    ];

    public function store($input)
    {
        return json_encode(array_values($input ?? []));
    }
}
