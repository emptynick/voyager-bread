<?php

namespace Bread\Formfields;

class Password extends BaseFormfield
{
    protected $name = "Password";
    protected $codename = "password";

    public $options = [
        'placeholder'    => '',
        'title'          => '',
        'help_text'      => '',
        'keep_password'  => true,
    ];

    public function browse($input)
    {
        return '********';
    }

    public function store($input)
    {
        if ($this->options->keep_password && (!$input || $input == '')) {
            return null; //Returning null will exclude the field from the update-query
        }
        return bcrypt($input ?: '');
    }
}
