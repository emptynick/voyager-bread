<?php

namespace Bread\Formfields;

class Tags extends BaseFormfield
{
    protected $name = 'Tags';
    protected $codename = 'tags';

    public $options = [
        'placeholder'    => '',
        'title'          => '',
        'help_text'      => '',
        'delimiter'      => ';',
    ];

    public function store($input)
    {
        return $input;
    }
}
