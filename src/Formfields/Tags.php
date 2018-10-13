<?php

namespace Bread\Formfields;

class Tags extends BaseFormfield
{
    protected $name = "Tags";
    protected $codename = "tags";

    public $options = [
        'placeholder'    => '',
        'title'          => '',
        'help_text'      => '',
        'delimiter'      => ';',
    ];

    public function browse($input)
    {
        return $input;
    }

    public function store($input)
    {
        return $input;
    }
}
